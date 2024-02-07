<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%report_dashboard}}`.
 */
class m231230_114008_create_report_dashboard_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%report_dashboard}}', [
            'id' => $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'),
            'slave_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(128)->notNull(),
            'description' => $this->string(255)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'PRIMARY KEY(id, slave_id)',
        ]);

    }

    /**
     * {@inheritdoc}
     */

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%report_dashboard}}');
    }
}
