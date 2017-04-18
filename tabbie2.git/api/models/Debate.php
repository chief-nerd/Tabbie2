<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Debate
 * @package api\models
 */
class Debate extends \common\models\Debate implements Linkable
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
		$fields = parent::fields();

		// remove fields that contain sensitive information
		unset(
            $fields['og_team_id'],
            $fields['oo_team_id'],
            $fields['cg_team_id'],
            $fields['co_team_id'],
            $fields['og_feedback'],
            $fields['oo_feedback'],
            $fields['cg_feedback'],
            $fields['co_feedback'],
            $fields['panel_id'],
            $fields['venue_id'],
            $fields['messages'],
            $fields['time'] // This field doesn't seem to hold data about the debate but when the model was changed?
		);

		$fields['venue'] = function ($model) {
			return $model->venue->name;
		};

		$fields['round_info'] = function ($model) {
			return [
			    "prep_start" => $model->round->prep_started,
                "closed" => $model->round->closed,
                "motion" => $model->round->motion,
                "infoslide" => $model->round->infoslide,
                "round_number" => $model->round->label
            ];
		};

		$fields['participants'] = function ($model) {
		    $participants = [];
            $teams = ['og_team', 'oo_team', 'cg_team', 'co_team'];
            $positions = ['speakerA', 'speakerB'];

            foreach ($teams as $t) {
                foreach ($positions as $p) {
                    array_push($participants, [
                        "userId" => $model[$t][$p . "_id"],
                        "givenName" => $model[$t][$p]->givenname,
                        "sureName" => $model[$t][$p]->surename,
                        "position" => $t
                    ]);
                }
            }

			return $participants;
		};

		return $fields;
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => [
				"api" => Url::to(['debate/view', "id" => $this->id], true),
				"web" => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['debate/view', "id" => $this->id])
			],
		];

		return $links;
	}

}
