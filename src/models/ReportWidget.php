<?php

namespace sadi01\bidashboard\models;

use sadi01\bidashboard\behaviors\Jsonable;
use sadi01\bidashboard\components\Pdate;
use sadi01\bidashboard\traits\CoreTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "report_widget".
 *
 * @property int $id
 * @property int $slave_id
 * @property string $title
 * @property string|null $description
 * @property string|null $search_model_class
 * @property string $search_model_method
 * @property int $status
 * @property int $deleted_at
 * @property string|null $search_model_run_result_view
 * @property int|null $range_type
 * @property int|null $visibility
 * @property string|null $add_on
 * @property int $updated_at
 * @property int $created_at
 * @property int $updated_by
 * @property int $created_by
 * @property string $search_route
 * @property string $search_model_form_name
 *
 * @property string $outputColumnTitle
 * @property ReportPage[] $reportPages
 * @property ReportPageWidget[] $reportPageWidgets
 * @property ReportWidgetResult[] $reportWidgetResults
 *
 * @mixin SoftDeleteBehavior
 * @mixin BlameableBehavior
 * @mixin TimestampBehavior
 */
class ReportWidget extends ActiveRecord
{
    use CoreTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const RANGE_TYPE_DAILY = 1;
    const RANGE_TYPE_MONTHLY = 2;
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PRIVATE = 2;

    public $params;
    public $outputColumn;

    const SCENARIO_UPDATE = 'update';

    public static function getDb()
    {
        return Yii::$app->biDB;
    }

