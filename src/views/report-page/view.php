<?php


use sadi01\bidashboard\models\ReportPageWidget;
use yii\helpers\Html;
use yii\helpers\Time;
use sadi01\bidashboard\models\ReportPage;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;
use yii\widgets\Pjax;


/**
 * @var View $this
 * @var ReportPage $model
 * @var object $pageWidgets
 * @var $pageWidget ReportPageWidget
 * @var $startRange int
 * @var $endRange int
 * @var $rangeDateNumber int
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Report Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pdate = Yii::$app->pdate;


?>

<div class="report-page-view">
    <?php Pjax::begin(['id' => 'p-jax-report-page-add', 'enablePushState' => false]); ?>
    <div class="p-3 bg-white">
        <?= Html::a(Yii::t('biDashboard', 'Update'), "javascript:void(0)",
            [
                'data-pjax' => '0',
                'class' => "btn btn-primary",
                'data-size' => 'modal-xl',
                'data-title' => Yii::t('app', 'update'),
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['report-page/update', 'id' => $model->id]),
                'data-handle-form-submit' => 1,
                'data-show-loading' => 0,
                'data-reload-pjax-container' => 'p-jax-report-page-add',
                'data-reload-pjax-container-on-show' => 0
            ]) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('biDashboard', 'اضافه کردن ویجت'), "javascript:void(0)",
            [
                'data-pjax' => '0',
                'class' => "btn btn-primary",
                'data-size' => 'modal-xl',
                'data-title' => Yii::t('app', 'create'),
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['report-page/add', 'id' => $model->id]),
                'data-handle-form-submit' => 1,
                'data-show-loading' => 0,
                'data-reload-pjax-container' => 'p-jax-report-page-add',
                'data-reload-pjax-container-on-show' => 0
            ]) ?>
        <div class="float-left">
            <span>عنوان  : </span>
            <span><?= $model->title ?></span>
            <span>/</span>
            <span class="text-muted"><?= ReportPage::itemAlias('range_type', $model->range_type) ?></span>
        </div>
    </div>

    <div>
        <div>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered bg-white">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col" class="text-center">ویجت</th>
                        <?php
                        for ($i=1;$i<=$rangeDateNumber;$i++){
                            if ($model->range_type == $model::RANGE_DAY) {
                                echo '<th scope="col" class="text-center">'.$i.'</th>';
                            }else{
                                $key = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
                                echo '<th scope="col" class="text-center">'.$key[$i-1].'</th>';
                            }
                        }
                        ?>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pageWidgets as $pageWidget): ?>
                    <?php $runWidget = $pageWidget->widget->lastResult($startRange, $endRange); ?>
                        <tr>
                            <th style="width: 17%">
                                <div class="">
                                    <div class="border-bottom">
                                        <?= Html::a('<i class="mdi mdi-launch"></i>', "javascript:void(0)",
                                            [
                                                'data-pjax' => '0',
                                                'class' => "btn btn-sm text-primary",
                                                'data-size' => 'modal-xl',
                                                'data-title' => '<i class="mdi mdi-launch"></i>',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal-pjax',
                                                'data-url' => Url::to(['/bidashboard/report-widget/view', 'id' => $pageWidget->widget->id]),
                                                'data-handle-form-submit' => 1,
                                                'data-show-loading' => 0,
                                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                                'data-reload-pjax-container-on-show' => 0
                                            ]) ?>
                                        <?= Html::a('<i class="mdi mdi-reload"></i>', "javascript:void(0)",
                                            [
                                                'data-pjax' => '0',
                                                'class' => "btn btn-sm text-success",
                                                'data-size' => 'modal-xl',
                                                'data-title' => '<i class="mdi mdi-reload"></i>',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal-pjax',
                                                'data-url' => Url::to(['/report-widget/create', 'id' => $model->id]),
                                                'data-handle-form-submit' => 1,
                                                'data-show-loading' => 0,
                                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                                'data-reload-pjax-container-on-show' => 0
                                            ]) ?>
                                        <?= Html::a('<i class="mdi mdi-pencil"></i>', "javascript:void(0)",
                                            [
                                                'data-pjax' => '0',
                                                'class' => "btn btn-sm text-info",
                                                'data-size' => 'modal-xl',
                                                'data-title' => Yii::t('app', 'update',),
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal-pjax',
                                                'data-url' => Url::to(['/report-widget/update', 'id' => $pageWidget->widget->id]),
                                                'data-handle-form-submit' => 1,
                                                'data-show-loading' => 0,
                                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                                'data-reload-pjax-container-on-show' => 0
                                            ]) ?>
                                        <?= Html::a('<i class="mdi mdi-delete"></i>', 'javascript:void(0)',
                                            [
                                                'title' => Yii::t('yii', 'delete'),
                                                'aria-label' => Yii::t('yii', 'delete'),
                                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                                'data-pjax' => '0',
                                                'data-url' => Url::to(['/bidashboard/report-page-widget/delete', 'id_widget' => $pageWidget->widget->id, 'id_page' => $model->id]),
                                                'class' => " p-jax-btn btn-sm text-danger",
                                                'data-title' => Yii::t('yii', 'delete'),
                                                'data-toggle' => 'tooltip',
                                            ]); ?>
                                    </div>
                                </div>
                                <div class="border-bottom text-left row p-3">
                                    <div class="col-12">
                                        <span>ویجت گزارش :</span>
                                        <span class="bg-warning"><?php echo $pageWidget->widget->title ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>فیلد ویجت گزارش :</label>
                                        <span class="bg-warning"><?php echo $pageWidget->report_widget_field ?></span>
                                    </div>
                                </div>
                                <div class="text-center text-info">
                                    <?=
                                        Yii::$app->formatter->asRelativeTime($runWidget->created_at, 'now');
                                    ?>
                                </div>
                            </th>
                            <?php
                                $formatter = \Yii::$app->formatter;
                                if ($runWidget) {
                                    for ($i=1;$i<=$rangeDateNumber;$i++){
                                        if ($model->range_type == $model::RANGE_DAY) {
                                            $key = array_search($i, array_column($runWidget->result, 'day'));
                                        }else{
                                            $key = array_search($i, array_column($runWidget->result, 'month'));
                                        }
                                        if ($key === false) {
                                            echo '<th scope="col"></th>';
                                        } else {
                                            $resultData = $runWidget->result[$key][$pageWidget->report_widget_field];
                                            if ($pageWidget->report_widget_field_format == $pageWidget::FORMAT_CURRENCY){
                                                $resultData = $formatter->asCurrency($resultData);
                                            }
                                            echo '<th scope="col" class="text-center">' . $resultData . '</th>';
                                        }
                                    }
                                }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>