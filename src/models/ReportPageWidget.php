<?php

namespace sadi01\bidashboard\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "report_page_widget".
 *
 * @property int $id
 * @property int $page_id
 * @property int $widget_id
 * @property string|null $report_widget_field
 * @property int|null $report_widget_field_format
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int|null $updated_by
 * @property int|null $created_by
 *
 * @property ReportPage $page
 * @property ReportWidget $widget
 */
class ReportPageWidget extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 2;
    const FORMAT_NUMBER = 1;
    const FORMAT_CURRENCY = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_page_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_id', 'widget_id'], 'required'],
            [['page_id', 'widget_id', 'report_widget_field_format', 'status'], 'integer'],
            [['report_widget_field'], 'string', 'max' => 64],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportPage::class, 'targetAttribute' => ['page_id' => 'id']],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportWidget::class, 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'page_id' => Yii::t('app', 'Page ID'),
            'widget_id' => Yii::t('app', 'Widget ID'),
            'report_widget_field' => Yii::t('app', 'Report Widget Field'),
            'report_widget_field_format' => Yii::t('app', 'Report Widget Field Format'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_by' => Yii::t('app', 'Created By'),
        ];
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery|ReportPageQuery
     */
    public function getPage()
    {
        return $this->hasOne(ReportPage::class, ['id' => 'page_id']);
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
     * @return ReportPageWidgetQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ReportPageWidgetQuery(get_called_class());
        $query->notDeleted();
        return $query;
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

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
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

    public function canDelete()
    {
        return true;
    }

}
