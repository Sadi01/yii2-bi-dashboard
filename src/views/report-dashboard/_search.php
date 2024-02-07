<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var \sadi01\bidashboard\models\ReportDashboardSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="report-dashboard">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="d-flex align-items-end">
        <div class="search-filed min-w-60 mr-2">
            <?= $form->field($model, 'title')->textInput([
                'class' => 'form-control rounded-md',
                'placeholder' => 'عنوان داشبورد را وارد کنید...'
            ])->label(false) ?>
        </div>
        <div class="">
            <?= Html::submitButton(Yii::t('biDashboard', 'Search'), ['class' => 'btn btn-primary rounded-md']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
