<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var sadi01\bidashboard\models\ExternalData $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="external-data-form">

    <?php $form = ActiveForm::begin(['id' => 'external-data-form', 'enableClientValidation' => true]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('biDashboard', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
