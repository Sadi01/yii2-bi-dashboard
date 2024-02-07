<?php

namespace sadi01\bidashboard\controllers;

use common\models\BaseModel;
use sadi01\bidashboard\helpers\CoreHelper;
use sadi01\bidashboard\models\ReportBaseModel;
use sadi01\bidashboard\models\ReportBox;
use sadi01\bidashboard\models\ReportBoxWidgets;
use sadi01\bidashboard\models\ReportDashboard;
use sadi01\bidashboard\models\ReportDashboardWidget;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\models\ReportDashboardSearch;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use sadi01\bidashboard\traits\CoreTrait;
use yii\base\Model;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use Yii;

use yii\helpers\ArrayHelper;

class ReportDashboardController extends Controller
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
                                'actions' => [
                                    'view-by-access-key'
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportDashboard/index'],
                                'actions' => [
                                    'index'
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportDashboard/view'],
                                'actions' => [
                                    'view'
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportDashboard/create'],
                                'actions' => [
                                    'create',
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportDashboard/update'],
                                'actions' => [
                                    'update',
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportDashboard/delete'],
                                'actions' => [
                                    'delete'
                                ]
                            ],
                        ]
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'index' => ['GET'],
                        'view' => ['GET', 'POST'],
                        'create' => ['GET', 'POST'],
                        'update' => ['GET', 'POST'],
                        'delete' => ['POST', 'DELETE'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $searchModel = new ReportDashboardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @param $mustBeUpdated
     * @param $year
     * @param $month
     * @param $day
     * @return string|Response
     *
     *
     */
    public function actionView($id, $mustBeUpdated = false, $year = null, $month = null, $day = null)
    {
        $model = $this->findModel($id);
        $boxes = $model->dashboardBoxes;

        $year = $year ?? CoreHelper::getCurrentYear();
        $month = $month ?? CoreHelper::getCurrentMonth();
        $day = $day ?? CoreHelper::getCurrentDay();

        $errors = [];
        $charts = [];
        $cards = [];
        $tables = [];

        foreach ($boxes as $box){

            if ($box->display_type == ReportBox::DISPLAY_CHART)
                $box->chartCategories = $box->getChartCategories($year,$month);

            if ($box->range_type == ReportBox::RANGE_TYPE_DAILY)
                $box->rangeDateCount = count($this->getMonthDays("$year/$month"));

            foreach ($box->boxWidgets as $widget){

                $widget->setWidgetProperties();
                $date_array = $widget->getStartAndEndTimestamps($widget, $year, $month, $day);

                if ($mustBeUpdated)
                    $widget->widget->runWidget($date_array['start'], $date_array['end']);

                $lastResult = $widget->widget->lastResult($date_array['start'], $date_array['end']);
                $widgetLastResult = $lastResult ? $lastResult->add_on['result'] : [];
                $results = array_reverse($widgetLastResult);

                if (!empty($results)) {
                    $widget->collectResults($widget, $results);
                }

                if ($widget->errors) {
                    $errors[] = $widget->errors;
                }
            }

            // Separate Boxes based on display_type
            match ($box->display_type)
            {
                ReportBox::DISPLAY_CARD => $cards[] = $box,
                ReportBox::DISPLAY_CHART => $charts[] = $box,
                ReportBox::DISPLAY_TABLE => $tables[] = $box,
            };
        }

        // Collect chart series data
        foreach ($charts as $chart) {
            foreach ($chart->boxWidgets as $boxWidget){
                $chart->chartSeries[] = ['name' => $boxWidget->title ?? $boxWidget->widget->title, 'data' => $boxWidget->results['chartData'] ?? ''];
            }
        }

        $monthDaysCount = count($this->getMonthDays($year . '/' . $month));

        if ($mustBeUpdated){
            return $this->asJson([
                'status' => true,
                'message' => $errors ? Yii::t('biDashboard', 'Error In Run Widget') : Yii::t("biDashboard", 'Success'),
            ]);
        }

        else {
            return $this->render('view', [
                'model' => $model,
                'boxes' => $boxes,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'charts' => $charts,
                'tables' => $tables,
                'cards' => $cards,
                'monthDaysCount' => $monthDaysCount,
            ]);
        }

    }

    public function actionCreate()
    {
        $model = new ReportDashboard();

        if ($model->load($this->request->post()) && $model->validate()) {

            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);

            else {
                return $this->asJson([
                    'status' => false,
                    'message' => Yii::t("biDashboard", 'Error In Save Page')
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

        if ($model->load($this->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                return $this->asJson([
                    'status' => true,
                    'message' => Yii::t("biDashboard", 'The Operation Was Successful')
                ]);
            } else {
                return $this->asJson([
                    'status' => false,
                    'message' => Yii::t("biDashboard", 'Error In Update Dashboard')
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('update', [
            'model' => $model,
        ]);

    }

    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            return $this->asJson([
                'status' => true,
                'message' => Yii::t("biDashboard", 'The Operation Was Successful')
            ]);
        } else {
            return $this->asJson([
                'status' => false,
                'message' => Yii::t("biDashboard", 'Error In Delete Action')
            ]);
        }
    }

    protected function findModel(int $id): ReportDashboard
    {
        if (($model = ReportDashboard::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}