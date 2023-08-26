<?php

use yii\db\Migration;

/**
 * Class m230826_064950_add_client_id_to_widget
 */
class m230826_064953_add_client_id_to_page_widget extends Migration
{

    public function init()
    {
        $this->db = 'biDB';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('{{%report_page_widget}}', 'bi_client_id', $this->tinyInteger()->unsigned());
        $this->createIndex('idx_unique_report_widget_id_client_id', '{{%report_page_widget}}', ['id', 'bi_client_id'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_unique_report_page_widget_id_client_id', '{{%report_page_widget}}');
        $this->dropColumn('{{%report_page_widget}}', 'bi_client_id');
    }
}
