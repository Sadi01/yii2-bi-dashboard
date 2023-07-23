<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var sadi01\bidashboard\models\ReportYear $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="report-year-form">

    <?php $form = ActiveForm::begin(['id' => 'year-form', 'enableClientValidation' => true]); ?>
    <?= $form->field($model, 'year')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>