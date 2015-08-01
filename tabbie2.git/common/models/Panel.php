<?php

namespace common\models;

use Exception;
use Yii;

/**
 * This is the model class for table "panel".
 *
 * @property integer              $id
 * @property integer              $strength
 * @property string               $time
 * @property integer              $tournament_id
 * @property integer              $used
 * @property integer              $is_preset
 * @property AdjudicatorInPanel[] $adjudicatorInPanels
 * @property Adjudicator[]        $adjudicators
 * @property Debate               $debate
 * @property Tournament           $tournament
 */
class Panel extends \yii\db\ActiveRecord
{

	const FUNCTION_CHAIR = 1;
	const FUNCTION_WING = 0;

	public $set_adjudicators = [];

	public static function getFunctionLabel($id)
	{
		$label = [
			self::FUNCTION_CHAIR => Yii::t("app", "Chair"),
			self::FUNCTION_WING  => Yii::t("app", "Wing"),
		];

		return $label[$id];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'panel';
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['strength', 'tournament_id', 'used', 'is_preset'], 'integer'],
			[['time', 'set_adjudicators'], 'safe'],
			[['tournament_id'], 'required']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'            => Yii::t('app', 'ID'),
			'strength'      => Yii::t('app', 'Strength'),
			'time'          => Yii::t('app', 'Time'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'used'          => Yii::t('app', 'Used'),
			'is_preset'     => Yii::t('app', 'Is Preset Panel'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorInPanels()
	{
		return $this->hasMany(AdjudicatorInPanel::className(), ['panel_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicators()
	{
		return $this->hasMany(Adjudicator::className(), ['id' => 'adjudicator_id'])
			->viaTable('adjudicator_in_panel', ['panel_id' => 'id']);
	}

	public function getAdjudicatorsObjects()
	{
		return Adjudicator::find()
			->joinWith('adjudicatorInPanels')
			->where(["panel_id" => $this->id])
			->orderBy(['function' => SORT_DESC])
			->all();
	}

	/**
	 * @param integer $id
	 *
	 * @return AdjudicatorInPanel
	 */
	public function getSpecificAdjudicatorInPanel($id)
	{
		return AdjudicatorInPanel::findByCondition(["panel_id" => $this->id, "adjudicator_id" => $id])->one();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebate()
	{
		return $this->hasOne(Debate::className(), ['panel_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	public function check()
	{
		$amount_chairs = 0;
		$amount = 0;
		foreach ($this->adjudicatorInPanels as $adj) {
			if ($adj->function == 1)
				$amount_chairs++;

			if ($adj->adjudicator instanceof Adjudicator)
				$amount++;
		}

		if ($amount > 0 && $amount_chairs == 1)
			return true;
		else
			throw new Exception(Yii::t("app", "Panel #{id} has {chairs} chairs", [
				"id"     => $this->id,
				"chairs" => $amount_chairs,
			]));

		return false;
	}

	/**
	 * Gets the Chair in the Panel
	 *
	 * @return AdjudicatorInPanel
	 */
	public function getChairInPanel()
	{
		return AdjudicatorInPanel::findBySql("SELECT " . AdjudicatorInPanel::tableName() . ".* from " . AdjudicatorInPanel::tableName() . " "
			. "LEFT JOIN " . Panel::tableName() . " ON panel_id = " . Panel::tableName() . ".id "
			. "WHERE " . Panel::tableName() . ".id = " . $this->id . " AND " . AdjudicatorInPanel::tableName() . ".function = " . Panel::FUNCTION_CHAIR)
			->one();
	}

	public function is_chair($id)
	{
		if ($this->getChairInPanel()->adjudicator_id == $id) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets an ID as Chair in that panel
	 * If null, next strongest Adjudicator will be promoted to Chair
	 *
	 * @param integer|null $id
	 */
	public function setChair($id = null)
	{

		if ($id == null) {
			$nextHighestAdj = AdjudicatorInPanel::find()->where([
				"panel_id" => $this->id
			])->joinWith("adjudicator")->orderBy(["strength" => SORT_DESC])->one();
			$id = $nextHighestAdj->adjudicator_id;
		}

		$oldChair = $this->getChairInPanel();
		if ($oldChair instanceof AdjudicatorInPanel) {
			$oldChair->function = Panel::FUNCTION_WING;
			$oldChair->save();
		}
		$chair = $this->getSpecificAdjudicatorInPanel($id);
		$chair->function = Panel::FUNCTION_CHAIR;

		if ($chair->save())
			return true;
		else
			throw new \yii\base\Exception(print_r($chair->getErrors(), true));
	}

	/**
	 * Changes the Panel of the ID
	 *
	 * @param Panel   $newPanel
	 * @param integer $id
	 */
	public function changeTo($newPanel, $id)
	{
		$adj = $this->getSpecificAdjudicatorInPanel($id);
		if ($adj instanceof AdjudicatorInPanel) {
			$adj->panel_id = $newPanel->id;
			if ($adj->save())
				return true;
			else
				throw new \yii\base\Exception(print_r($adj->getErrors(), true));
		} else
			throw new Exception("getSpecificAdjudicatorInPanel with ID " . $id . " NOT found");
	}

	public function setWing($id)
	{
		$adj = $this->getSpecificAdjudicatorInPanel($id);

		if ($adj->function == Panel::FUNCTION_CHAIR) {
			$nextHighestAdjNotID = AdjudicatorInPanel::find()
				->where("panel_id = " . $this->id . " AND adjudicator_id != " . $id)
				->joinWith("adjudicator")->orderBy("strength")->one();
			$id = $nextHighestAdjNotID->adjudicator_id;
			$this->setChair($id);

			$adj->function = Panel::FUNCTION_WING;
			if ($adj->save())
				return true;
			else
				throw new \yii\base\Exception(print_r($adj->getErrors(), true));
		}

		return true;
	}

	public function setAllWings()
	{
		foreach ($this->adjudicatorInPanels as $adj) {
			$adj->function = Panel::FUNCTION_WING;
			if (!$adj->save())
				throw new \yii\base\Exception(print_r($adj->getErrors(), true));
		}

		return true;
	}


	public function generateStrength()
	{
		$strength = 0;
		foreach ($this->adjudicators as $adj) {
			$strength += $adj->strength;
		}

		if ($strength > 0)
			$this->strength = intval($strength / count($this->adjudicators));
		else
			$this->strength = $strength;

		return $this->strength;
	}


	public function createAIP()
	{
		//Clean
		AdjudicatorInPanel::deleteAll(["panel_id" => $this->id]);

		$result = $this->save();

		//Set new
		$first = true;
		foreach ($this->set_adjudicators as $new_adju) {
			if ($new_adju != "") {
				$aip = new AdjudicatorInPanel();
				$aip->adjudicator_id = $new_adju;
				$aip->panel_id = $this->id;
				$aip->function = ($first) ? 1 : 0;
				$aip->save();

				$first = false;
			}
		}
		$this->refresh();
		$this->generateStrength();

		return $this->save();
	}

	/**
	 * @param Array $a
	 * @param Array $b
	 *
	 * @return int
	 */
	public function compare_length_strength($a, $b)
	{
		$l_a = count($a["adju"]);
		$l_b = count($b["adju"]);

		if ($l_a < $l_b)
			return -1;
		elseif ($l_a > $l_b)
			return 1;
		else {

			$s_a = $a["strength"];
			$s_b = $b["strength"];
			if ($s_a < $s_b)
				return 1;
			elseif ($s_a > $s_b)
				return -1;
			else
				return 0;
		}
	}

}
