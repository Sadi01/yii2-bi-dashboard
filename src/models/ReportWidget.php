<?php

namespace sadi01\bidashboard\models;

use sadi01\bidashboard\behaviors\Jsonable;
use sadi01\bidashboard\components\Pdate;
use sadi01\bidashboard\models\ReportPageQuery;
use sadi01\bidashboard\models\ReportWidgetResultQuery;
use sadi01\bidashboard\traits\CoreTrait;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "report_widget".
 *
 * @property int $id
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
 * @property ReportPage[] $reportPages
 * @property ReportWidgetResult[] $reportWidgetResults
 *
 * @mixin SoftDeleteBehavior
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

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'report_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'search_model_method', 'search_model_class', 'search_route', 'search_model_form_name', 'range_type'], 'required'],
            [['status', 'deleted_at', 'range_type', 'visibility', 'updated_at', 'created_at', 'updated_by', 'created_by'], 'integer'],
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
     * Gets query for [[ReportWidgetResults]].
     *
     * @return ActiveQuery|ReportWidgetResultQuery
     */
    public function getReportWidgetResults()
    {
        return $this->hasMany(ReportWidgetResult::class, ['widget_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ReportWidgetQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportWidgetQuery(get_called_class());
        $query->notDeleted();
        return $query;
    }

    public static function itemAlias($type, $code = NULL)
    {
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
                'invokeDeleteEvents' => false
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


    /**
     * @param $id
     * @param $start_range
     * @param $end_rage
     * @return mixed
     * @throws \Exception
     */
    public function runWidget($id, $start_range = null, $end_range = null)
    {

        $widget = $this;
        /**@var $pDate Pdate */
        $pDate = \Yii::$app->pdate;

        if ($start_range and $end_range) {
            if (gettype($start_range) != 'integer') {
                if ($widget->range_type == $widget::RANGE_TYPE_DAILY) {
                    $start_range = $pDate->jmktime('', '', '', $start_range['mon'], $start_range['day'], $start_range['year']);
                    $end_range = $pDate->jmktime('', '', '', $end_range['mon'], $end_range['day'], $end_range['year']);
                } else {
                    $start_range = $this->getStartAndEndOfMonth($start_range['year'] . "/" . $start_range['mon'])['start'];
                    $end_range = $this->getStartAndEndOfMonth($end_range['year'] . "/" . $end_range['mon'])['end'];
                }
            }
        } else {
            if ($widget->range_type == $widget::RANGE_TYPE_DAILY) {
                $dateTemp = $this->getStartAndEndOfMonth();
            } else {
                $dateTemp = $this->getStartAndEndOfYear();
            }
            $start_range = $dateTemp['start'];
            $end_range = $dateTemp['end'];
        }

        $modelQueryResults = $this->findSearchModelWidget($start_range, $end_range);

        // -- create Report Widget Result
        $reportWidgetResult = new ReportWidgetResult();
        $reportWidgetResult->widget_id = $id;
        $reportWidgetResult->start_range = $start_range;
        $reportWidgetResult->end_range = $end_range;
        $reportWidgetResult->run_action = Yii::$app->controller->action->id;
        $reportWidgetResult->run_controller = Yii::$app->controller->id;
        $reportWidgetResult->result = $modelQueryResults;
        $reportWidgetResult->save();

        return $reportWidgetResult;
    }

    public function findSearchModelWidget($startDate, $endDate)
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
        $params = json_decode($this->params, true);
        foreach ($params as $key => $param) {
            $modalRouteParams .= $this->search_model_form_name . "[" . $key . "]=" . $param . "&";
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
            $start_range = $dateTemp['start'];
            $end_range = $dateTemp['end'];
        }

        $runWidget = ReportWidgetResult::find()
            ->where(['widget_id' => $this->id])
            ->andWhere(['start_range' => $startRange])
            ->andWhere(['end_range' => $endRange])
            ->one();

        if (!$runWidget) {
            $runWidget = $this->runWidget($this->id, $startRange, $endRange);
        }

        return $runWidget;
    }


}