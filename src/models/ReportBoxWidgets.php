<?php

namespace sadi01\bidashboard\models;

use sadi01\bidashboard\helpers\CoreHelper;
use sadi01\bidashboard\models\ReportWidget;
use sadi01\bidashboard\models\ReportBox;
use sadi01\bidashboard\traits\AjaxValidationTrait;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use sadi01\bidashboard\traits\CoreTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "report_dashboard_widget".
 *
 * @property int $id
 * @property int $slave_id
 * @property int $widget_id
 * @property int $status
 * @property string widget_field
 * @property int widget_field_format
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int|null $updated_by
 * @property int|null $created_by
 *
 * @property  $dashboard
 * @property ReportWidget $widget
 */

class ReportBoxWidgets extends ActiveRecord
{
    use AjaxValidationTrait;
    use CoreTrait;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const FORMAT_NUMBER = 1;
    const FORMAT_CURRENCY = 2;

    const CARD_PRIMARY = 1;
    const CARD_SECONDARY = 2;
    const CARD_SUCCESS = 3;
    const CARD_DANGER = 4;
    const CARD_WARNING = 5;
    const CARD_INFO = 6;
    const CARD_DARK = 7;

    const VALID = 1;
    const IN_VALID = 0;

    public $description;
    public $rangeType;
    public $isValid;
    public $rangeDateCount;
    public $results = [];

    public $cardResultCount;

    public static function getDb()
    {
        return Yii::$app->biDB;
    }

    public static function tableName()
    {
        return '{{%report_box_widget}}';
    }

