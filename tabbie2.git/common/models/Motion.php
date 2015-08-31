<?php
namespace common\models;

use kartik\helpers\Html;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Login form
 */
class Motion extends Model
{
	const LEGACY_PREFIX = "m";

	public $object;

	public $id = null;
	public $motion = null;
	public $infoslide = null;
	public $tournament = null;
	public $round = null;
	public $link = null;
	public $date = null;
	public $language = 'en';
	public $tags = [];

	/**
	 * Find one Element by ID
	 *
	 * @param integer|string $id
	 *
	 * @return \common\models\Motion
	 * @throws \yii\base\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public static function findOne($id)
	{
		if (strpos("id", self::LEGACY_PREFIX) === 0) {
			//Legacy lookup
			$id = substr($id, 1);
			$legacy = LegacyMotion::findOne($id);
			return self::convertFrom($legacy);
		} else if (is_numeric($id)) {
			$round = Round::findOne($id);
			if ($round instanceof Round)
				return self::convertFrom($round);
		}

		throw new NotFoundHttpException("Not a valid id");
	}

	/**
	 * @param $o Object to be converted from
	 *
	 * @return \api\models\Motion
	 * @throws \yii\base\Exception
	 */
	public static function convertFrom($o)
	{
		$motion = new \api\models\Motion();
		if ($o instanceof Round) {
			$motion->id = $o->id;
			$motion->motion = $o->motion;
			$motion->infoslide = $o->infoslide;
			$motion->tournament = $o->tournament->name;
			$motion->round = $o->getName();
			$motion->link = Yii::$app->urlManager->createUrl(["tournament/view", "id" => $o->tournament_id]);
			$motion->date = strtotime($o->time);
			$motion->tags = ArrayHelper::map($o->motionTags, "id", "name");

		} else if ($o instanceof LegacyMotion) {
			$motion->id = self::LEGACY_PREFIX . $o->id;
			$motion->motion = $o->motion;
			$motion->infoslide = $o->infoslide;
			$motion->tournament = $o->tournament;
			$motion->round = $o->round;
			$motion->link = $o->link;
			$motion->date = strtotime($o->time);
			$motion->tags = ArrayHelper::map($o->motionTags, "id", "name");
		} else {
			throw new Exception("Object not defined");
		}
		return $motion;
	}

	/**
	 * @return Motion[]
	 */
	public static function findAll()
	{
		$m = [];
		$rounds = Round::find()
			->where(["displayed" => 1])
			->orderBy(["time" => SORT_DESC])
			->all();

		foreach ($rounds as $r) {
			$m[] = Motion::convertFrom($r);
		}

		$legacy = LegacyMotion::find()->orderBy(["time" => SORT_DESC])->all();

		foreach ($legacy as $l) {
			$m[] = Motion::convertFrom($l);
		}

		usort($m, [self::className(), "date_sort"]);

		return $m;
	}

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
			$m[] = Motion::convertFrom($r);
		}

		$legacy = LegacyMotion::find()->orderBy(["time" => SORT_DESC]);

		if ($tags)
			$legacy = $legacy->leftJoin("legacy_tag", "legacy_motion.id = legacy_motion_id")
				->andWhere(["IN", "motion_tag_id", $tags]);

		if (is_int($limit))
			$legacy->limit($limit - count($rounds));

		$legacy = $legacy->all();

		foreach ($legacy as $l) {
			$m[] = Motion::convertFrom($l);
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
