<?php

namespace app\controllers;

use app\models\Log;
use app\models\PingObject;
use app\models\search\PingObjectSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ObjectController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-logs' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PingObjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView(int $id)
    {
        $object = $this->findModel($id);

        return $this->render('view', [
            'model' => $object,
            'logs' => $object->getLogs()->limit(100)->all(),
        ]);
    }

    public function actionCreate()
    {
        $model = new PingObject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Объект успешно создан');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Объект успешно изменен');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        $object = $this->findModel($id);

        $object->delete();

        Yii::$app->getSession()->setFlash('success', 'Объект успешно удален');

        return $this->redirect(['index']);
    }

    public function actionDeleteLogs(int $id)
    {
        $object = $this->findModel($id);

        Log::deleteAll(['object_id' => $object->id]);

        Yii::$app->getSession()->setFlash('success', 'История успешно очищена');

        return $this->redirect(['view', 'id' => $object->id]);
    }

    protected function findModel($id): PingObject
    {
        if (($model = PingObject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
