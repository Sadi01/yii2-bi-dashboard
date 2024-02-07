<?php

namespace sadi01\bidashboard\models;

use sadi01\bidashboard\behaviors\Jsonable;
use sadi01\bidashboard\traits\CoreTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "report_widget_result".
 *
 * @property int $id
 * @property int $slave_id
 * @property string|null $add_on
 * @property int $widget_id
 * @property int $start_range
 * @property int $end_range
 * @property string|null $run_controller
 * @property string|null $run_action
 * @property int $status
 * @property int $updated_at
 * @property int $created_at
 * @property int $deleted_at
 * @property int $updated_by
 * @property int $created_by
 *
 * @property ReportWidget $widget
 */
class ReportWidgetResult extends ActiveRecord
{
    use CoreTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const FORMAT_NUMBER = 1;
    const FORMAT_CURRENCY = 2;

    const CHART_LINE = 'line';
    const CHART_COLUMN = 'column';
    const CHART_PIE = 'pie';
    const CHART_AREA = 'area';
    const CHART_WORD_CLOUD = 'wordcloud';

    public $result;

    public static function getDb()
    {
        return Yii::$app->biDB;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%report_widget_result}}';
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
            [['widget_id'], 'required'],
            [['widget_id', 'start_range', 'end_range', 'status', 'updated_at', 'created_at', 'deleted_at', 'updated_by', 'created_by', 'slave_id'], 'integer'],
            [['add_on', 'params'], 'safe'],
            [['run_controller'], 'string', 'max' => 256],
            [['run_action'], 'string', 'max' => 128],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportWidget::class, 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('biDashboard', 'ID'),
            'add_on' => Yii::t('biDashboard', 'Add On'),
            'widget_id' => Yii::t('biDashboard', 'Widget ID'),
            'start_range' => Yii::t('biDashboard', 'Start Range'),
            'end_range' => Yii::t('biDashboard', 'End Range'),
            'run_controller' => Yii::t('biDashboard', 'Run Controller'),
            'run_action' => Yii::t('biDashboard', 'Run Action'),
            'status' => Yii::t('biDashboard', 'Status'),
            'updated_at' => Yii::t('biDashboard', 'Updated At'),
            'created_at' => Yii::t('biDashboard', 'Created At'),
            'deleted_at' => Yii::t('biDashboard', 'Deleted At'),
            'updated_by' => Yii::t('biDashboard', 'Updated By'),
            'created_by' => Yii::t('biDashboard', 'Created By'),
        ];
    }

    /**
     * Gets query for [[Widget]].
     *
     * @return ActiveQuery|ReportWidgetQuery
     */
    public function getWidget()
    {
        return $this->hasOne(ReportWidget::class, ['id' => 'widget_id']);
    }

    /**
     * {@inheritdoc}
     * @return ReportWidgetResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportWidgetResultQuery(get_called_class());
        return $query->bySlaveId()->notDeleted();
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'Chart' => [
                self::CHART_LINE => Yii::t('biDashboard', 'Chart line'),
                self::CHART_COLUMN => Yii::t('biDashboard', 'Chart column'),
                self::CHART_PIE => Yii::t('biDashboard', 'Chart pie'),
                self::CHART_AREA => Yii::t('biDashboard', 'Chart area'),
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
                        'result',
                    ],
                ],
            ],
        ];
    }
}