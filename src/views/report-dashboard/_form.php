<?php

use sadi01\bidashboard\models\ReportDashboard;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var ReportDashboard $model */
/** @var ActiveForm $form */

?>


<div class="dashboard-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form', 'enableClientValidation' => true]); ?>

    <div class="row justify-content-center">
        <div class="col-5">
            <?= $form->field($model, 'title')->textInput([
                'placeholder' => 'عنوان داشبورد را وارد کنید...',
                'maxlength' => true,
                'class' => 'form-control rounded-md input-border'
            ])->label('عنوان داشبورد'. ' '. '<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-7">
            <?= $form->field($model, 'description')->textInput([
                'placeholder' => 'توضیحات داشبورد را وارد کنید...',
                'maxlength' => true,
                'class' => 'form-control rounded-md input-border'
            ])->label('توضیحات داشبورد'. ' '. '<span class="text-danger">*</span>') ?>
        </div>
        <?php if ($model->isNewRecord): ?>
            <div class="col-4">
                <?= $form->field($model, 'status')->widget(Select2::class, [
                    'data' => ReportDashboard::itemAlias('Status'),
                    'options' => [
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'initialize' => !$model->isNewRecord,
                    ],
                ]); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-end">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('biDashboard', 'Save Dashboard') : Yii::t('biDashboard', 'update'), ['class' => 'btn btn-success rounded-md']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
