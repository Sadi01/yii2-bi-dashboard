<?php

use sadi01\bidashboard\models\ReportBox;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

/** @var ReportBox $model */
/** @var ActiveForm $form */
/** @var View $this */

?>


<div class="box-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form', 'enableClientValidation' => true]); ?>

    <div class="row justify-content-center">
        <div class="col-4">
            <?= $form->field($model, 'title')->textInput([
                'placeholder' => 'عنوان باکس را وارد کنید...',
                'maxlength' => true,
                'class' => 'form-control rounded-md input-border'
            ])->label('عنوان باکس'. ' '. '<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, "display_type")->widget(Select2::class, [
                'data' => ReportBox::itemAlias('DisplayTypes'),
                'options' => [
                    'placeholder' => 'نوع نمایش ویجت های باکس را انتخاب کنید...',
                    'id' => "display_type-id"
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'initialize' => !$model->isNewRecord,
                ],
            ])->label('نوع نمایش' . ' '. '<span class="text-danger">*</span>'); ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, "chart_type")->widget(DepDrop::class, [
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'id' => "chart_type-id",
                    'placeholder' => 'نوع نمودار را انتخاب کنید...',
                ],
                'select2Options' => [
                    'pluginOptions' => ['allowClear' => true]
                ],
                'pluginOptions' => [
                    'depends' => ["display_type-id"],
                    'initialize' => !$model->isNewRecord,
                    'url' => Url::to(['/bidashboard/report-box/chart-types/'])
                ]
            ])->label('نوع نمودار'); ?>
        </div>
        <?php if ($model->isNewRecord): ?>
            <div class="col-4">
                <?= $form->field($model, "range_type")->widget(Select2::class, [
                    'data' => ReportBox::itemAlias('RangeType'),
                    'options' => [
                        'placeholder' => 'دوره نمایش باکس را انتخاب کنید...',
                        'id' => "range_type"
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'initialize' => !$model->isNewRecord,
                    ],
                ])->label('دوره نمایش'. ' '. '<span class="text-danger">*</span>'); ?>
            </div>
        <?php endif; ?>
        <div class="col-8">
            <?= $form->field($model, 'description')->textInput([
                'placeholder' => 'توضیحات مربوط به باکس را وارد کنید...',
                'maxlength' => true,
                'class' => 'form-control rounded-md input-border'
            ])->label('توضیحات باکس') ?>
        </div>
    </div>


    <div class="d-flex justify-content-end">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('biDashboard', 'Save Box') : Yii::t('biDashboard', 'update'), ['class' => 'btn btn-success rounded-md']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
