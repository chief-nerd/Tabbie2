<?php

use yii\db\Schema;
use yii\db\Migration;

class m141025_135958_imageChange extends Migration {

    public function up() {
        $this->execute("ALTER TABLE `tabbie`.`tournament`
CHANGE COLUMN `logo` `logo` VARCHAR(255) NULL DEFAULT NULL ");
    }

    public function down() {
        $this->execute("ALTER TABLE `tabbie`.`tournament`
CHANGE COLUMN `logo` `logo` BLOB NULL DEFAULT NULL ");
    }

}
