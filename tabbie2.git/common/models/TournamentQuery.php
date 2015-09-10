<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 */
class TournamentQuery extends ActiveQuery
{

	public function tournament($id, $table = null)
	{
		$column = (($table != null) ? $table . "." : "") . "tournament_id";
		return $this->andWhere([$column => $id]);
	}

}
