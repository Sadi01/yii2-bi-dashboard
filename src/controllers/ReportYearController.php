<?php

namespace sadi01\bidashboard\controllers;

use sadi01\bidashboard\models\ReportYear;
use sadi01\bidashboard\models\ReportYearSearch;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use Yii;
use Yii\base\ExitException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ReportYearController implements the CRUD actions for ReportYear model.
 */
class ReportYearController extends Controller
{
    use AjaxValidationTrait;

    public $layout = 'bid_main';

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
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
                        'index' => ['GET'],
                        'view' => ['GET'],
                        'create' => ['GET', 'POST'],
                        'update' => ['GET', 'PUT', 'POST'],
                        'delete' => ['POST', 'DELETE'],
                    ],
                ],
            ]
        );
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        Yii::$app->controller->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Lists all ReportYear models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ReportYearSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReportYear model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ReportYear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws ExitException
     */
    public function actionCreate(): Response|string
    {
        $model = new ReportYear();

        if ($model->load($this->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->asJson([
                    'status' => true,
                    'message' => Yii::t("app", 'Success')
                ]);
            } else {
                return $this->asJson([
                    'status' => false,
                    'message' => Yii::t("app", 'Fail in Save')
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReportYear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ExitException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->asJson([
                    'status' => true,
                    'message' => Yii::t("app", 'Success')
                ]);
            } else {
                return $this->asJson([
                    'status' => false,
                    'message' => Yii::t("app", 'Fail in Save')
                ]);
            }
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ReportYear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        if ($model->canDelete() && $model->softDelete()) {
            return $this->asJson([
                'status' => true,
                'message' => Yii::t("biDashboard", 'Item Deleted')
            ]);
        } else {
            return $this->asJson([
                'status' => false,
                'message' => Yii::t("biDashboard", 'Error In Delete Action')
            ]);
        }
    }

    /**
     * Finds the ReportYear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return ReportYear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): ReportYear
    {
        if (($model = ReportYear::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}