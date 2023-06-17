<?php

namespace sadi01\bidashboard\controllers;

use sadi01\bidashboard\components\Pdate;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\models\ReportWidgetResult;
use sadi01\bidashboard\models\ReportWidgetSearch;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use sadi01\bidashboard\traits\CoreTrait;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ReportWidgetController implements the CRUD actions for ReportWidget model.
 */
class ReportWidgetController extends Controller
{
    use AjaxValidationTrait;
    use CoreTrait;

    public $layout = 'bid_main';

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ReportWidget models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReportWidgetSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReportWidget model.
     * @param int $id ID
     * @param string $method
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $method = null)
    {
        $model = $this->findModel($id);

        $modelRoute = $model->getModelRoute();

        $runWidget = ReportWidgetResult::find()
            ->where(['widget_id' => $model->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (!$runWidget || $method == 'new') {
            $runWidget = $model->runWidget($id, null, null);
        }

        return $this->render('view', [
            'model' => $model,
            'modelRoute' => $modelRoute,
            'runWidget' => $runWidget,
        ]);
    }

    /**
     * Creates a new ReportWidget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($searchModelClass=null,
                                 $searchModelMethod=null,
                                 $searchModelRunResultView=null,
                                 $search_route=null,
                                 $search_model_form_name=null,
                                 $queryParams=null,$output_column=null)
    {

        $model = new ReportWidget();
        $model->loadDefaultValues();
        if ($this->request->isPost) {
            $model->search_model_method = $searchModelMethod;
            $model->search_route = $search_route;
            $model->search_model_class = $searchModelClass;
            $model->search_model_form_name = $search_model_form_name;
            $model->search_model_run_result_view = $searchModelRunResultView;
            $model->params = $queryParams;

            $output_column = $this->request->post('output_column',null);
            $model->outputColumn = json_encode(array_filter($output_column, fn($value) => array_filter($value)));

            if ($model->load($this->request->post()) && $model->validate()) {
                $model->save(false);
                return $this->asJson([
                    'success' => true,
                    'msg' => Yii::t('biDashboard', 'Saved successfully'),
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'msg' => Yii::t('biDashboard', 'There was a problem saving'),
                ]);
            }
        } else {
            $model->loadDefaultValues();
        }
        $this->performAjaxValidation($model);

        return $this->renderAjax('create', [
            'model' => $model,
            'queryParams' => $queryParams,
            'output_column' => $output_column,
        ]);
    }

    /**
     * Updates an existing ReportWidget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ReportWidget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->softDelete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReportWidget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ReportWidget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportWidget::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('biDashboard', 'The requested page does not exist.'));
    }

    public function actionOpenModal()
    {
        $searchModel = new ReportWidgetSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->renderAjax('list-widget', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}