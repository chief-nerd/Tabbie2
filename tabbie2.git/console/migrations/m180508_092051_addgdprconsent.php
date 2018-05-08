<?php

use yii\db\Migration;

class m180508_092051_addgdprconsent extends Migration
{
	public function up()
	{
		$this->addColumn('user', 'gdprconsent', $this->integer()."(11) DEFAULT 0");
	}

	public function down()
	{
		$this->dropColumn('user', 'gdprconsent');
	}
}
