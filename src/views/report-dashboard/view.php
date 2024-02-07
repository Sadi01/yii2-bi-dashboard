<?php

use sadi01\bidashboard\widgets\Alert;
use sadi01\bidashboard\models\ReportDashboard;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\models\ReportBox;
use sadi01\bidashboard\models\ReportBoxWidgets;
use sadi01\bidashboard\models\ReportYear;
use yii\helpers\Html;
use yii\helpers\Time;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\Highmaps;
use miloschuman\highcharts\GanttChart;
use yii\web\JsExpression;
use miloschuman\highcharts\HighchartsAsset;
/**
 * @var View $this
 * @var ReportDashboard $model
 * @var ReportBox $boxes
 * @var ReportBox $charts
 * @var ReportBox $tables
 * @var ReportBoxWidgets $cards
 */
$this->title = $model->title;
$pdate = Yii::$app->pdate;

HighchartsAsset::register($this)->withScripts(['modules/stock', 'modules/exporting', 'modules/drilldown']);

$this->registerJsFile('https://code.highcharts.com/modules/wordcloud.js', [
    'depends' => 'miloschuman\highcharts\HighchartsAsset'
]);
?>

<div class="report-dashboard-view">

    <?php Pjax::begin([
        'id' => 'p-jax-report-dashboard-view',
        'enablePushState' => false,
        'timeout' => false,
        ]); ?>

    <div class="bg-white p-3 d-flex justify-content-between rounded-md mb-4 shadow-sm">
        <?= Alert::widget() ?>
        <?php if (Yii::$app->user->identity): ?>
        <div class="d-flex flex-column">
            <h1 class="h4 font-bold"><?= $model->title ?></h1>
            <span class="text-muted"><?= $model->description ?></span>
        </div>
        <div class="d-flex align-items-center">
            <?= Html::a('<i class="fa-plus far"></i>' . ' ' . Yii::t('biDashboard', 'create box'), "javascript:void(0)",
                [
                    'data-pjax' => '0',
                    'class' => "btn btn-secondary rounded-md mr-2",
                    'data-size' => 'modal-xl',
                    'data-title' => Yii::t('biDashboard', 'create box'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax-bi',
                    'data-url' => Url::to(['report-box/create', 'dashboardId' => $model->id]),
                    'data-handle-form-submit' => 1,
                    'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                ]) ?>
            <?= Html::a(Yii::t('biDashboard', 'Sync Dashboard'), 'javascript:void(0)',
                [
                    'title' => Yii::t('biDashboard', 'Sync Dashboard'),
                    'aria-label' => Yii::t('biDashboard', 'Sync Dashboard'),
                    'data-reload-pjax-container' => 'p-jax-report-dashboard-view',
                    'data-pjax' => '0',
                    'data-url' => Url::to(['/bidashboard/report-dashboard/view', 'id' => $model->id, 'mustBeUpdated' =>  true, 'year' => $year, 'month' => $month, 'day' => $day]),
                    'class' => " p-jax-btn btn btn-success rounded-md mr-1",
                    'data-title' => Yii::t('biDashboard', 'Sync Dashboard'),
                ]); ?>
            <form>
                <div class="d-flex mr-1">
                    <div class="px-1">
                        <select name="day" class="form-control rounded-md" onchange="this.form.submit()">
                            <?php for ($i = 1; $i <= $monthDaysCount; $i++): ?>
                                <option value="<?= $i ?>" <?= $day == $i ? 'selected' : '' ?> ><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="px-1">
                        <select name="month" class="form-control rounded-md" onchange="this.form.submit()">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= $month == $i ? 'selected' : '' ?> ><?= $pdate->jdate_words(['mm' => $i])['mm'] ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="px-1">
                        <select name="year" class="form-control rounded-md" onchange="this.form.submit()">
                            <?php foreach (ReportYear::itemAlias('List') as $Year): ?>
                                <option <?= $Year ?> <?= $year == $Year ? 'selected' : '' ?> ><?= $Year ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <input type="hidden" name="id" value="<?= $model->id ?>">
                    </div>
                </div>
            </form>
            <a href="<?= Url::to(['/bidashboard/report-dashboard/']) ?>" class="btn btn-info rounded-md">بازگشت</a>
        </div>
        <?php endif; ?>
    </div>
    <div class="container-xxl">

        <?php if (!empty($cards)): ?>
            <!--  Separator  -->
            <div class="separator text-muted my-4">
                <span class="font-bold h4 mb-0 mx-2"><?= Yii::t('biDashboard', 'Cards') ?></span>
            </div>
            <!--  Cards  -->
            <div class="row">
            <?php foreach($cards as $box):?>
                <div class="m-2">
                    <div class="card border rounded-md shadow mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between px-3">
                            <span class="mr-3"><?= $box->title ?? 'عنوان باکس' ?> | <span class="font-12 font-normal">(<?= ReportBox::itemAlias('RangeType', $box->range_type) ?>)</span></span>
                            <div class="d-flex align-items-center">
                                <?= Html::a('<i class="fas fa-edit text-info font-18"></i>', "javascript:void(0)",
                                    [
                                        'data-pjax' => '0',
                                        'class' => "d-flex mr-3",
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('biDashboard', 'Edit Box'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['report-box/update', 'id' => $box->id]),
                                        'data-handle-form-submit' => 1,
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                    ]) ?>
                                <?= Html::a('<i class="fas fa-trash-alt text-danger font-18"></i>', "javascript:void(0)",
                                    [
                                        'title' => Yii::t('biDashboard', 'Delete Box'),
                                        'aria-label' => Yii::t('yii', 'Delete Box'),
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['/bidashboard/report-box/delete', 'id' => $box->id]),
                                        'class' => "p-jax-btn d-flex mr-2",
                                        'data-title' => Yii::t('biDashboard', 'Delete Box'),
                                        'data-toggle' => 'tooltip',
                                    ]) ?>
                            </div>
                        </div>
                        <div class="card-body row min-h-60">
                            <?php if (!empty($box->boxWidgets)): ?>
                                <?php foreach($box->boxWidgets as $card):?>
                                    <div class="card shadow-sm text-white <?= $card->widget_card_color ? ReportBoxWidgets::itemAlias('CardColorsClass', $card->widget_card_color) : 'bg-secondary' ?> m-2 rounded-md min-width-card">
                                        <div class="card-header px-2 d-flex justify-content-between align-items-center">
                                            <span class="font-12"><?= $card->title ?? $card->widget->title ?></span>
                                            <div class="d-flex align-items-center">
                                                <?= Html::a('<i class="fas fa-trash-alt text-danger font-14"></i>', "javascript:void(0)",
                                                    [
                                                        'title' => Yii::t('biDashboard', 'Delete'),
                                                        'data-pjax' => '0',
                                                        'class' => "p-jax-btn d-flex btn btn-light p-button",
                                                        'data-url' => Url::to(['report-box-widget/delete', 'widgetId' => $card->id]),
                                                        'data-handle-form-submit' => 1,
                                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view',
                                                        'data-toggle' => 'tooltip',
                                                    ]) ?>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h2 class="text-center"><?= ReportBoxWidgets::getFormattedValue($card->widget_field_format, $card->cardResultCount) ?></h2>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between align-items-center h6 p-2 mb-0">
                                            <p class="mb-0"><?= $card->description ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <span class="text-muted">این باکس خالی است!</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between px-3">
                            <span class="text-muted font-12 mr-3"><?= $box->description ?? '(توضیحات باکس)' ?></span>
                            <div class="d-flex">
                                <?= Html::a(Yii::t('biDashboard', 'Add and Edit Widgets'), "javascript:void(0)",
                                    [
                                        'data-pjax' => '0',
                                        'class' => "btn btn-info btn-sm rounded-md font-12 ml-2",
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('biDashboard', 'Add and Edit Widgets'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['report-box-widget/update', 'boxId' => $box->id]),
                                        'data-handle-form-submit' => 1,
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($charts)): ?>

            <!--  Separator  -->
            <div class="separator text-muted my-4">
                <span class="font-bold h4 mb-0 mx-2"><?= Yii::t('biDashboard', 'Charts') ?></span>
            </div>

            <!--  Charts  -->
            <div class="" style="display:grid; grid-template-columns: 1fr 1fr;gap:1.5rem; padding:0 1rem;max-width:calc(100% - 15rem);">
                <?php foreach($charts as $index => $box):?>
                    <div class="border bg-white rounded-md shadow mb-2 text-center">
                        <div class="card-header d-flex align-items-center justify-content-between px-3">
                            <span><?= $box->title ?? 'عنوان باکس' ?> | <span class="font-12 font-normal">(<?= ReportBox::itemAlias('RangeType', $box->range_type) ?>)</span></span>
                            <div class="d-flex align-items-center">
                                <?= Html::a('<i class="fas fa-edit text-info font-18"></i>', "javascript:void(0)",
                                    [
                                        'data-pjax' => '0',
                                        'class' => "d-flex mr-3",
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('biDashboard', 'Edit Box'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['report-box/update', 'id' => $box->id]),
                                        'data-handle-form-submit' => 1,
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                    ]) ?>
                                <?= Html::a('<i class="fas fa-trash-alt text-danger font-18"></i>', "javascript:void(0)",
                                    [
                                        'title' => Yii::t('biDashboard', 'Delete Box'),
                                        'aria-label' => Yii::t('yii', 'Delete Box'),
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['/bidashboard/report-box/delete', 'id' => $box->id]),
                                        'class' => "p-jax-btn d-flex",
                                        'data-title' => Yii::t('biDashboard', 'Delete Box'),
                                        'data-toggle' => 'tooltip',
                                    ]) ?>
                            </div>
                        </div>
                        <?php if (!empty($box->chartSeries)): ?>
                        <?= Highcharts::widget([
                            'options' => [
                                'scripts' => [
                                    'modules/exporting',
                                    'themes/grid',
                                ],
                                'accessibility' => [
                                    'enabled' => false
                                ],
                                'chart' => [
                                    'style' => [
                                        'fontFamily' => 'IRANSans, sans-serif',
                                        /*'direction' => 'rtl'*/
                                    ],
                                    'type' => ReportBox::itemAlias('ChartTypes', $box->chart_type),
                                    'className' => 'high-chart'
                                ],
                                'lang' => [
                                    'printChart' => 'چاپ نمودار',
                                    'downloadPNG' => 'PNG دانلود تصویر',
                                    'downloadJPEG' => 'JPEG دانلود تصویر',
                                    'downloadPDF' => 'PDF دانلود فایل',
                                    'downloadSVG' => 'SVG دانلود فایل',
                                    'viewFullscreen' => 'مشاهده در حالت تمام صفحه',
                                ],
                                'title' => [
                                    'text' => '' ,
                                ],
                                'xAxis' => [
                                    'categories' => $box->chartCategories
                                ],
                                'yAxis' => [
                                    'title' => [
                                        'text' => 'مقدار'
                                    ]
                                ],
                                'series' => $box->chartSeries
                            ]
                        ]); ?>

                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center min-h-96 w-100">
                                <span class="text-muted">این باکس خالی است!</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-footer d-flex align-items-center justify-content-between px-3">
                            <span class="text-muted font-12 mr-3"><?= $box->description ?? '(توضیحات باکس)' ?></span>
                            <div class="d-flex">
                                <?= Html::a(Yii::t('biDashboard', 'Add and Edit Widgets'), "javascript:void(0)",
                                    [
                                        'data-pjax' => '0',
                                        'class' => "btn btn-info btn-sm rounded-md font-12 ml-1",
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('biDashboard', 'Edit Box Widgets'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['/bidashboard/report-box-widget/update', 'boxId' => $box->id]),
                                        'data-handle-form-submit' => 1,
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($tables)): ?>

            <!--  Separator  -->
            <div class="separator text-muted my-4">
                <span class="font-bold h4 mb-0 mx-2"><?= Yii::t('biDashboard', 'Tables') ?></span>
            </div>

            <!--  Tables  -->
            <?php foreach($tables as $box):?>
                <div class="card text-center table-responsive border rounded-md shadow mb-5">
                    <div class="card-header d-flex align-items-center justify-content-between px-3">
                        <span><?= $box->title ?? 'عنوان باکس' ?> | <span class="font-12 font-normal">(<?= ReportBox::itemAlias('RangeType', $box->range_type) ?>)</span></span>
                        <div class="d-flex align-items-center">
                            <?= Html::a('<i class="fas fa-edit text-info font-18"></i>', "javascript:void(0)",
                                [
                                    'data-pjax' => '0',
                                    'class' => "d-flex mr-3",
                                    'data-size' => 'modal-xl',
                                    'data-title' => Yii::t('biDashboard', 'Edit Box'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax-bi',
                                    'data-url' => Url::to(['report-box/update', 'id' => $box->id]),
                                    'data-handle-form-submit' => 1,
                                    'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                ]) ?>
                            <?= Html::a('<i class="fas fa-trash-alt text-danger font-18"></i>', "javascript:void(0)",
                                [
                                    'title' => Yii::t('biDashboard', 'Delete Box'),
                                    'aria-label' => Yii::t('yii', 'Delete Box'),
                                    'data-reload-pjax-container' => 'p-jax-report-dashboard-view',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['/bidashboard/report-box/delete', 'id' => $box->id]),
                                    'class' => "p-jax-btn d-flex",
                                    'data-title' => Yii::t('biDashboard', 'Delete Box'),
                                    'data-toggle' => 'tooltip',
                                ]) ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="bg-white mb-0 table table-hover table-striped">
                            <thead class="text-white bg-inverse">
                            <tr>
                                <th class="text-center font-bold"><?= Yii::t('biDashboard', 'widgets') ?></th>
                                <?php
                                for ($i = 1; $i <= $box->rangeDateCount; $i++) {
                                    if ($box->range_type == ReportBox::RANGE_TYPE_DAILY){
                                        echo '<th scope="col" class="text-center font-bold">'. $i . '</th>';
                                    }
                                    elseif($box->range_type == ReportBox::RANGE_TYPE_MONTHLY) {
                                        echo '<th scope="col" class="text-center">' . $pdate->jdate_words(['mm' => $i], ' ') . '</th>';
                                    }
                                } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($box->boxWidgets)): ?>
                                <?php foreach($box->boxWidgets as $table):?>
                                    <tr>
                                        <td class="text-center"><?= $table->title ?? $table->widget->title ?> | <span class="font-12 text-muted"><?= $table->description ?></span></td>
                                        <?php for ($i = 1; $i <= $table->rangeDateCount; $i++) {
                                            if ($box->range_type == ReportBox::RANGE_TYPE_DAILY){
                                                if (!empty($table->results['combine'])){
                                                    if (array_key_exists($i , $table->results['combine']) == 1){
                                                        echo '<td scope="col" class="text-center">'
                                                            . ReportBoxWidgets::getFormattedValue($table->widget_field_format, $table->results['combine'][$i])
                                                            . '</td>';
                                                    }
                                                    else
                                                        echo '<td scope="col" class="text-center">'. '-' . '</td>';
                                                }
                                            }
                                            elseif($box->range_type == ReportBox::RANGE_TYPE_MONTHLY) {
                                                if (!empty($table->results['combine'])){
                                                    if (array_key_exists($pdate->jdate_words(['mm' => $i], ' ') , $table->results['combine']) == 1){
                                                        echo '<td scope="col" class="text-center">'
                                                            . ReportBoxWidgets::getFormattedValue($table->widget_field_format, $table->results['combine'][$pdate->jdate_words(['mm' => $i], ' ')])
                                                            . '</td>';
                                                    }
                                                    else
                                                        echo '<td scope="col" class="text-center">'. '-' . '</td>';
                                                }
                                            }
                                        } ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center min-h-28 w-100">
                                    <span class="text-muted">این باکس خالی است!</span>
                                </div>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between px-3">
                        <span class="text-muted font-12 mr-3"><?= $box->description ?? '(توضیحات باکس)' ?></span>
                        <div class="d-flex">
                            <?= Html::a(Yii::t('biDashboard', 'Add and Edit Widgets'), "javascript:void(0)",
                                [
                                    'data-pjax' => '0',
                                    'class' => "btn btn-info btn-sm rounded-md font-12 ml-2",
                                    'data-size' => 'modal-xl',
                                    'data-title' => Yii::t('biDashboard', 'Add and Edit Widgets'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax-bi',
                                    'data-url' => Url::to(['report-box-widget/update', 'boxId' => $box->id]),
                                    'data-handle-form-submit' => 1,
                                    'data-reload-pjax-container' => 'p-jax-report-dashboard-view'
                                ]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <?php if(empty($cards) && empty($charts) && empty($table)): ?>
    <div class="d-flex justify-content-center">
        <span>اطلاعاتی برای نمایش وجود ندارد.</span>
    </div>
    <?php endif; ?>

    <?php Pjax::end(); ?>
</div>

