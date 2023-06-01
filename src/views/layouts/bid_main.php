<?php

use Yii;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use sadi01\bidashboard\BiAssets;
use yii\bootstrap4\Modal;

$biAssets = BiAssets::register($this);
$url = $biAssets->baseUrl;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html dir="rtl" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="<?= Yii::$app->charset ?>">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->head() ?>
    </head>

    <body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <?= $this->render('@sadi01/bidashboard/views/layouts/topbar.php', ['url' => $url]) ?>
        <?= $this->render('@sadi01/bidashboard/views/layouts/sidebar.php', ['url' => $url]) ?>
        <div class="page-wrapper">
            <div>
                <div>
                    <?= Alert::widget() ?>
                </div>
                <?php
                Modal::begin([
                    'headerOptions' => ['id' => 'modalPjaxHeader'],
                    'id' => 'modal-pjax',
                    'bodyOptions' => [
                        'id' => 'modalPjaxContent',
                        'class' => 'p-3',
                        'data' => ['show-preloader' => 0]
                    ],
                    'options' => ['tabindex' => false]
                ]); ?>
                <div class="text-center">
                    <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <?php Modal::end(); ?>
                <?php
                Modal::begin([
                    'headerOptions' => ['id' => 'modalPjaxOverHeader'],
                    'id' => 'modal-pjax-over',
                    'bodyOptions' => [
                        'id' => 'modalPjaxOverContent',
                        'class' => 'p-3',
                        'data' => ['show-preloader' => 0]
                    ],
                    'options' => ['tabindex' => false, 'style' => 'z-index:1051;']
                ]); ?>
                <div class="text-center">
                    <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <?php Modal::end(); ?>
                <div class="modal fade top-modal-with-space" id="quickAccessModal" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content-wrap">
                            <div class="modal-content">
                                <div class="modal-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->beginBody() ?>
            <?= $content ?>
            <?php $this->endBody() ?>
            <footer class="footer text-center">
                BI Dashboard
            </footer>
        </div>
    </html>
<?php $this->endPage();