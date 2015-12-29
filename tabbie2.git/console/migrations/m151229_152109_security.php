<?php

use yii\db\Schema;
use yii\db\Migration;

class m151229_152109_security extends Migration
{
    public function up()
    {
        $this->alterColumn("tournament", "accessToken", "varchar(255)");
    }

    public function down()
    {
        $this->alterColumn("tournament", "accessToken", "varchar(100)");
    }
}
