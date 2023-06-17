<?php

namespace sadi01\bidashboard\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "report_page".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int|null $range_type
 * @property string|null $add_on
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int|null $updated_by
 * @property int|null $created_by
 *
 * @property \sadi01\bidashboard\models\ReportPageWidget $reportPageWidgets
 *
 * @mixin SoftDeleteBehavior
 */
class ReportPage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 2;
    const RANGE_DAY = 1;
    const RANGE_MONTH = 2;

    public static function tableName()
    {
        return 'report_page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['status', 'range_type', 'created_at', 'updated_at', 'deleted_at', 'updated_by', 'created_by'], 'integer'],
            [['add_on'], 'safe'],
            [['title'], 'string', 'max' => 128],
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
            'status' => Yii::t('biDashboard', 'Status'),
            'range_type' => Yii::t('biDashboard', 'Range Type'),
            'add_on' => Yii::t('biDashboard', 'Add On'),
            'created_at' => Yii::t('biDashboard', 'Created At'),
            'updated_at' => Yii::t('biDashboard', 'Updated At'),
            'deleted_at' => Yii::t('biDashboard', 'Deleted At'),
            'updated_by' => Yii::t('biDashboard', 'Updated By'),
            'created_by' => Yii::t('biDashboard', 'Created By'),
            'report_widget_field_format' => Yii::t('biDashboard', 'Report Widget Field Format'),
        ];
    }

    /**
     * Gets query for [[ReportPageWidgets]].
     *
     * @return ActiveQuery|ReportPageWidgetQuery
     */
    public function getReportPageWidgets()
    {
        return $this->hasMany(\sadi01\bidashboard\models\ReportPageWidget::class, ['page_id' => 'id']);
    }
    public function getWidgets()
    {
        return $this->hasMany(ReportWidget::class, ['id' => 'widget_id'])
            ->viaTable('report_page_widget', ['page_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ReportPageQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportPageQuery(get_called_class());
        $query->notDeleted();
        return $query;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'range_type' => [
                self::RANGE_DAY => Yii::t('biDashboard', 'روزانه'),
                self::RANGE_MONTH => Yii::t('biDashboard', 'ماهانه'),
            ],
            'Status' => [
                self::STATUS_DELETED => Yii::t('biDashboard', 'DELETED'),
                self::STATUS_ACTIVE => Yii::t('biDashboard', 'ACTIVE'),
                self::STATUS_INACTIVE => Yii::t('biDashboard', 'INACTIVE'),
            ],
            'StatusClass' => [
                self::STATUS_DELETED => 'danger',
                self::STATUS_ACTIVE => 'success',
                self::STATUS_INACTIVE => 'warning',
            ],
            'StatusColor' => [
                self::STATUS_DELETED => '#ff5050',
                self::STATUS_ACTIVE => '#04AA6D',
                self::STATUS_INACTIVE => '#eea236',
            ],];
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

    public function canDelete()
    {
        return true;
    }
}