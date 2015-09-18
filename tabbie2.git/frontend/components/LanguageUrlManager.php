<?php
/**
 * User: jakob
 * Date: 18/09/15
 * Time: 15:28
 */

namespace frontend\components;

use codemix\localeurls\UrlManager;
use common\models\Language;
use yii\helpers\ArrayHelper;

class LanguageUrlManager extends UrlManager {

	public function init() {
		$this->languages = self::getLangConfig();
		parent::init();
	}

	public static function getLangConfig() {
		$dblang = ArrayHelper::getColumn(self::getAllLanguages(), "language");
		return array_merge([\Yii::$app->sourceLanguage], $dblang);
	}

	public static function getAllLanguages() {

		$langs = \Yii::$app->cache->get("app_langs");

		if (!is_array($langs)) {
			$langs = Language::find()->all();
			\Yii::$app->cache->set("app_langs", $langs, 3600);
		}

		return $langs;

	}
}