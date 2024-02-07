<?php

use sadi01\bidashboard\models\SharingPage;
use sadi01\bidashboard\widgets\grid\ActionColumn;
use sadi01\bidashboard\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var \sadi01\bidashboard\models\SharingPageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('biDashboard', 'Sharing Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content container-fluid text-left pt-5 bg-white" id="main-wrapper">
    <?php Pjax::begin(['id' => 'p-jax-sharing-page']); ?>
    <div class="work-report-index card">
        <div class="panel-group m-bot20" id="accordion">
            <div class="card-header d-flex justify-content-between">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                       href="#collapseSearch" aria-expanded="false">
                        <i class="fa fa-search"></i> جستجو
                    </a>
                </h4>
                <div>
                    <?= Html::a(Yii::t('biDashboard', 'create'), "javascript:void(0)",
                        [
                            'data-pjax' => '0',
                            'class' => "btn btn-primary",
                            'data-size' => 'modal-md',
                            'data-title' => Yii::t('biDashboard', 'create'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax-bi',
                            'data-url' => Url::to(['sharing-page/create']),
                            'data-handle-form-submit' => 1,
                            'data-reload-pjax-container' => 'p-jax-sharing-page'
                        ]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body page-content container-fluid text-left">
        <div id="collapseSearch" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'page_id',
                    'value' => function (SharingPage $model) {
                        return $model->page->title;
                    },
                ],
                [
                    'attribute' => 'access_key',
                    'format' => 'raw',
                    'value' => function (SharingPage $model) {
                        return '<span class="fa fa-copy text-info p-1" onclick="copyToClipboard(generateAccessKeyLink(\'' . $model->access_key . '\'))"></span> ' . $model->access_key;
                    },
                ],
                [
                    'attribute' => 'expire_time',
                    'value' => function (SharingPage $model) {
                        return $model->expire_time ? Yii::$app->pdate->jdate('Y/m/d-h:i', $model->expire_time) : '';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function (SharingPage $model) {
                        $tooltip = $model->created_by ?
                            Yii::t('biDashboard', 'Created by User ID') . " : {$model->created_by}"
                            : Yii::t('biDashboard', 'No creator information available');

                        $formattedDate = Yii::$app->pdate->jdate('Y/m/d-h:i', $model->created_at);

                        return Html::tag('span', $formattedDate, [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => $tooltip,
                        ]);
                    },
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'raw',
                    'value' => function (SharingPage $model) {
                        $tooltip = $model->updated_by
                            ? Yii::t('biDashboard', 'Updated by User ID') . " : {$model->updated_by}"
                            : Yii::t('biDashboard', 'No updater information available');

                        $formattedDate = Yii::$app->pdate->jdate('Y/m/d-h:i', $model->updated_at);

                        return Html::tag('span', $formattedDate, [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => $tooltip,
                        ]);
                    },
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => function ($action, SharingPage $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    },
                    'template' => '{update} {delete} {expire}',
                    'visibleButtons' => [
                        'expire' => function (SharingPage $model) {
                            return ($model->isExpire());
                        },
                    ],
                    'buttons' => [
                        'delete' => function ($url, SharingPage $model, $key) {
                            return Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0)', [
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('biDashboard', 'Delete'),
                                'data-reload-pjax-container' => 'p-jax-sharing-page',
                                'data-pjax' => '0',
                                'data-url' => Url::to(['/bidashboard/sharing-page/delete', 'id' => $model->id]),
                                'class' => 'p-jax-btn text-danger p-0',
                                'data-title' => Yii::t('yii', 'Delete'),
                                'data-toggle' => 'tooltip',
                            ]);
                        },
                        'update' => function ($url, SharingPage $model, $key) {
                            return Html::a('<i class="fa fa-pen"></i>', "javascript:void(0)",
                                [
                                    'data-pjax' => '0',
                                    'class' => "btn text-primary p-0",
                                    'data-size' => 'modal-md',
                                    'data-title' => Yii::t('biDashboard', 'update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax-bi',
                                    'data-url' => Url::to(['sharing-page/update', 'id' => $model->id]),
                                    'data-handle-form-submit' => 1,
                                    'data-reload-pjax-container' => 'p-jax-sharing-page'
                                ]
                            );
                        },
                        'expire' => function ($url, SharingPage $model, $key) {
                            return Html::a('<i class="fa fa-times-circle"></i>', 'javascript:void(0)', [
                                'title' => Yii::t('biDashboard', 'Expired'),
                                'aria-label' => Yii::t('biDashboard', 'Expired'),
                                'data-reload-pjax-container' => 'p-jax-sharing-page',
                                'data-pjax' => '0',
                                'data-url' => Url::to(['/bidashboard/sharing-page/expire', 'id' => $model->id]),
                                'class' => 'p-jax-btn btn-sm text-info p-0',
                                'data-title' => Yii::t('biDashboard', 'Expired'),
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
    <?php Pjax::end(); ?>
</div>