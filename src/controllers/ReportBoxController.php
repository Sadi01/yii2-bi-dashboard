<?php

namespace sadi01\bidashboard\controllers;

use sadi01\bidashboard\models\ReportBaseModel;
use sadi01\bidashboard\models\ReportBox;
use sadi01\bidashboard\models\ReportBoxWidgets;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use sadi01\bidashboard\traits\CoreTrait;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class ReportBoxController extends Controller
{
    use AjaxValidationTrait;
    use CoreTrait;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' =>
                        [
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportBox/create'],
                                'actions' => [
                                    'create','chart-types','get-widgets-by-range',
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportBox/update'],
                                'actions' => [
                                    'update','chart-types','get-widgets-by-range',
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportBox/delete'],
                                'actions' => [
                                    'delete'
                                ]
                            ],
                        ]
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'create' => ['GET', 'POST'],
                        'update' => ['GET', 'POST'],
                        'delete' => ['POST', 'DELETE'],
                    ],
                ],
            ]
        );
    }

    public function actionCreate($dashboardId)
    {
        $model = new ReportBox();
        $model->scenario = $model::SCENARIO_CREATE;

        if ($model->load($this->request->post())) {

            $model->dashboard_id = $dashboardId;
            $valid = $model->validate();

            if ($valid) {
                try {
                    $model->save(false);
                    return $this->asJson([
                        'status' => true,
                        'message' => Yii::t("biDashboard", 'Box Saved Successfully')
                    ]);

                } catch (Exception $e) {
                    Yii::error($e->getMessage() .PHP_EOL. $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                    return $this->asJson([
                        'status' => false,
                        'message' => $e->getMessage() .PHP_EOL. Yii::t("biDashboard", 'Error In Save Box'),
                    ]);
                }
            }

            else {
                return $this->asJson([
                    'status' => false,
                    'message' => $model->errors,
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('create', [
            'model' => $model,
        ]);

    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = $model::SCENARIO_UPDATE;

        if ($model->load($this->request->post()) && $model->validate()) {

            if ($model->save(false)) {
                return $this->asJson([
                    'status' => true,
                    'message' => Yii::t("biDashboard", 'The Operation Was Successful')
                ]);
            }
            else {
                return $this->asJson([
                    'status' => false,
                    'message' => $model->errors,
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('update', [
            'model' => $model,
        ]);

    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            return $this->asJson([
                'status' => true,
                'message' => Yii::t("biDashboard", 'The Operation Was Successful')
            ]);
        }
        else {
            return $this->asJson([
                'status' => false,
                'message' => Yii::t("biDashboard", 'Error In Delete Action')
            ]);
        }
    }

    public function actionChartTypes() {

        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $display_type = $_POST['depdrop_parents'][0];
            if ($display_type == ReportBox::DISPLAY_CHART) {

                $charts = ReportBox::itemAlias('ChartNames');
                foreach ($charts as $key => $chart){
                    $out[] = [
                        "id" => $key,
                        "name" => $chart
                    ];
                }

                return $this->asJson([
                    'output' => $out,
                    'selected' => '',
                ]);
            }
        }

        return $this->asJson([
            'output' => '',
            'selected' => '',
        ]);
    }

    public function actionGetWidgetsByRange(){

        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null){
                $range_type = $parents[0];
                $widgets = ReportWidget::itemAlias('List', $range_type);
                foreach ($widgets as $key => $widget){
                    $out[] = [
                        "id" => $key,
                        "name" => $widget
                    ];
                }
                return $this->asJson([
                    'output' => $out,
                    'selected' => '',
                ]);
            }
        }

        return $this->asJson([
            'output' => '',
            'selected' => '',
        ]);
    }

    protected function findModel(int $id): ReportBox
    {
        if (($model = ReportBox::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}