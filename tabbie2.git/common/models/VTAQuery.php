<?php

namespace common\models;


/**
 * VTAQuery
 * V ... Venue
 * T ... Team
 * A ... Adjudicator
 */
class VTAQuery extends TournamentQuery {

	public function active($state = true) {
		return $this->andWhere(['active' => $state]);
	}

}
