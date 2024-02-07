<?php

namespace sadi01\bidashboard\models;

use sadi01\bidashboard\traits\AjaxValidationTrait;
use sadi01\bidashboard\traits\CoreTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use sadi01\bidashboard\models\ReportBoxWidgets;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;

/**
 * This is the model class for table "report_dashboard".
 *
 * @property int $id
 * @property int $slave_id
 * @property string $title
 * @property string $description
 * @property int $dashboard_id
 * @property int $display_type
 * @property int $chart_type
 * @property int $range_type
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int $updated_by
 * @property int $created_by
 *
 * @property ReportBoxWidgets $boxWidgets
*
 * @mixin SoftDeleteBehavior
*/


class ReportBox extends ActiveRecord
{
    use AjaxValidationTrait;
    use CoreTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 2;

    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const DISPLAY_CARD = 1;
    const DISPLAY_CHART = 2;
    const DISPLAY_TABLE = 3;

    const CHART_LINE = 1;
    const CHART_COLUMN = 2;
    const CHART_PIE = 3;
    const CHART_AREA = 4;
    const CHART_WORD_CLOUD = 5;

    const FORMAT_NUMBER = 1;
    const FORMAT_CURRENCY = 2;

    public $chartCategories = [];
    public $chartSeries = [];
    public $rangeDateCount = 12;

    const RANGE_TYPE_DAILY = 1;
    const RANGE_TYPE_MONTHLY = 2;

    public static function getDb()
    {
        return Yii::$app->biDB;
    }

    public static function tableName()
    {
        return '{{%report_box}}';
    }

    public function rules()
    {
        return [
            [['dashboard_id', 'display_type', 'range_type', 'title'], 'required', 'on' => self::SCENARIO_CREATE],
            [['dashboard_id', 'display_type', 'title'], 'required', 'on' => self::SCENARIO_UPDATE],

            [['title'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 255],

            [['slave_id'], 'default', 'value' => function () {
                return Yii::$app->params['bi_slave_id'] ?? null;
            }],
            ['chart_type', 'required', 'when' => function($model) {
                return $model->display_type == self::DISPLAY_CHART;
            }],
            [['dashboard_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportDashboard::class, 'targetAttribute' => ['dashboard_id' => 'id']],
            [['dashboard_id', 'display_type', 'chart_type', 'status', 'created_at', 'updated_at', 'deleted_at', 'updated_by', 'created_by', 'slave_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['dashboard_id', 'display_type', 'range_type', 'title', 'description', 'slave_id', 'chart_type'],
            self::SCENARIO_CREATE => ['dashboard_id', 'display_type', 'range_type', 'title', 'description', 'slave_id', 'chart_type'],
            self::SCENARIO_UPDATE => ['dashboard_id', 'display_type', 'title', 'description', 'slave_id', 'chart_type'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => Yii::t('biDashboard', 'ID'),
            'title' => Yii::t('biDashboard', 'Title'),
            'description' => Yii::t('biDashboard', 'Description'),
            'dashboard_id' => Yii::t('biDashboard', 'Dashboard ID'),
            'display_type' => Yii::t('biDashboard', 'Display Type'),
            'chart_type' => Yii::t('biDashboard', 'Chart Type'),
            'range_type' => Yii::t('biDashboard', 'Range Type'),
            'status' => Yii::t('biDashboard', 'Status'),
            'created_at' => Yii::t('biDashboard', 'Created At'),
            'updated_at' => Yii::t('biDashboard', 'Updated At'),
            'deleted_at' => Yii::t('biDashboard', 'Deleted At'),
            'updated_by' => Yii::t('biDashboard', 'Updated By'),
            'created_by' => Yii::t('biDashboard', 'Created By'),
        ];
    }

    public function canDelete()
    {
        return true;
    }

    public function getBoxWidgets()
    {
        return $this->hasMany(ReportBoxWidgets::class, ['box_id' => 'id']);
    }

    /**
     *
     * @return \yii\db\ActiveQuery|ReportDashboardQuery
     */
    public function getDashboard()
    {
        return $this->hasOne(ReportDashboard::class, ['id' => 'dashboard_id']);
    }

    public static function find()
    {
        $query = new ReportBoxQuery(get_called_class());
        return $query->bySlaveId()->notDeleted();
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'Status' => [
                self::STATUS_ACTIVE => Yii::t('biDashboard', 'Active'),
                self::STATUS_INACTIVE => Yii::t('biDashboard', 'InActive'),
                self::STATUS_DELETED => Yii::t('biDashboard', 'Deleted'),
            ],
            'DisplayTypes' => [
                self::DISPLAY_CHART => Yii::t('biDashboard', 'Chart'),
                self::DISPLAY_TABLE => Yii::t('biDashboard', 'Table'),
                self::DISPLAY_CARD => Yii::t('biDashboard', 'Card'),
            ],
            'ChartTypes' => [
                self::CHART_LINE => 'line',
                self::CHART_COLUMN => 'column',
                //self::CHART_PIE => 'pie',
                self::CHART_AREA => 'area',
                //self::CHART_WORD_CLOUD => 'worldcloud',
            ],
            'ChartNames' => [
                self::CHART_LINE => Yii::t('biDashboard', 'Chart line'),
                self::CHART_COLUMN => Yii::t('biDashboard', 'Chart column'),
                //self::CHART_PIE => Yii::t('biDashboard', 'Chart pie'),
                self::CHART_AREA => Yii::t('biDashboard', 'Chart area'),
                //self::CHART_WORD_CLOUD => Yii::t('biDashboard', 'Chart world cloud'),
            ],
            'RangeType' => [
                self::RANGE_TYPE_DAILY => Yii::t('biDashboard', 'روزانه'),
                self::RANGE_TYPE_MONTHLY => Yii::t('biDashboard', 'ماهانه'),
            ],
            'Format' => [
                self::FORMAT_CURRENCY => Yii::t('biDashboard', 'Currency'),
                self::FORMAT_NUMBER => Yii::t('biDashboard', 'Number'),
            ],
        ];

        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function getChartCategories($year, $month): Array {

        $categories = [];
        $pdate = Yii::$app->pdate;
        $monthDaysCount = count($this->getMonthDays($year . '/' . $month));

        if ($this->range_type == self::RANGE_TYPE_DAILY){
            for($i = 1; $i<= $monthDaysCount; $i++ ){
                $categories[] = $i;
            }
        }
        elseif($this->range_type == self::RANGE_TYPE_MONTHLY){
            for($i = 1; $i <= 12; $i++){
                $categories[] = $pdate->jdate_words(['mm' => $i])['mm'];
            }
        }

        return $categories;
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
                'replaceRegularDelete' => false,
                'invokeDeleteEvents' => true
            ],
        ];
    }

}