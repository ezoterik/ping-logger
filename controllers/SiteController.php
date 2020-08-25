<?php

namespace app\controllers;

use app\models\form\LoginForm;
use app\models\Group;
use app\models\Log;
use app\models\PingObject;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'get-monitor-data', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'get-monitor-data' => ['get'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetMonitorData()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $objects = PingObject::find()
            ->orderBy(['status' => SORT_ASC, 'avg_rtt' => SORT_DESC])
            ->asArray()
            ->all();

        $lastErrorEventsDates = (new Query())
            ->select(['object_id', 'MAX(created_at) AS last_error_event'])
            ->from(Log::tableName())
            ->where(['event_num' => Log::EVENT_ERROR])
            ->groupBy('object_id')
            ->indexBy('object_id')
            ->all();

        foreach ($objects as &$object) {
            if (isset($lastErrorEventsDates[$object['id']])) {
                $object['lastErrorEventDate'] = Yii::$app->formatter->asDatetime($lastErrorEventsDates[$object['id']]['last_error_event'], 'php:c');
            }

            //Генерация случайного статуса для тестирования
            //$object['status'] = array_rand(Object::$statuses);
            //$object['avg_rtt'] = rand(5, 20);
        }
        unset($object, $lastErrorEventsDates);

        $groups = Group::find()
            ->orderBy('name')
            ->indexBy('id')
            ->asArray()
            ->all();

        //Распределяем объекты по группам
        foreach ($objects as $object) {
            $groups[$object['group_id']]['objects'][] = $object;
        }
        unset($objects);

        //Сбрасываем индексы на обычные
        $groups = array_values($groups);

        return ['groups' => $groups];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
