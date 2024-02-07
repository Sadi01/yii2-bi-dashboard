<?php

use sadi01\bidashboard\models\ExternalData;
use sadi01\bidashboard\widgets\grid\ActionColumn;
use sadi01\bidashboard\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var sadi01\bidashboard\models\ExternalDataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\data\ActiveDataProvider $dataProviderValues */

$this->title = Yii::t('biDashboard', 'External Datas');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'p-jax-external-data', 'enablePushState' => false]); ?>

<div class="page-content container-fluid text-left" id="main-wrapper">
    <div class="work-report-index card">
        <div class="panel-group m-bot20" id="accordion">
            <div class="card-header d-flex justify-content-between">
                <h4 class="panel-title pt-2">
                    <?= Yii::t('biDashboard', 'External Data'); ?>
                </h4>
                <div>
                    <?=
                    Html::a(Yii::t('biDashboard', 'Create External Data'), "javascript:void(0)",
                        [
                            'data-pjax' => '0',
                            'class' => "btn btn-primary",
                            'data-size' => 'modal-md',
                            'data-title' => Yii::t('app', 'create'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax-bi',
                            'data-url' => Url::to(['/bidashboard/external-data/create']),
                            'data-handle-form-submit' => 1,
                            'data-reload-pjax-container' => 'p-jax-external-data'
                        ])
                    ?>
                </div>
            </div>
            <div class="card-body page-content container-fluid text-left">
                <?= $this->render('_search', ['model' => $searchModel]); ?>

                <?= $this->render('/external-data-value/_nav') ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'id',
                        'title',
                        [
                            'attribute' => 'created_at',
                            'value' => function (ExternalData $item) {
                                return Yii::$app->pdate->jdate('Y/m/d-h:i', $item->created_at);
                            }
                        ],
                        [
                            'class' => ActionColumn::class,
                            'urlCreator' => function ($action, ExternalData $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            },
                            'template' => '{add-value} {view}  {update}  {delete}',
                            'buttons' => [
                                'update' => function ($url, ExternalData $model, $key) {
                                    return Html::a('<i class="fa fa-pen"></i>', "javascript:void(0)",
                                        [
                                            'data-pjax' => '0',
                                            'class' => "btn text-primary",
                                            'data-size' => 'modal-md',
                                            'data-title' => Yii::t('biDashboard', 'update'),
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal-pjax-bi',
                                            'data-url' => Url::to(['/bidashboard/external-data/update', 'id' => $model->id]),
                                            'data-handle-form-submit' => 1,
                                            'data-reload-pjax-container' => 'p-jax-external-data'
                                        ]
                                    );
                                },
                                'delete' => function ($url, ExternalData $model, $key) {
                                    return Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0)', [
                                        'title' => Yii::t('biDashboard', 'Delete'),
                                        'aria-label' => Yii::t('biDashboard', 'Delete'),
                                        'data-reload-pjax-container' => 'p-jax-external-data',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['/bidashboard/external-data/delete', 'id' => $model->id]),
                                        'class' => 'p-jax-btn text-danger p-0',
                                        'data-title' => Yii::t('biDashboard', 'Delete'),
                                        'data-toggle' => 'tooltip',
                                    ]);
                                },
                                'add-value' => function ($url, ExternalData $model, $key) {
                                    return Html::a('<i class="fa fa-plus-circle"></i>', "javascript:void(0)", [
                                        'title' => Yii::t('biDashboard', 'Add new value'),
                                        'aria-label' => Yii::t('biDashboard', 'Add new value'),
                                        'data-pjax' => '0',
                                        'class' => "btn text-success",
                                        'data-size' => 'modal-md',
                                        'data-title' => Yii::t('biDashboard', 'Add new value'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax-bi',
                                        'data-url' => Url::to(['/bidashboard/external-data-value/create', 'external_data_id' => $model->id]),
                                        'data-handle-form-submit' => 1,
                                        'data-reload-pjax-container' => 'p-jax-external-data',
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>