    public function rules()
    {
        return [
            [['widget_id', 'widget_field', 'widget_field_format'], 'required'],
            [['slave_id'], 'default', 'value' => function () {
                return Yii::$app->params['bi_slave_id'] ?? null;
            }],
            [['title'], 'string', 'max' => 128],
            [['title'], 'default', 'value' => null],
            [['widget_card_color'], 'default', 'value' => null],
            [['widget_field'], 'string', 'max' => 64],
            [['box_id', 'widget_id', 'widget_field_format', 'widget_card_color', 'status', 'slave_id', 'id'], 'integer'],
            [['box_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportBox::class, 'targetAttribute' => ['box_id' => 'id']],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportWidget::class, 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('biDashboard', 'ID'),
            'box_id' => Yii::t('biDashboard', 'Box ID'),
            'title' => Yii::t('biDashboard', 'Title'),
            'widget_id' => Yii::t('biDashboard', 'Widget'),
            'widget_field' => Yii::t('biDashboard', 'Report Widget Field'),
            'widget_field_format' => Yii::t('biDashboard', 'Report Widget Field Format'),
            'widget_card_color' => Yii::t('biDashboard', 'Card Color'),
            'status' => Yii::t('biDashboard', 'Status'),
            'created_at' => Yii::t('biDashboard', 'Created At'),
            'updated_at' => Yii::t('biDashboard', 'Updated At'),
            'deleted_at' => Yii::t('biDashboard', 'Deleted At'),
            'updated_by' => Yii::t('biDashboard', 'Updated By'),
            'created_by' => Yii::t('biDashboard', 'Created By'),
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery|ReportBoxQuery
     */
    public function getBox()
    {
        return $this->hasOne(ReportBox::class, ['id' => 'box_id']);
    }

    /**
     * Gets query for [[Widget]].
     *
     * @return \yii\db\ActiveQuery|ReportWidgetQuery
     */
    public function getWidget()
    {
        return $this->hasOne(ReportWidget::class, ['id' => 'widget_id']);
    }

    /**
     * {@inheritdoc}
     * @return ReportBoxWidgetQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportBoxWidgetQuery(get_called_class());
        return $query->bySlaveId()->notDeleted();
    }

    public function canDelete()
    {
        return true;
    }

    public static function softDeleteAll($condition = null, $params = [])
    {
        $attributes = [
            'deleted_at' => time(),
            'status' => self::STATUS_DELETED,
        ];

        return static::updateAll($attributes, $condition, $params);
    }

    public static function getFormattedValue($format, $value)
    {
        return match ($format) {
            self::FORMAT_NUMBER => Yii::$app->formatter->asInteger($value),
            self::FORMAT_CURRENCY => Yii::$app->formatter->asCurrency($value),
            default => null,
        };
    }

    public function setWidgetProperties() {

        $this->description = $this->widget->description;
        $this->rangeType = $this->widget->range_type;
        $this->rangeDateCount = 12;
        $this->cardResultCount = 0;
        $this->isValid = self::VALID;
    }

    public function getStartAndEndTimestamps($widget, $year, $month, $day) : array
    {
        if ($widget->rangeType == ReportWidget::RANGE_TYPE_DAILY) {

            if ($widget->box->display_type == ReportBox::DISPLAY_CARD){
                $date_array = CoreHelper::getStartAndEndOfDay(time: $this->jalaliToTimestamp($year.'/'.$month.'/'.$day.' 00:00:00'));
            }
            else {
                $date_array = $this->getStartAndEndOfMonth($year . '/' . $month);
                //check below
                $widget->rangeDateCount = count($this->getMonthDays($year . '/' . $month));
            }
        }
        elseif ($widget->rangeType == ReportWidget::RANGE_TYPE_MONTHLY) {

            if ($widget->box->display_type == ReportBox::DISPLAY_CARD) {
                $date_array = $this->getStartAndEndOfMonth($year . '/' . $month);
            }
            else {
                $date_array = $this->getStartAndEndOfYear($year);
            }
        }

        return $date_array;
    }

    public function collectResults($widget, $results) {

        $pdate = Yii::$app->pdate;

        if ($widget->box->display_type == ReportBox::DISPLAY_CARD){
            foreach ($results as $result){
                if (isset($result[$widget->widget_field]))
                    $widget->cardResultCount += $result[$widget->widget_field];
                else
                    $widget->isValid = self::IN_VALID;
            }
        }
        else {
            $widget->results['data'] = array_map(function ($item) use ($widget) {
                return (int)$item[$widget->widget_field];
            }, $results);

            $widget->results['categories'] = array_map(function ($item) use ($widget) {
                if ($widget->rangeType == ReportWidget::RANGE_TYPE_DAILY) {
                    return $item["day"];
                } else {
                    return $item["month_name"];
                }
            }, $results);

            $widget->results['combine'] = array_combine($widget->results['categories'], $widget->results['data']);

            if ($widget->rangeType == ReportWidget::RANGE_TYPE_DAILY){
                for ($i = 0; $i <= 30; $i++) {
                    if (array_key_exists($i+1, $widget->results['combine'])) {
                        $widget->results['chartData'][$i] = $widget->results['combine'][$i+1];
                    }
                    else {
                        $widget->results['chartData'][$i] = 0;
                    }
                }
            }

            elseif($widget->rangeType == ReportWidget::RANGE_TYPE_MONTHLY){

                for ($i = 0; $i <= 11; $i++){
                    if (array_key_exists($pdate->jdate_words(['mm' => $i+1])['mm'], $widget->results['combine'])){
                        $widget->results['chartData'][$i] = $widget->results['combine'][$pdate->jdate_words(['mm' => $i+1])['mm']];
                    }
                    else {
                        $widget->results['chartData'][$i] = 0;
                    }
                }
            }

        }
    }

    public static function itemAlias($type, $code = NULL)
    {
        $data = [];
        $_items = [
            'Status' => [
                self::STATUS_ACTIVE => Yii::t('biDashboard', 'Active'),
                self::STATUS_DELETED => Yii::t('biDashboard', 'Deleted'),
                self::STATUS_INACTIVE => Yii::t('biDashboard', 'InActive')
            ],
            'Format' => [
                self::FORMAT_CURRENCY => Yii::t('biDashboard', 'Currency'),
                self::FORMAT_NUMBER => Yii::t('biDashboard', 'Number'),
            ],
            'CardColorsClass' => [
                self::CARD_PRIMARY => 'bg-primary',
                self::CARD_SECONDARY => 'bg-secondary',
                self::CARD_SUCCESS => 'bg-success',
                self::CARD_DANGER => 'bg-danger',
                self::CARD_WARNING => 'bg-warning',
                self::CARD_INFO => 'bg-info',
                self::CARD_DARK => 'bg-dark',
            ],
            'CardColorsName' => [
                self::CARD_PRIMARY => Yii::t('biDashboard', 'Blue'),
                self::CARD_SECONDARY => Yii::t('biDashboard', 'Gray'),
                self::CARD_SUCCESS => Yii::t('biDashboard', 'Green'),
                self::CARD_DANGER => Yii::t('biDashboard', 'Red'),
                self::CARD_WARNING => Yii::t('biDashboard', 'Yellow'),
                self::CARD_INFO => Yii::t('biDashboard', 'Turquoise'),
                self::CARD_DARK => Yii::t('biDashboard', 'Black'),
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
                'replaceRegularDelete' => false,
                'invokeDeleteEvents' => false
            ],
        ];
    }

}