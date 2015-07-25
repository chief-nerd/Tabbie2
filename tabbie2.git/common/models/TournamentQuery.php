<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 */
class TournamentQuery extends ActiveQuery
{

	public function tournament($id)
	{
		return $this->andWhere(["tournament_id" => $id]);
	}

}
