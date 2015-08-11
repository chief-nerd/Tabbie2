<?php

return [
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Max Iterations to improve the Adjudicator Allocation"),
		"key"           => "max_iterations",
		"value"         => 20000,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Team and adjudicator in same society penalty"),
		"key"           => "society_strike",
		"value"         => 1000,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Both Adjudicators are clashed"),
		"key"           => "adjudicator_strike",
		"value"         => 1000,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Team with Adjudicator is clashed"),
		"key"           => "team_strike",
		"value"         => 1000,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Adjudicator is not allowed to chair"),
		"key"           => "non_chair",
		"value"         => 1000,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Chair is not perfect at the current situation"),
		"key"           => "chair_not_perfect",
		"value"         => 100,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Adjudicator has seen the team already"),
		"key"           => "judge_met_team",
		"value"         => 50,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Adjudicator has already judged in this combination"),
		"key"           => "judge_met_judge",
		"value"         => 50,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Panel is wrong strength for room"),
		"key"           => "panel_steepness",
		"value"         => 1,
	],
	[
		"tournament_id" => 1,
		"label"         => Yii::t("app", "Adjudicator has already judged in this combination"),
		"key"           => "rich_allocation",
		"value"         => 0,
	],
];
