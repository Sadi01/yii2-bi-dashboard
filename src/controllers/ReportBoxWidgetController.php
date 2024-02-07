<?php

namespace sadi01\bidashboard\controllers;

use sadi01\bidashboard\models\ReportBox;
use sadi01\bidashboard\models\ReportBoxWidgets;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use sadi01\bidashboard\traits\CoreTrait;
use sadi01\bidashboard\models\ReportBaseModel;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class ReportBoxWidgetController extends Controller
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
                                'roles' => ['BI/ReportBoxWidget/update'],
                                'actions' => [
                                    'update', 'card-colors', 'get-widget-columns',
                                ]
                            ],
                            [
                                'allow' => true,
                                'roles' => ['BI/ReportBoxWidget/delete'],
                                'actions' => [
                                    'delete'
                                ]
                            ],
                        ]
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'update' => ['GET', 'POST'],
                        'delete' => ['POST', 'DELETE'],
                    ],
                ],
            ]
        );
    }

    public function actionUpdate($boxId)
    {
        $modelBox = ReportBox::find()->where(['id' => $boxId])->one();
        $modelsWidget = $modelBox->boxWidgets;

        if ($modelBox->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsWidget, 'id', 'id');
            $modelsWidget = ReportBaseModel::createMultiple(ReportBoxWidgets::class, multipleModels: $modelsWidget);
            $valid = Model::loadMultiple($modelsWidget, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsWidget, 'id', 'id')));

            // validate all models
            $valid = $modelBox->validate();
            $valid = Model::validateMultiple($modelsWidget) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelBox->save(false)) {
                        if (!empty($deletedIDs)) {
                            ReportBoxWidgets::softDeleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsWidget as $modelWidget) {

                            $modelWidget->box_id = $boxId;
                            if (! ($flag = $modelWidget->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->asJson([
                            'status' => true,
                            'message' => Yii::t("biDashboard", 'The Operation Was Successful')
                        ]);
                    }
                    else {
                        $transaction->rollBack();
                        return $this->asJson([
                            'status' => false,
                            'message' => Yii::t("biDashboard", 'The Operation Failed'),
                        ]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() .PHP_EOL. $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                    return $this->asJson([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
            else {
                return $this->asJson([
                    'status' => false,
                    'message' => Yii::t("biDashboard", 'The Operation Failed'),
                ]);
            }
        }

        $this->performAjaxMultipleError($modelsWidget);
        return $this->renderAjax('update', [
            'modelBox' => $modelBox,
            'modelsWidget' => (empty($modelsWidget)) ? [new ReportBoxWidgets()] : $modelsWidget
        ]);
    }

    public function actionDelete(int $widgetId)
    {
        $model = $this->findModel($widgetId);

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

    public function actionCardColors() {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $display_type = $_POST['depdrop_parents'][0];
            if ($display_type == ReportBox::DISPLAY_CARD) {

                $colors = ReportBoxWidgets::itemAlias('CardColorsName');
                foreach ($colors as $key => $color){
                    $out[] = [
                        "id" => $key,
                        "name" => $color
                    ];
                }
                return ['output' => $out, 'selected' => ''];
            }
        }

        return ['output' => '', 'selected' => ''];
    }

    public function actionGetWidgetColumns()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {

            $widgetId = $_POST['depdrop_parents'][0];

            if ($widgetId != null ) {
                $widget = ReportWidget::findOne(['id' => $widgetId]);
                if (!$widget) {
                    return ['output' => [], 'selected' => ''];
                }
                $outputColumns = $widget->outputColumn;
                foreach ($outputColumns as $item) {
                    $out[] = ['id' => $item['column_name'], 'name' => $item['column_title']];
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    protected function findModel(int $id): ReportBoxWidgets
    {
        if (($model = ReportBoxWidgets::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}