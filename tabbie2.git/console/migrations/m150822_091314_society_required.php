<?php

use yii\db\Schema;
use yii\db\Migration;

class m150822_091314_society_required extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `society`
			CHANGE COLUMN `fullname` `fullname` VARCHAR(255) NOT NULL COMMENT '' ,
			CHANGE COLUMN `abr` `abr` VARCHAR(45) NOT NULL COMMENT '' ;
			");
	}

	public function down()
	{
		$this->execute("ALTER TABLE `society`
			CHANGE COLUMN `fullname` `fullname` VARCHAR(255) DEFAULT NULL COMMENT '' ,
			CHANGE COLUMN `abr` `abr` VARCHAR(45) DEFAULT NULL COMMENT '' ;
			");
	}
}
