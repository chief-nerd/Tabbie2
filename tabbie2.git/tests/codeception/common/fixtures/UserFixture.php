<?php

namespace tests\codeception\common\fixtures;

use yii\test\ActiveFixture;

/**
 * User fixture
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'common\models\User';

	public function beforeLoad()
	{
		parent::beforeLoad();
		$this->db->createCommand()->setSql('SET FOREIGN_KEY_CHECKS = 0')->execute();
	}

	public function afterLoad()
	{
		parent::afterLoad();
		$this->db->createCommand()->setSql('SET FOREIGN_KEY_CHECKS = 1')->execute();
	}
}
