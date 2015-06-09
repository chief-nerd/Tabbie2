<?php

use yii\db\Schema;
use yii\db\Migration;

class m150608_134237_accessUrl extends Migration {

	// Use safeUp/safeDown to run migration code within a transaction
	public function safeUp() {
		$this->addColumn("tournament", "accessToken", "varchar(100) NOT NULL");
		$tournaments = \common\models\Tournament::find()->all();
		foreach ($tournaments as $t) {
			$t->generateAccessToken();
			$t->save();
		}
	}

	public function safeDown() {
		$this->dropColumn("tournament", "accessToken");
	}

}
