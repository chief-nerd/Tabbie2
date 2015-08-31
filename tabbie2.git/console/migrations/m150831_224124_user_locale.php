<?php

use yii\db\Migration;

class m150831_224124_user_locale extends Migration
{
	public function up()
	{
		$this->addColumn("user", "language", "VARCHAR(10) NOT NULL DEFAULT 'en-UK'");
	}

	public function down()
	{
		$this->dropColumn("user", "language");
	}
}
