<?php
namespace common\models;

use kartik\helpers\Html;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Login form
 */
class Motion extends Model
{
	public $object;

	public $motion = null;
	public $infoslide = null;
	public $tournament = null;
	public $round = null;
	public $link = null;
	public $date = null;
	public $language = 'en';
	public $tags = [];

	public static function findAllByTags($tags, $limit = false)
	{
		$m = [];
		$rounds = Round::find()
			->where(["displayed" => 1])
			->orderBy(["time" => SORT_DESC]);

		if ($tags)
			$rounds = $rounds->leftJoin("tag", "round_id = round.id")
				->andWhere(["IN", "motion_tag_id", $tags]);

		$rounds = $rounds->limit($limit)->all();

		foreach ($rounds as $r) {
			$m[] = new Motion(["object" => $r]);
		}

		$legacy = LegacyMotion::find()->orderBy(["time" => SORT_DESC]);

		if ($tags)
			$legacy = $legacy->leftJoin("legacy_tag", "legacy_motion.id = legacy_motion_id")
				->andWhere(["IN", "motion_tag_id", $tags]);

		$legacy = $legacy->limit($limit - count($rounds))->all();

		foreach ($legacy as $l) {
			$m[] = new Motion(["object" => $l]);
		}

		usort($m, [self::className(), "date_sort"]);

		return $m;
	}

	/**
	 * @param Motion $a
	 * @param Motion $b
	 *
	 * @return int
	 */
	public static function date_sort($a, $b)
	{
		$ad = ($a->date);
		$bd = ($b->date);

		return ($ad < $bd) ? 1 : (($ad > $bd) ? -1 : 0);
	}

	public function init()
	{
		$o = $this->object;
		if ($o instanceof Round) {
			$this->motion = $o->motion;
			$this->infoslide = $o->infoslide;
			$this->tournament = $o->tournament->name;
			$this->round = $o->getName();
			$this->link = Yii::$app->urlManager->createUrl(["tournament/view", "id" => $o->tournament_id]);
			$this->date = strtotime($o->time);
			$this->tags = ArrayHelper::map($o->motionTags, "id", "name");

		} else if ($o instanceof LegacyMotion) {
			$this->motion = $o->motion;
			$this->infoslide = $o->infoslide;
			$this->tournament = $o->tournament;
			$this->round = $o->round;
			$this->link = $o->link;
			$this->date = strtotime($o->time);
			$this->tags = ArrayHelper::map($o->motionTags, "id", "name");
		} else {
			throw new Exception("Object not defined");
		}
	}

	public function getTagsField()
	{
		$field = [];
		asort($this->tags);
		foreach ($this->tags as $id => $text) {
			$field[] = Html::a($text, ["motiontag/view", "id" => $id]);
		}

		return implode(", ", $field);
	}
}
