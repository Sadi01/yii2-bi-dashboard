<?php

use sadi01\bidashboard\models\ReportBoxWidgets;
use sadi01\bidashboard\models\ReportBox;
use yii\helpers\Html;
use yii\web\View;

/** @var ReportBoxWidgets $modelsWidget */
/** @var ReportBox $modelBox */
/** @var View $this */

$this->title = Yii::t('biDashboard', 'create dashboard');
?>
<div class="report-widget-create">
    <div class="page-content container-fluid text-left">
        <div class="work-report-index">
            <div class="panel-group m-bot20" id="accordion">
                <div class="report-page-create">
                    <?= $this->render('_form', [
                        'modelBox' => $modelBox,
                        'modelsWidget' => $modelsWidget,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>