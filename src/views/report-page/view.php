<?php

use yii\helpers\Html;
use sadi01\bidashboard\models\ReportPage;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;
use Yii;
use yii\widgets\Pjax;

/** @var View $this */
/** @var ReportPage $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Report Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-page-view">
    <?php Pjax::begin(['id' => 'p-jax-add-widget', 'enablePushState' => false]); ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= Html::a(Yii::t('app', 'add widgets'), "javascript:void(0)",
        [
            'data-pjax' => '0',
            'class' => "btn btn-outline-success float-right ",
            'data-size' => 'modal-xl',
            'data-title' => Yii::t('app', 'add'),
            'data-toggle' => 'modal',
            'data-target' => '#modal-pjax',
            'data-url' => Url::to(['report-page/add']),
            'data-handle-form-submit' => 1,
            'data-show-loading' => 0,
            'data-reload-pjax-container' => 'p-jax-add-widget',
            'data-reload-pjax-container-on-show' => 0
        ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
            'updated_by',
            'created_by',
        ],
    ]) ?>
    <?php Pjax::end(); ?>

</div>