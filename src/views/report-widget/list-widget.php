<?php

use sadi01\bidashboard\models\ReportWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;
use sadi01\bidashboard\models\search\ReportWidgetSearch;
use yii\data\ActiveDataProvider;

/** @var View $this */
/** @var ReportWidgetSearch $searchModel */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('biDashboard', 'Report Widgets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content container-fluid text-left">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            'description',
            'search_model_class',
            'search_model_method',
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, ReportWidget $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
</div>