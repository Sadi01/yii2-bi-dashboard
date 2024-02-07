<?php

use sadi01\bidashboard\models\ReportModelClass;
use sadi01\bidashboard\models\ReportPage;
use sadi01\bidashboard\models\ReportPageWidget;
use sadi01\bidashboard\models\ReportYear;
use sadi01\bidashboard\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Time;
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
 * @var  ReportYear $years
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('biDashboard', 'Report Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pdate = Yii::$app->pdate;

/**
 * @var $year string
 * @var $month string
 */
?>

<div class="report-page-view">
    <?php Pjax::begin(['id' => 'p-jax-report-page-add', 'enablePushState' => false]); ?>
    <div class="p-3 bg-white">
        <?= Alert::widget() ?>
        <div class="row d-flex">
            <div class="pt-2 col-sm-2 text-left mr-auto">
                <h2 class="d-inline"><?= $model->title ?></h2>
                <?php if (Yii::$app->user->identity): ?>
                    <?= Html::a('<i class="fa fa-edit"></i>', "javascript:void(0)",
                        [
                            'data-pjax' => '0',
                            'class' => "btn text-primary",
                            'data-size' => 'modal-md',
                            'data-title' => Yii::t('biDashboard', 'update'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax-bi',
                            'data-url' => Url::to(['report-page/update', 'id' => $model->id]),
                            'data-handle-form-submit' => 1,
                            'data-show-loading' => 0,
                            'data-reload-pjax-container' => 'p-jax-report-page-add',
                            'data-reload-pjax-container-on-show' => 0
                        ]) ?>
                <?php endif; ?>
            </div>
            <?php if (Yii::$app->user->identity): ?>
                <div class="col-sm-10 d-flex justify-content-end">
                    <form>
                        <div class="d-flex justify-content-end">
                            <h4 class="bg-info px-2 py-1 text-white">
                                <strong><?= ReportPage::itemAlias('RangeType', $model->range_type) ?></strong></h4>
                            <div class="px-1">
                                <select name="year" class="form-control" onchange="this.form.submit()">
                                    <?php foreach (ReportYear::itemAlias('List') as $Year): ?>
                                        <option <?= $Year ?> <?= $year == $Year ? 'selected' : '' ?> ><?= $Year ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if ($model->range_type == $model::RANGE_DAY): ?>
                                <div class="">
                                    <select name="month" class="form-control" onchange="this.form.submit()">
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option
                                                    value="<?= $i ?>" <?= $month == $i ? 'selected' : '' ?> ><?= Yii::$app->pdate->jdate_words(['mm' => $i])['mm'] ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="px-1">
                                <input type="hidden" name="id" value="<?= $model->id ?>">
                            </div>
                        </div>
                    </form>
                    <div>
                        <?= Html::a(Yii::t('biDashboard', 'add widget'), "javascript:void(0)",
                            [
                                'data-pjax' => '0',
                                'class' => "btn btn-success",
                                'data-size' => 'modal-xl',
                                'data-title' => Yii::t('biDashboard', 'create'),
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-pjax-bi',
                                'data-url' => Url::to(['report-page/add', 'id' => $model->id]),
                                'data-handle-form-submit' => 1,
                                'data-show-loading' => 0,
                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                'data-reload-pjax-container-on-show' => 0
                            ]) ?>
                        <?= Html::a(Yii::t('biDashboard', 'run all widget'), 'javascript:void(0)',
                            [
                                'title' => Yii::t('biDashboard', 'run all widget'),
                                'aria-label' => Yii::t('biDashboard', 'run all widget'),
                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                'data-pjax' => '0',
                                'data-url' => Url::to(['/bidashboard/report-page/run-all-widgets', 'id' => $model->id, 'start_range' => $startRange, 'end_range' => $endRange]),
                                'class' => " p-jax-btn btn btn-primary",
                                'data-title' => Yii::t('biDashboard', 'run all widget'),
                            ]); ?>
                        <?= Html::a('<i class="fa fa-share-alt"></i>' . '  ' . Yii::t('biDashboard', 'share'), "javascript:void(0)",
                            [
                                'data-pjax' => '0',
                                'class' => "btn btn-primary",
                                'data-size' => 'modal-xl',
                                'data-title' => Yii::t('biDashboard', 'create'),
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-pjax-bi',
                                'data-url' => Url::to(['sharing-page/management', 'id' => $model->id]),
                                'data-handle-form-submit' => 1,
                                'data-show-loading' => 0,
                                'data-reload-pjax-container' => 'p-jax-report-page-add',
                                'data-reload-pjax-container-on-show' => 0
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-bordered bg-white">
            <thead class="text-white bg-inverse">
            <tr>
                <th scope="col" class="text-center">ویجت</th>
                <?php
                for ($i = 1; $i <= $rangeDateNumber; $i++) {
                    if ($model->range_type == $model::RANGE_DAY) {
                        echo '<th scope="col" class="text-center">' . $i . '</th>';
                    } else {
                        echo '<th scope="col" class="text-center">' . $pdate->jdate_words(['mm' => $i], ' ') . '</th>';
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pageWidgets as $pageWidget): ?>
                <?php $runWidget = $pageWidget->widget->lastResult($startRange, $endRange); ?>
                <tr>
                    <td class="bg-light">
                        <div class="d-flex justify-content-between">
                            <span class="widget-name"><?= $pageWidget->widget->title ?></span>
                            <div class="">
                                <?= Html::a('<i class="fa fa-chart-line"></i>', "javascript:void(0)",
                                    [
                                        'data-pjax' => '0',
                                        'class' => "btn btn-sm text-secondary fa-lg p-0",
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('biDashboard', 'Chart'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['report-widget/modal-show-chart', 'id' => $pageWidget->widget->id, 'field' => $pageWidget->report_widget_field, 'start_range' => $startRange, 'end_range' => $endRange, 'chart_type' => 'line']),
                                        'data-handle-form-submit' => 1,
                                        'data-show-loading' => 0,
                                        'data-reload-pjax-container' => 'p-jax-report-page-add',
                                        'data-reload-pjax-container-on-show' => 0
                                    ]) ?>
                                <?php if (Yii::$app->user->identity): ?>
                                    <?= Html::a(
                                        '<i class="fas fa-external-link-alt fa-lg text-info "></i>',
                                        [$pageWidget->widget->getModelRoute()],
                                        [
                                            'title' => Yii::t('biDashboard', 'Show Model'),
                                            'aria-label' => Yii::t('biDashboard', 'Show Model'),
                                            'data-pjax' => '0',
                                            'target' => '_blank',
                                        ]
                                    ) ?>
                                    <?= Html::a('<i class="fa fa-history text-success" aria-hidden="true"></i>', 'javascript:void(0)',
                                        [
                                            'title' => Yii::t('biDashboard', 'Reload'),
                                            'aria-label' => Yii::t('biDashboard', 'Reload'),
                                            'data-reload-pjax-container' => 'p-jax-report-page-add',
                                            'data-pjax' => '0',
                                            'data-url' => Url::to(['/bidashboard/report-widget/run', 'id' => $pageWidget->widget->id, 'start_range' => $startRange, 'end_range' => $endRange]),
                                            'class' => " p-jax-btn btn-sm text-info fa-lg p-0",
                                            'data-title' => Yii::t('biDashboard', 'Reload'),
                                            'data-toggle' => 'tooltip',
                                        ]); ?>
                                    <?= Html::a('<i class="fa fa-edit"></i>', "javascript:void(0)",
                                        [
                                            'data-pjax' => '0',
                                            'class' => "btn btn-sm text-primary fa-lg p-0",
                                            'data-size' => 'modal-md',
                                            'data-title' => Yii::t('biDashboard', 'update',),
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal-pjax-bi',
                                            'data-url' => Url::to(['/bidashboard/report-widget/update', 'id' => $pageWidget->widget->id]),
                                            'data-handle-form-submit' => 1,
                                            'data-show-loading' => 0,
                                            'data-reload-pjax-container' => 'p-jax-report-page-add',
                                            'data-reload-pjax-container-on-show' => 0
                                        ]) ?>
                                    <?= Html::a('<i class="fa fa-trash-alt"></i>', 'javascript:void(0)',
                                        [
                                            'title' => Yii::t('yii', 'Delete'),
                                            'aria-label' => Yii::t('yii', 'Delete'),
                                            'data-reload-pjax-container' => 'p-jax-report-page-add',
                                            'data-pjax' => '0',
                                            'data-url' => Url::to(['/bidashboard/report-page-widget/delete', 'id_widget' => $pageWidget->widget->id, 'id_page' => $model->id]),
                                            'class' => "p-jax-btn btn-sm text-danger fa-lg p-0",
                                            'data-title' => Yii::t('yii', 'Delete'),
                                            'data-toggle' => 'tooltip',
                                        ]); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-left my-3">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex justify-content-start pr-1">
                                    <span>ویجت گزارش :</span>
                                    <span class="bg-warning px-1">
                                            <?= ReportModelClass::itemAlias('list', $pageWidget->widget->search_model_class) ?>
                                        </span>
                                </div>
                                <div class="d-flex justify-content-start">
                                    <span>فیلد ویجت گزارش :</span>
                                    <span class="bg-warning px-1" data-toggle="tooltip"
                                          title="<?= $pageWidget->report_widget_field ?>">
                                     <?= $pageWidget->widget->getOutputColumnTitle($pageWidget->report_widget_field) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php if ($runWidget) { ?>
                            <div class="border-top text-center text-info">
                                <?= Yii::$app->formatter->asRelativeTime($runWidget->created_at, 'now'); ?>
                            </div>
                        <?php } ?>
                    </td>
                    <?php
                    if ($runWidget) {
                        $lastNumber = null;
                        $rateNumber = null;
                        $typeRate = null;

                        for ($i = 1; $i <= $rangeDateNumber; $i++) {
                            if ($model->range_type == $model::RANGE_DAY) {
                                $key = array_search($i, array_column($runWidget->result, 'day'));
                            } else {
                                $key = array_search($i, array_column($runWidget->result, 'month'));
                            }
                            if ($key === false) {
                                echo '<th scope="col"></th>';
                                $lastNumber = null;
                            } else {
                                $resultData = key_exists($pageWidget->report_widget_field, $runWidget->result[$key]) ? $runWidget->result[$key][$pageWidget->report_widget_field] : '.::field error(1)::.';

                                $resultData = (int)$resultData;

                                $salesChange = $lastNumber - $resultData;
                                $rateNumber = $lastNumber ? round(($salesChange / $lastNumber) * 100, 2) : 0;
                                $lastNumber = $resultData;

                                echo '<td scope="col" class="text-center font-bold">';
                                echo '<span id="number_item_' . $i . '">' . $pageWidget->getFormattedValue($resultData) . '</span>';
                                echo "<a class='far fa-copy text-info p-1' onclick='copyToClipboard(\"$resultData\")' href='javascript:void(0)' data-pjax='0'></a>";

                                if ($rateNumber != 0) {
                                    echo '<br />';
                                    echo '<span class="p-1 mt-2 fa ' . ($rateNumber > 0 ? 'fa-arrow-alt-circle-down text-danger' : 'fa-arrow-alt-circle-up text-success') . '"></span>';
                                    echo '%' . abs($rateNumber);
                                }
                                echo '</td>';
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
<?php Pjax::end(); ?>
