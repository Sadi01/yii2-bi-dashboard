<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%report_box_report_widget}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%report_box}}`
 * - `{{%report_widget}}`
 */
class m240113_102237_create_junction_table_for_report_box_and_report_widget_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%report_box_widget}}', [
            'id' => $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'),
            'slave_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(128),
            'box_id' => $this->integer()->unsigned()->notNull(),
            'widget_id' => $this->integer()->unsigned()->notNull(),
            'widget_field' => $this->string(64),
            'widget_field_format' => $this->tinyInteger(),
            'widget_card_color' => $this->tinyInteger(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'PRIMARY KEY(id, slave_id)',
        ]);

        // creates index for column `box_id`
        $this->createIndex(
            '{{%idx-report_box_widget-box_id}}',
            '{{%report_box_widget}}',
            'box_id'
        );

        // add foreign key for table `{{%report_box}}`
        $this->addForeignKey(
            '{{%fk-report_box_widget-box_id}}',
            '{{%report_box_widget}}',
            'box_id',
            '{{%report_box}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `widget_id`
        $this->createIndex(
            '{{%idx-report_box_widget-widget_id}}',
            '{{%report_box_widget}}',
            'widget_id'
        );

        // add foreign key for table `{{%report_widget}}`
        $this->addForeignKey(
            '{{%fk-report_box_widget-widget_id}}',
            '{{%report_box_widget}}',
            'widget_id',
            '{{%report_widget}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%report_box}}`
        $this->dropForeignKey(
            '{{%fk-report_box_widget-box_id}}',
            '{{%report_box_widget}}'
        );

        // drops index for column `box_id`
        $this->dropIndex(
            '{{%idx-report_box_widget-box_id}}',
            '{{%report_box_widget}}'
        );

        // drops foreign key for table `{{%report_widget}}`
        $this->dropForeignKey(
            '{{%fk-report_box_widget-widget_id}}',
            '{{%report_box_widget}}'
        );

        // drops index for column `widget_id`
        $this->dropIndex(
            '{{%idx-report_box_widget-widget_id}}',
            '{{%report_box_widget}}'
        );

        $this->dropTable('{{%report_box_widget}}');
    }
}