    public static function tableName()
    {
        return '{{%report_widget}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slave_id'], 'default', 'value' => function () {
                return Yii::$app->params['bi_slave_id'] ?? null;
            }],
            [['title', 'search_model_method', 'search_model_class', 'search_route', 'range_type'], 'required'],
            [['title'], 'required', 'on' => $this::SCENARIO_UPDATE],
            [['description'], 'safe', 'on' => $this::SCENARIO_UPDATE],
            ['search_model_method', 'validateSearchModelMethod'],
            [['status', 'deleted_at', 'range_type', 'visibility', 'updated_at', 'created_at', 'updated_by', 'created_by', 'slave_id'], 'integer'],
            [['add_on', 'search_model_class', 'params', 'outputColumn'], 'safe'],
            [['title', 'search_model_method', 'search_model_run_result_view', 'search_route', 'search_model_form_name'], 'string', 'max' => 128],
            [['description', 'search_model_class'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('biDashboard', 'ID'),
            'title' => Yii::t('biDashboard', 'Title'),
            'description' => Yii::t('biDashboard', 'Description'),
            'search_model_class' => Yii::t('biDashboard', 'Search Model Class'),
            'search_model_method' => Yii::t('biDashboard', 'Search Model Method'),
            'status' => Yii::t('biDashboard', 'Status'),
            'deleted_at' => Yii::t('biDashboard', 'Deleted At'),
            'search_model_run_result_view' => Yii::t('biDashboard', 'Search Model Run Result View'),
            'range_type' => Yii::t('biDashboard', 'Range Type'),
            'visibility' => Yii::t('biDashboard', 'Visibility'),
            'add_on' => Yii::t('biDashboard', 'Add On'),
            'updated_at' => Yii::t('biDashboard', 'Updated At'),
            'created_at' => Yii::t('biDashboard', 'Created At'),
            'updated_by' => Yii::t('biDashboard', 'Updated By'),
            'created_by' => Yii::t('biDashboard', 'Created By'),
            'search_route' => Yii::t('biDashboard', 'Search Route'),
            'search_model_form_name' => Yii::t('biDashboard', 'Search Model Form Name'),
        ];
    }

    public function validateSearchModelMethod($attribute, $params, $validator)
    {
        if ($this->search_model_class) {
            $searchModel = new ($this->search_model_class);
            $methodExists = method_exists($searchModel, $this->search_model_method);
            if ($methodExists) {
                $reflection = new \ReflectionMethod($searchModel, $this->search_model_method);
                $parameters = $reflection->getParameters();
                if (count($parameters) <= 3) {
                    $this->addError('search_model_method', 'The input parameters of the function are invalid');
                }
            } else {
                $this->addError('search_model_method', 'function in search model not exists');
            }
        }
    }

    /**
     * Gets query for [[ReportPages]].
     *
     * @return ActiveQuery|ReportPageQuery
     */
    public function getReportPages()
    {
        return $this->hasMany(ReportPage::class, ['widget_id' => 'id']);
    }

    /**
     * Gets query for [[ReportPageWidget]].
     *
     * @return ActiveQuery|ReportPageWidgetQuery
     */
    public function getReportPageWidgets()
    {
        return $this->hasMany(ReportPageWidget::class, ['widget_id' => 'id']);
    }

    /**
     * Gets query for [[ReportWidgetResults]].
     *
     * @return ActiveQuery|ReportWidgetResultQuery
     */
    public function getReportWidgetResults()
    {
        return $this->hasMany(ReportWidgetResult::class, ['widget_id' => 'id']);
    }

    public function getOutputColumnTitle(string $field): string
    {
        foreach ($this->add_on['outputColumn'] as $column) {
            if ($column['column_name'] == $field) {
                return $column["column_title"];
            }
        }

        /**@var $searchModel ActiveRecord */
        $searchModel = new ($this->search_model_class);

        return $searchModel->getAttributeLabel($field);
    }

    /**
     * @param $id
     * @param $start_range
     * @param $end_rage
     * @throws \Exception
     * @var $pDate Pdate
     * @var $pDate Pdate
     */
    public function runWidget($start_range = null, $end_range = null)
    {
        $timestamp_date_range = $this->getTimeStampDateRange($start_range, $end_range);

        $modelQueryResults = $this->findAndRunSearchModelWidget($timestamp_date_range['start_range'], $timestamp_date_range['end_range']);

        $isValid = $this->validateRunWidgetResult($modelQueryResults);

        if (!$isValid) {
            $this->addError('status', Yii::t('app', 'Error In Run Widget'));
            return false;
        }

        return $this->createReportWidgetResult($modelQueryResults, $start_range, $end_range);
    }

    public function validateRunWidgetResult($modelQueryResults)
    {
        if (!$modelQueryResults) {
            $isValid = true;
        } elseif ($this->range_type == $this::RANGE_TYPE_DAILY) {
            $isValid = key_exists('day', $modelQueryResults[0]) && key_exists('month', $modelQueryResults[0]) && key_exists('year', $modelQueryResults[0]);
        } elseif ($this->range_type == $this::RANGE_TYPE_MONTHLY) {
            $isValid = key_exists('month', $modelQueryResults[0]) && key_exists('year', $modelQueryResults[0]);
        } else {
            $isValid = false;
        }

        return $isValid;
    }

    public function getTimeStampDateRange($start_range = null, $end_range = null)
    {
        $pDate = Yii::$app->pdate;

        if ($start_range && $end_range) {
            if (gettype($start_range) != 'integer') {
                if ($this->range_type == $this::RANGE_TYPE_DAILY) {
                    $start_range = $pDate->jmktime('', '', '', $start_range['mon'], $start_range['day'], $start_range['year']);
                    $end_range = $pDate->jmktime('', '', '', $end_range['mon'], $end_range['day'], $end_range['year']);
                } else {
                    $start_range = $this->getStartAndEndOfMonth($start_range['year'] . "/" . $start_range['mon'])['start'];
                    $end_range = $this->getStartAndEndOfMonth($end_range['year'] . "/" . $end_range['mon'])['end'];
                }
            }
        } else {
            if ($this->range_type == $this::RANGE_TYPE_DAILY) {
                $dateTemp = $this->getStartAndEndOfMonth();
            } else {
                $dateTemp = $this->getStartAndEndOfYear();
            }
            $start_range = $dateTemp['start'];
            $end_range = $dateTemp['end'];
        }

        return [
            'start_range' => $start_range,
            'end_range' => $end_range,
        ];
    }

    public function findAndRunSearchModelWidget($startDate, $endDate)
    {
        $params = $this->params;
        $searchModel = new ($this->search_model_class);
        $methodExists = method_exists($searchModel, $this->search_model_method);
        if ($methodExists) {
            $dataProvider = $searchModel->{$this->search_model_method}($params, $this->range_type, $startDate, $endDate);
            $modelQueryResults = $dataProvider->query->asArray()->all();
        } else {
            $modelQueryResults = null;
        }
        return $modelQueryResults;
    }

    public function getModelRoute()
    {
        $modelRoute = "/" . $this->search_route . "?";
        $modalRouteParams = "";
        foreach ($this->params as $key => $param) {
            if (is_array($param)) {
                foreach ($param as $each) {
                    $modalRouteParams .= $this->search_model_form_name . "[" . $key . "][]=" . $each . "&";
                }
            } else {
                $modalRouteParams .= $this->search_model_form_name . "[" . $key . "]=" . $param . "&";
            }
        }
        $modelRoute .= $modalRouteParams;
        return $modelRoute;
    }

    public function lastResult($startRange = null, $endRange = null)
    {
        if (!$startRange || !$endRange) {
            if ($this->range_type == $this::RANGE_TYPE_DAILY) {
                $dateTemp = $this->getStartAndEndOfMonth();
            } else {
                $dateTemp = $this->getStartAndEndOfYear();
            }
            $startRange = $dateTemp['start'];
            $endRange = $dateTemp['end'];
        }
        $runWidget = ReportWidgetResult::find()
            ->where(['widget_id' => $this->id])
            ->andWhere(['start_range' => $startRange])
            ->andWhere(['end_range' => $endRange])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();

        if (!$runWidget) {
            $runWidget = $this->runWidget($startRange, $endRange);
        }

        return $runWidget;
    }

    public function createReportWidgetResult($modelQueryResults, $start_range, $end_range)
    {
        $reportWidgetResult = new ReportWidgetResult();
        $reportWidgetResult->widget_id = $this->id;
        $reportWidgetResult->start_range = $start_range;
        $reportWidgetResult->end_range = $end_range;
        $reportWidgetResult->run_action = Yii::$app->controller->action->id;
        $reportWidgetResult->run_controller = Yii::$app->controller->id;
        $reportWidgetResult->result = $modelQueryResults;
        $reportWidgetResult->save();

        return $reportWidgetResult;
    }

    public function canDelete()
    {
        return true;
    }

    public function createReportModelClass(): bool
    {
        $reportModelClass = ReportModelClass::find()->where(['model_class' => $this->search_model_class])->limit(1)->one();
        if (!$reportModelClass) {
            $reportModelClass = new ReportModelClass([
                'model_class' => $this->search_model_class,
                'title' => $this->search_model_class,
            ]);
            $reportModelClass->loadDefaultValues();

            return $reportModelClass->save();
        }

        return true;
    }

    public function afterDelete()
    {
        foreach ($this->reportPageWidgets as $item) {
            $item->softDelete();
        }
        return parent::afterDelete();
    }

    /**
     * {@inheritdoc}
     * @return ReportWidgetQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportWidgetQuery(get_called_class());
        return $query->bySlaveId()->notDeleted();
    }

    public static function itemAlias($type, $code = NULL)
    {
        $data = [];
        if ($type == 'List') {
            $data = ArrayHelper::map(ReportWidget::find()->where(['range_type' => $code])->all(), 'id', 'title');
            $code = null;
        }
        $_items = [
            'Status' => [
                self::STATUS_ACTIVE => Yii::t('biDashboard', 'Active'),
                self::STATUS_DELETED => Yii::t('biDashboard', 'Deleted')
            ],
            'RangeTypes' => [
                self::RANGE_TYPE_DAILY => Yii::t('biDashboard', 'Daily'),
                self::RANGE_TYPE_MONTHLY => Yii::t('biDashboard', 'Monthly'),
            ],
            'Visibility' => [
                self::VISIBILITY_PUBLIC => Yii::t('biDashboard', 'Public'),
                self::VISIBILITY_PRIVATE => Yii::t('biDashboard', 'Private'),
            ],
            'List' => $data,
        ];

        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => time(),
                    'status' => self::STATUS_DELETED
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => 0,
                    'status' => self::STATUS_ACTIVE
                ],
                'replaceRegularDelete' => false, // mutate native `delete()` method
                'invokeDeleteEvents' => true
            ],
            'jsonable' => [
                'class' => Jsonable::class,
                'jsonAttributes' => [
                    'add_on' => [
                        'params',
                        'outputColumn',
                    ],
                ],
            ],
        ];
    }
}