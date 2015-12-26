<?php

use yii\db\Schema;
use yii\db\Migration;

class m151226_095226_parameter_length extends Migration
{
    public function up()
    {
        $this->alterColumn("question", "param", "TEXT");
    }

    public function down()
    {
        $this->alterColumn("question", "param", "VARCHAR(255)");
    }
}
