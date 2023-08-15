<?php

use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\widgets\grid\ActionColumn;
use sadi01\bidashboard\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var sadi01\bidashboard\models\search\ReportWidgetSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('biDashboard', 'Report Widgets');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'p-jax-report-widget', 'enablePushState' => false]); ?>
    <div class="page-content container-fluid text-left pt-5">
        <div class="work-report-index card">
            <div class="panel-group m-bot20" id="accordion">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                           href="#collapseSearch" aria-expanded="false">
                            <i class="fa fa-search"></i> جستجو
                        </a>
                    </h4>
                </div>

                <div class="card-body">
                    <div id="collapseSearch" class="panel-collapse collapse" aria-expanded="false">
                        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                    </div>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'id',
                            'title',
                            'search_model_class',
                            'search_model_method',
                            [
                                'class' => ActionColumn::class,
                                'urlCreator' => function ($action, ReportWidget $model, $key, $index, $column) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                },
                                'template' => '{view} {delete}',
                                'buttons' => [
                                    'delete' => function ($url, ReportWidget $model, $key) {
                                        return Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0)', [
                                            'title' => Yii::t('biDashboard', 'Delete'),
                                            'aria-label' => Yii::t('biDashboard', 'Delete'),
                                            'data-reload-pjax-container' => 'p-jax-report-widget',
                                            'data-pjax' => '0',
                                            'data-url' => Url::to(['report-widget/delete', 'id' => $model->id]),
                                            'class' => 'p-jax-btn text-danger',
                                            'data-title' => Yii::t('biDashboard', 'Delete'),
                                            'data-toggle' => 'tooltip',
                                        ]);
                                    },
                                    'view' => function ($url, ReportWidget $model, $key) {
                                        return Html::a('<i class="fa fa-eye"></i>', "javascript:void(0)",
                                            [
                                                'data-pjax' => '0',
                                                'class' => "btn text-info p-0",
                                                'data-size' => 'modal-xl',
                                                'data-title' => Yii::t('biDashboard', 'view'),
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal-pjax',
                                                'data-url' => Url::to(['report-widget/view', 'id' => $model->id]),
                                                'data-handle-form-submit' => 1,
                                                'data-reload-pjax-container' => 'p-jax-report-widget'
                                            ]
                                        );
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