<?php

use sadi01\bidashboard\models\ReportDashboard;
use sadi01\bidashboard\models\ReportDashboardSearch;
use sadi01\bidashboard\widgets\grid\ActionColumn;
use sadi01\bidashboard\widgets\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var ReportDashboardSearch $searchModel */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('biDashboard', 'Report Dashboards');
$this->params['breadcrumbs'][] = ' ' . $this->title;

?>

<div class="page-content container-fluid text-left pt-5" id="main-wrapper">
    <?php Pjax::begin(['id' => 'p-jax-report-dashboard', 'enablePushState' => false]); ?>
    <div class="work-report-index card">
        <div class="panel-group m-bot20" id="accordion">
            <div class="card-header d-flex justify-content-between">
                <div class="d-flex align-items-center font-16 font-bold"><span><?= Yii::t('biDashboard', 'Dashboard List') ?></span></div>
                <?= $this->render('_search', ['model' => $searchModel]); ?>
                <div>
                    <?= Html::a(Yii::t('biDashboard', 'create dashboard'), "javascript:void(0)",
                        [
                            'data-pjax' => '0',
                            'class' => "btn btn-success rounded-md",
                            'data-size' => 'modal-lg',
                            'data-title' => Yii::t('biDashboard', 'create dashboard'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax-bi',
                            'data-url' => Url::to(['report-dashboard/create']),
                            'data-handle-form-submit' => 1,
                            'data-reload-pjax-container' => 'p-jax-report-dashboard'
                        ]) ?>
                </div>
            </div>
            <div class="card-body page-content container-fluid text-left">

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'id',
                        [
                            'attribute' => 'title',
                            'format' => 'raw',
                            'value' => function (ReportDashboard $model) {
                                return Html::a($model->title, ['/bidashboard/report-dashboard/view','id' => $model->id ], [
                                    'title' => $model->title,
                                    'class' => 'btn text-info',
                                    'data-toggle' => 'tooltip',
                                    'data-pjax' => '0',
                                    'style' => 'font-size:18px;'
                                ]);
                            },
                        ],
                        'description',
                        [
                            'attribute' => 'status',
                            'value' => function (ReportDashboard $model) {
                                return ReportDashboard::itemAlias('Status', $model->status);
                            },
                        ],
                        [
                            'class' => ActionColumn::class,
                            'template' => '{view} {delete}',
                            'urlCreator' => function ($action, ReportDashboard $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            },
                            'template' => '{view} {update} {delete}',
                            'buttons' => [
                                'delete' => function ($url, ReportDashboard $model, $key) {
                                    return Html::a('<i class="fas fa-trash-alt"></i>', 'javascript:void(0)', [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'aria-label' => Yii::t('yii', 'Delete'),
                                        'data-reload-pjax-container' => 'p-jax-report-dashboard',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['report-dashboard/delete', 'id' => $model->id]),
                                        'class' => 'p-jax-btn text-danger mr-2',
                                        'data-title' => Yii::t('yii', 'Delete'),
                                        'data-toggle' => 'tooltip',
                                    ]);
                                },
                                'update' => function ($url, ReportDashboard $model, $key) {
                                    return Html::a('<i class="fas fa-edit"></i>', "javascript:void(0)",
                                        [
                                            'data-pjax' => '0',
                                            'class' => "btn text-primary",
                                            'data-size' => 'modal-xl',
                                            'data-title' => Yii::t('biDashboard', 'update'),
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal-pjax-bi',
                                            'data-url' => Url::to(['report-dashboard/update', 'id' => $model->id]),
                                            'data-handle-form-submit' => 1,
                                            'data-reload-pjax-container' => 'p-jax-report-dashboard'
                                        ]
                                    );
                                },
                                'view' => function ($url, ReportDashboard $model, $key) {
                                    return Html::a('<i class="fas fa-eye"></i>', $url, [
                                        'title' => Yii::t('yii', 'View'),
                                        'aria-label' => Yii::t('yii', 'View'),
                                        'class' => 'btn text-info p-0',
                                        'data-toggle' => 'tooltip',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                        ],
                    ],
                    'summary' => "",
                ]); ?>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>