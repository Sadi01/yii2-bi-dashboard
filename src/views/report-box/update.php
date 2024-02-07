<?php

use yii\helpers\Html;
use yii\web\View;
use sadi01\bidashboard\models\ReportBox;

/** @var View $this */
/** @var ReportBox $model */

$this->title = Yii::t('app', 'Update Box: {title}', [
    'title' => $model->title,
]);
?>

<div class="report-widget-create">
    <div class="page-content container-fluid text-left ">
        <div class="work-report-index ">
            <div class="panel-group m-bot20" id="accordion">
                <div class="card-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>