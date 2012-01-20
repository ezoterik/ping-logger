<?php

namespace app\controllers;

use app\models\Log;
use app\models\Group;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\models\LoginForm;

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
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
        //TODO: более оптимально вынуть все объекты, а уже потом группы к ним
        $groups = Group::find()->with('objects')->orderBy('name')->all();

        $lastErrorEventsDates = (new Query())
            ->select('object_id, MAX(created) AS last_error_event')
            ->from('logs')
            ->where(['event_num' => Log::EVENT_ERROR])
            ->groupBy('object_id')
            ->indexBy('object_id')
            ->all();

        return $this->render('index', [
            'groups' => $groups,
            'lastErrorEventsDates' => $lastErrorEventsDates,
        ]);
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
