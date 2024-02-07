<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%report_box}}`.
 */
class m240113_100847_create_report_box_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%report_box}}', [
            'id' => $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'),
            'slave_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(128)->notNull(),
            'description' => $this->string(255),
            'dashboard_id' => $this->integer()->unsigned()->notNull(),
            'display_type' => $this->tinyInteger()->notNull(),
            'chart_type' => $this->tinyInteger(),
            'range_type' => $this->tinyInteger(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'PRIMARY KEY(id, slave_id)',
        ]);

        // creates index for column `dashboard_id`
        $this->createIndex(
            '{{%idx-report_box-dashboard-id}}',
            '{{%report_box}}',
            'dashboard_id'
        );

        // add foreign key for table `{{%report_dashboard}}`
        $this->addForeignKey(
            '{{%fk-report_box-dashboard_id}}',
            '{{%report_box}}',
            'dashboard_id',
            '{{%report_dashboard}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%report_dashboard}}`
        $this->dropForeignKey(
            '{{%fk-report_box-dashboard_id}}',
            '{{%report_box}}'
        );

        // drops index for column `dashboard_id`
        $this->dropIndex(
            '{{%idx-report_box-dashboard_id}}',
            '{{%report_box}}'
        );

        $this->dropTable('{{%report_box}}');
    }
}
