<?php

use sadi01\bidashboard\models\ReportDashboard;
use yii\helpers\Html;
use yii\web\View;

/** @var ReportDashboard $model */
/** @var View $this */

$this->title = Yii::t('biDashboard', 'create dashboard');
$this->params['breadcrumbs'][] = ['label' => Yii::t('biDashboard', 'Dashboard'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-widget-create">
    <div class="page-content container-fluid text-left">
        <div class="work-report-index">
            <div class="panel-group m-bot20" id="accordion">
                <div class="report-page-create">

                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>