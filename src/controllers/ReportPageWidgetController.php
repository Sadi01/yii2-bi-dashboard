<?php

namespace sadi01\bidashboard\controllers;

use sadi01\bidashboard\models\ReportPage;
use sadi01\bidashboard\models\ReportPageWidget;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ReportPageController implements the CRUD actions for ReportPage model.
 */
class ReportPageWidgetController extends Controller
{
    use AjaxValidationTrait;

    public $layout = 'bid_main';

    public function actionDelete($id_widget, $id_page)
    {
        $model = ReportPageWidget::findOne(['widget_id' => $id_widget, 'page_id' => $id_page]);

        if ($model->canDelete() && $model->softDelete()) {
            return $this->asJson([
                'success' => true,
                'msg' => Yii::t("app", 'Success')
            ]);

        } else {
            return $this->asJson([
                'success' => false,
                'msg' => Yii::t("app", 'Error')
            ]);
        }
    }

    public function beforeAction($action)
    {
        Yii::$app->controller->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * Finds the ReportPage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ReportPage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportPageWidget::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}