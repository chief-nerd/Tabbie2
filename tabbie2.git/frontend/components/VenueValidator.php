<?php
/**
 * VenueValidator.php File
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace frontend\components;

use yii\validators\Validator;
use Yii;

class VenueValidator extends Validator
{
	public $low_message;
	public $high_message;

	public function init()
	{
		parent::init();
		$this->low_message = Yii::t("app", "Not enough venues") . ' ...';
		$this->high_message = Yii::t("app", "Too many venues") . ' ...';
	}

	public function validateAttribute($model, $attribute)
	{
		if (count($model->$attribute) < $model->level) {
			$model->addError($attribute, $this->low_message);
		} else if (count($model->$attribute) > $model->level) {
			$model->addError($attribute, $this->high_message);
		}
	}

	public function clientValidateAttribute($model, $attribute, $view)
	{
		$amount = $model->level;
		$low_message = json_encode($this->low_message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$high_message = json_encode($this->high_message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		return <<<JS
		if (value.length < $amount) {
			messages.push($low_message);
		}
		if (value.length > $amount) {
			messages.push($high_message);
		}
JS;
	}
}