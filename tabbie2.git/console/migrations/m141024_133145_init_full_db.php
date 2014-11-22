<?php

use yii\db\Schema;
use yii\db\Migration;

class m141024_133145_init_full_db extends Migration {

    public function up() {
        $filename = Yii::getAlias("@app") . "/../../schema/BasicStructure.sql";
        if (file_exists($filename)) {
            $sql = file_get_contents($filename, "r");
            $this->execute($sql);
            return true;
        } else
            echo "ERROR: $filename doesn't exist!";

        return false;
    }

    public function down() {
        echo "m141024_133145_init_full_db cannot be reverted.\n";

        return false;
    }

}
