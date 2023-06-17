<?php

namespace sadi01\bidashboard\widgets\views;

use sadi01\bidashboard\BiAssets;
use Yii;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;

$biAssets = BiAssets::register($this);
$url = $biAssets->baseUrl;

$this->title = 'Bi dashboard widget';

/**
 * @var $searchModel object
 * @var $searchRoute string
 * @var $searchModelMethod string
 * @var $searchModelFormName string
 * @var $queryParams array
 */

?>

<?= Html::a(Html::tag('span', 'افزودن ویجت', ['class' => ['btn btn-info']]), "javascript:void(0)",
    [
        'data-pjax' => '0',
        'data-size' => 'modal-xl',
        'title' => Yii::t('biDashboard', 'Save As Report Widget'),
        'data-title' => Yii::t('biDashboard', 'Save As Report Widget'),
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to([
            '/bidashboard/report-widget/create',
            'searchModelClass' => $searchModel::class,
            'searchModelMethod' => $searchModelMethod,
            'searchModelRunResultView' => $searchModelRunResultView,
            'search_route' => $searchRoute,
            'search_model_form_name' => $searchModelFormName,
            'queryParams' => json_encode($queryParams),
            'output_column' => json_encode($outputColumn),
        ]),
        'data-handle-form-submit' => 1
    ]) ?>

<?= Html::a(Html::tag('span', 'لیست ویجت‌ها', ['class' => ['btn btn-info']]), "javascript:void(0)",
    [
        'data-pjax' => '0',
        'data-size' => 'modal-xl',
        'title' => Yii::t('biDashboard', 'Report Widgets'),
        'data-title' => Yii::t('biDashboard', 'Report Widgets'),
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to([
            '/bidashboard/report-widget/open-modal',
            'ReportWidgetSearch[search_model_class]' => $searchModel::class,
            'ReportWidgetSearch[search_model_method]' => 'search',
        ]),
        'data-handle-form-submit' => 0
    ]) ?>