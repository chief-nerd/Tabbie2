<?php

use yii\db\Schema;
use yii\db\Migration;

class m151225_161516_feedback_wudc extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn("feedback", "from_id", "INTEGER");
        $this->addColumn("question", "help", "VARCHAR(255) DEFAULT NULL");
    }

    public function safeDown()
    {
        $this->dropColumn("feedback", "from_id");
        $this->dropColumn("question", "help");
    }
}
