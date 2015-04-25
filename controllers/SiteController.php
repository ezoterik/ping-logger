<?php

namespace app\controllers;

use app\models\Log;
use app\models\Group;
use app\models\Object;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\models\LoginForm;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'get-monitor-data', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'get-monitor-data' => ['get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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

        //TODO: можно пробовать использовать механизм связей
        $objects = Object::find()
            ->asArray()
            ->all();

        $lastErrorEventsDates = (new Query())
            ->select('object_id, MAX(created) AS last_error_event')
            ->from('logs')
            ->where(['event_num' => Log::EVENT_ERROR])
            ->groupBy('object_id')
            ->indexBy('object_id')
            ->all();

        foreach ($objects as &$object) {
            if (isset($lastErrorEventsDates[$object['id']])) {
                $object['lastErrorEventDate'] = $lastErrorEventsDates[$object['id']]['last_error_event'];
            }

            //Генерация случайного статуса для тестирования
            //$object['status'] = array_rand(Object::$statuses);
        }
        unset($object, $lastErrorEventsDates);

        $groups = Group::find()
            ->orderBy('name')
            ->indexBy('id')
            ->asArray()
            ->all();

        //Распределяем объекты по группам
        foreach ($objects as $object) {
            $groups[$object['type_id']]['objects'][] = $object;
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
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
