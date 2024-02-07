<?php

use sadi01\bidashboard\models\ReportModelClass;
use sadi01\bidashboard\models\ReportWidget;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this
 * @var ReportWidget $model
 * @var ActiveForm $form
 * @var $queryString string
 * @var $queryParams array
 * @var $output_column array
 */
?>

<div class="report-widget-form">
    <?php $queryString = Yii::$app->request->queryString; ?>
    <?php $form = ActiveForm::begin(['id' => 'report_widget_form', 'action' => ['/bidashboard/report-widget/create?' . $queryString]]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'range_type')->dropDownList($model->itemAlias('RangeTypes')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'visibility')->dropDownList($model->itemAlias('Visibility')) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th colspan="2">پارامتر‌های جستجوی</th>
            </tr>
            <tr>
                <th><?= Yii::t('biDashboard', 'attribute') ?></th>
                <th><?= Yii::t('biDashboard', 'value') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($queryParams)): ?>
                <?php foreach ($queryParams as $Pkey => $Pvalue): ?>
                    <tr>
                        <td>
                            <?= Yii::t('app', $Pkey) ?>
                        </td>
                        <td>
                            <?= json_encode($Pvalue) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="repeater">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th colspan="2">فیلد‌های خروجی گزارش ویجت</th>
                <th>
                    <input data-repeater-create type="button" value="افزودن" class="btn btn-success"/>
                </th>
            </tr>
            </thead>
            <tbody data-repeater-list="output_column">
            <tr data-repeater-item>
                <th>
                    <div class="form-group">
                        <label>فیلد</label>
                        <input type="text" name="column_name" class="form-control"/>
                    </div>
                </th>
                <th>
                    <div class="form-group">
                        <label>عنوان</label>
                        <input type="text" name="column_title" class="form-control"/>
                    </div>
                </th>
                <th class="col-sm-1">
                    <div class="form-group mt-2">
                        <input data-repeater-delete type="button" value="حذف" class="btn btn-danger mt-4"/>
                    </div>
                </th>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('biDashboard', 'Save'), ['class' => 'btn btn-success col-sm-4']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        var outPutColumn = $('.repeater').repeater({
            show: function () {
                $(this).slideDown();
            },
        });

        outPutColumn.setList([
            <?php foreach ($output_column as $Kcolumn => $Vcolumn): ?>
            {
                "column_name": "<?= $Kcolumn ?>",
                "column_title": "<?= $Vcolumn ?>",
            },
            <?php endforeach; ?>
        ])
    });
</script>