<?php

namespace common\models;

use yii\db\ActiveQuery;

class VenueQuery extends ActiveQuery {

    public function active($state = true) {
        return $this->andWhere(['active' => $state]);
    }

    public function tournament($id) {
        return $this->andWhere(["tournament_id" => $id]);
    }

}
