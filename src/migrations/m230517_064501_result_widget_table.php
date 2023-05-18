<?php


use yii\db\Migration;


/**
 * Handles the creation of table `{{%widget}}`.
 */
class m230517_064501_result_widget_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%widget_result}}', [
            'id' => $this->primaryKey()->unsigned(),
            'result' => $this->json(),
            'widget_id' => $this->integer()->unsigned()->notNull(),
            'start_at' => $this->integer()->unsigned()->notNull(),
            'end_at' => $this->integer()->unsigned()->notNull(),
            'status' => $this->integer()->unsigned()->notNull(),
            'parameters' => $this->json(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->unsigned()->notNull(),
            'update_by' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->createIndex('widget_result_ibfk_1', '{{%widget_result}}', ['widget_id']);
        $this->addForeignKey(
            'widget_result_ibfk_1',
            '{{%widget_result}}',
            ['widget_id'],
            '{{%widget}}',
            ['id'],
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%widget_result}}');
    }
}
