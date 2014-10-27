<?php

use yii\db\Schema;
use yii\db\Migration;

class m141027_111904_activeRoom extends Migration {

    public function up() {
        $this->execute("ALTER TABLE `venue`
ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `name`");
    }

    public function down() {
        $this->dropColumn("venue", "active");

        return false;
    }

}
