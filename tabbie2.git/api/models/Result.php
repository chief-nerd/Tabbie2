<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Round
 * @package api\models
 */
class Result extends \common\models\Result implements Linkable
{
	/**
	 * @return array
	 */
	public function extraFields()
	{
		$fields = $this->fields();

		return $fields;
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return ['hashtable' =>
			function ($model) {

			$debates = $model->debate->round->debates;
			$hashTable = [];

			foreach($debates as $debate){
				$result = $debate->result;
				$hashTable[] = ['venue' => $debate->venue->name, 'hash' => md5($result->og_A_speaks.$result->og_B_speaks.$result->oo_A_speaks.$result->oo_B_speaks.$result->cg_A_speaks.$result->cg_B_speaks.$result->co_A_speaks.$result->co_B_speaks)];
			}

			return $hashTable;

			}];
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => [
				"api" => Url::to(['result/view', "id" => $this->id], true),
				"web" => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['result/view', "id" => $this->id])
			],
		];

		return $links;
	}

}
