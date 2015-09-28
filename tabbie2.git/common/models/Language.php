<?php

namespace common\models;

use kartik\helpers\Html;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "language".
 *
 * @property string $language
 * @property string $label
 * @property string $last_update
 *
 * @property LanguageMaintainer[] $languageMaintainers
 * @property User[] $users
 */
class Language extends \yii\db\ActiveRecord {

	public static $configFile = "common/messages/config.php";

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'language';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['language', 'label'], 'required'],
			[['last_update'], 'safe'],
			[['language'], 'string', 'max' => 16],
			[['label'], 'string', 'max' => 100]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'language' => Yii::t('app', 'Language Code'),
			'label' => Yii::t('app', 'Label'),
			'cover' => Yii::t('app', 'Coverage'),
			'last_update' => Yii::t('app', 'Last Update'),
		];
	}

	public static function getLabel($lang) {
		$options = self::getOptions();

		return (isset($options[$lang])) ? $options[$lang] : "";
	}

	public static function getOptions() {
		return array(
			'en' => 'English (Int)',
			'aa-DJ' => 'Afar (Djibouti)',
			'aa-ER' => 'Afar (Eritrea)',
			'aa-ET' => 'Afar (Ethiopia)',
			'af-ZA' => 'Afrikaans (South Africa)',
			'sq-AL' => 'Albanian (Albania)',
			'sq-MK' => 'Albanian (Macedonia)',
			'am-ET' => 'Amharic (Ethiopia)',
			'ar-DZ' => 'Arabic (Algeria)',
			'ar-BH' => 'Arabic (Bahrain)',
			'ar-EG' => 'Arabic (Egypt)',
			'ar-IN' => 'Arabic (India)',
			'ar-IQ' => 'Arabic (Iraq)',
			'ar-JO' => 'Arabic (Jordan)',
			'ar-KW' => 'Arabic (Kuwait)',
			'ar-LB' => 'Arabic (Lebanon)',
			'ar-LY' => 'Arabic (Libya)',
			'ar-MA' => 'Arabic (Morocco)',
			'ar-OM' => 'Arabic (Oman)',
			'ar-QA' => 'Arabic (Qatar)',
			'ar-SA' => 'Arabic (Saudi Arabia)',
			'ar-SD' => 'Arabic (Sudan)',
			'ar-SY' => 'Arabic (Syria)',
			'ar-TN' => 'Arabic (Tunisia)',
			'ar-AE' => 'Arabic (United Arab Emirates)',
			'ar-YE' => 'Arabic (Yemen)',
			'an-ES' => 'Aragonese (Spain)',
			'hy-AM' => 'Armenian (Armenia)',
			'as-IN' => 'Assamese (India)',
			'ast-ES' => 'Asturian (Spain)',
			'az-AZ' => 'Azerbaijani (Azerbaijan)',
			'az-TR' => 'Azerbaijani (Turkey)',
			'eu-FR' => 'Basque (France)',
			'eu-ES' => 'Basque (Spain)',
			'be-BY' => 'Belarusian (Belarus)',
			'bem-ZM' => 'Bemba (Zambia)',
			'bn-BD' => 'Bengali (Bangladesh)',
			'bn-IN' => 'Bengali (India)',
			'ber-DZ' => 'Berber (Algeria)',
			'ber-MA' => 'Berber (Morocco)',
			'byn-ER' => 'Blin (Eritrea)',
			'bs-BA' => 'Bosnian (Bosnia and Herzegovina)',
			'br-FR' => 'Breton (France)',
			'bg-BG' => 'Bulgarian (Bulgaria)',
			'my-MM' => 'Burmese (Myanmar [Burma])',
			'ca-AD' => 'Catalan (Andorra)',
			'ca-FR' => 'Catalan (France)',
			'ca-IT' => 'Catalan (Italy)',
			'ca-ES' => 'Catalan (Spain)',
			'zh-CN' => 'Chinese (China)',
			'zh-HK' => 'Chinese (Hong Kong SAR China)',
			'zh-SG' => 'Chinese (Singapore)',
			'zh-TW' => 'Chinese (Taiwan)',
			'cv-RU' => 'Chuvash (Russia)',
			'kw-GB' => 'Cornish (United Kingdom)',
			'crh-UA' => 'Crimean Turkish (Ukraine)',
			'hr-HR' => 'Croatian (Croatia)',
			'cs-CZ' => 'Czech (Czech Republic)',
			'da-DK' => 'Danish (Denmark)',
			'dv-MV' => 'Divehi (Maldives)',
			'nl-AW' => 'Dutch (Aruba)',
			'nl-BE' => 'Dutch (Belgium)',
			'nl-NL' => 'Dutch (Netherlands)',
			'dz-BT' => 'Dzongkha (Bhutan)',
			'en-AG' => 'English (Antigua and Barbuda)',
			'en-AU' => 'English (Australia)',
			'en-BW' => 'English (Botswana)',
			'en-CA' => 'English (Canada)',
			'en-DK' => 'English (Denmark)',
			'en-HK' => 'English (Hong Kong SAR China)',
			'en-IN' => 'English (India)',
			'en-IE' => 'English (Ireland)',
			'en-NZ' => 'English (New Zealand)',
			'en-NG' => 'English (Nigeria)',
			'en-PH' => 'English (Philippines)',
			'en-SG' => 'English (Singapore)',
			'en-ZA' => 'English (South Africa)',
			'en-GB' => 'English (United Kingdom)',
			'en-US' => 'English (United States)',
			'en-ZM' => 'English (Zambia)',
			'en-ZW' => 'English (Zimbabwe)',
			'eo' => 'Esperanto',
			'et-EE' => 'Estonian (Estonia)',
			'fo-FO' => 'Faroese (Faroe Islands)',
			'fil-PH' => 'Filipino (Philippines)',
			'fi-FI' => 'Finnish (Finland)',
			'fr-BE' => 'French (Belgium)',
			'fr-CA' => 'French (Canada)',
			'fr-FR' => 'French (France)',
			'fr-LU' => 'French (Luxembourg)',
			'fr-CH' => 'French (Switzerland)',
			'fur-IT' => 'Friulian (Italy)',
			'ff-SN' => 'Fulah (Senegal)',
			'gl-ES' => 'Galician (Spain)',
			'lg-UG' => 'Ganda (Uganda)',
			'gez-ER' => 'Geez (Eritrea)',
			'gez-ET' => 'Geez (Ethiopia)',
			'ka-GE' => 'Georgian (Georgia)',
			'de-AT' => 'German (AT)',
			'de-BE' => 'German (Belgium)',
			'de-DE' => 'German (DE)',
			'de-LI' => 'German (Liechtenstein)',
			'de-LU' => 'German (Luxembourg)',
			'de-CH' => 'German (Switzerland)',
			'el-CY' => 'Greek (Cyprus)',
			'el-GR' => 'Greek (Greece)',
			'gu-IN' => 'Gujarati (India)',
			'ht-HT' => 'Haitian (Haiti)',
			'ha-NG' => 'Hausa (Nigeria)',
			'he-IL' => 'Hebrew (Israel)',
			'hi-IN' => 'Hindi (India)',
			'hu-HU' => 'Hungarian (Hungary)',
			'is-IS' => 'Icelandic (Iceland)',
			'ig-NG' => 'Igbo (Nigeria)',
			'id-ID' => 'Indonesian (Indonesia)',
			'ia' => 'Interlingua',
			'iu-CA' => 'Inuktitut (Canada)',
			'ik-CA' => 'Inupiaq (Canada)',
			'ga-IE' => 'Irish (Ireland)',
			'it-IT' => 'Italian (Italy)',
			'it-CH' => 'Italian (Switzerland)',
			'ja-JP' => 'Japanese (Japan)',
			'kl-GL' => 'Kalaallisut (Greenland)',
			'kn-IN' => 'Kannada (India)',
			'ks-IN' => 'Kashmiri (India)',
			'csb-PL' => 'Kashubian (Poland)',
			'kk-KZ' => 'Kazakh (Kazakhstan)',
			'km-KH' => 'Khmer (Cambodia)',
			'rw-RW' => 'Kinyarwanda (Rwanda)',
			'ky-KG' => 'Kirghiz (Kyrgyzstan)',
			'kok-IN' => 'Konkani (India)',
			'ko-KR' => 'Korean (South Korea)',
			'ku-TR' => 'Kurdish (Turkey)',
			'lo-LA' => 'Lao (Laos)',
			'lv-LV' => 'Latvian (Latvia)',
			'li-BE' => 'Limburgish (Belgium)',
			'li-NL' => 'Limburgish (Netherlands)',
			'lt-LT' => 'Lithuanian (Lithuania)',
			'nds-DE' => 'Low German (Germany)',
			'nds-NL' => 'Low German (Netherlands)',
			'mk-MK' => 'Macedonian (Macedonia)',
			'mai-IN' => 'Maithili (India)',
			'mg-MG' => 'Malagasy (Madagascar)',
			'ms-MY' => 'Malay (Malaysia)',
			'ml-IN' => 'Malayalam (India)',
			'mt-MT' => 'Maltese (Malta)',
			'gv-GB' => 'Manx (United Kingdom)',
			'mi-NZ' => 'Maori (New Zealand)',
			'mr-IN' => 'Marathi (India)',
			'mn-MN' => 'Mongolian (Mongolia)',
			'ne-NP' => 'Nepali (Nepal)',
			'se-NO' => 'Northern Sami (Norway)',
			'nso-ZA' => 'Northern Sotho (South Africa)',
			'nb-NO' => 'Norwegian Bokmål (Norway)',
			'nn-NO' => 'Norwegian Nynorsk (Norway)',
			'oc-FR' => 'Occitan (France)',
			'or-IN' => 'Oriya (India)',
			'om-ET' => 'Oromo (Ethiopia)',
			'om-KE' => 'Oromo (Kenya)',
			'os-RU' => 'Ossetic (Russia)',
			'pap-AN' => 'Papiamento (Netherlands Antilles)',
			'ps-AF' => 'Pashto (Afghanistan)',
			'fa-IR' => 'Persian (Iran)',
			'pl-PL' => 'Polish (Poland)',
			'pt-BR' => 'Portuguese (Brazil)',
			'pt-PT' => 'Portuguese (Portugal)',
			'pa-IN' => 'Punjabi (India)',
			'pa-PK' => 'Punjabi (Pakistan)',
			'ro-RO' => 'Romanian (Romania)',
			'ru-RU' => 'Russian (Russia)',
			'ru-UA' => 'Russian (Ukraine)',
			'sa-IN' => 'Sanskrit (India)',
			'sc-IT' => 'Sardinian (Italy)',
			'gd-GB' => 'Scottish Gaelic (United Kingdom)',
			'sr-ME' => 'Serbian (Montenegro)',
			'sr-RS' => 'Serbian (Serbia)',
			'sid-ET' => 'Sidamo (Ethiopia)',
			'sd-IN' => 'Sindhi (India)',
			'si-LK' => 'Sinhala (Sri Lanka)',
			'sk-SK' => 'Slovak (Slovakia)',
			'sl-SI' => 'Slovenian (Slovenia)',
			'so-DJ' => 'Somali (Djibouti)',
			'so-ET' => 'Somali (Ethiopia)',
			'so-KE' => 'Somali (Kenya)',
			'so-SO' => 'Somali (Somalia)',
			'nr-ZA' => 'South Ndebele (South Africa)',
			'st-ZA' => 'Southern Sotho (South Africa)',
			'es-AR' => 'Spanish (Argentina)',
			'es-BO' => 'Spanish (Bolivia)',
			'es-CL' => 'Spanish (Chile)',
			'es-CO' => 'Spanish (Colombia)',
			'es-CR' => 'Spanish (Costa Rica)',
			'es-DO' => 'Spanish (Dominican Republic)',
			'es-EC' => 'Spanish (Ecuador)',
			'es-SV' => 'Spanish (El Salvador)',
			'es-GT' => 'Spanish (Guatemala)',
			'es-HN' => 'Spanish (Honduras)',
			'es-MX' => 'Spanish (Mexico)',
			'es-NI' => 'Spanish (Nicaragua)',
			'es-PA' => 'Spanish (Panama)',
			'es-PY' => 'Spanish (Paraguay)',
			'es-PE' => 'Spanish (Peru)',
			'es-ES' => 'Spanish (Spain)',
			'es-US' => 'Spanish (United States)',
			'es-UY' => 'Spanish (Uruguay)',
			'es-VE' => 'Spanish (Venezuela)',
			'sw-KE' => 'Swahili (Kenya)',
			'sw-TZ' => 'Swahili (Tanzania)',
			'ss-ZA' => 'Swati (South Africa)',
			'sv-FI' => 'Swedish (Finland)',
			'sv-SE' => 'Swedish (Sweden)',
			'tl-PH' => 'Tagalog (Philippines)',
			'tg-TJ' => 'Tajik (Tajikistan)',
			'ta-IN' => 'Tamil (India)',
			'tt-RU' => 'Tatar (Russia)',
			'te-IN' => 'Telugu (India)',
			'th-TH' => 'Thai (Thailand)',
			'bo-CN' => 'Tibetan (China)',
			'bo-IN' => 'Tibetan (India)',
			'tig-ER' => 'Tigre (Eritrea)',
			'ti-ER' => 'Tigrinya (Eritrea)',
			'ti-ET' => 'Tigrinya (Ethiopia)',
			'ts-ZA' => 'Tsonga (South Africa)',
			'tn-ZA' => 'Tswana (South Africa)',
			'tr-CY' => 'Turkish (Cyprus)',
			'tr-TR' => 'Turkish (Turkey)',
			'tk-TM' => 'Turkmen (Turkmenistan)',
			'ug-CN' => 'Uighur (China)',
			'uk-UA' => 'Ukrainian (Ukraine)',
			'hsb-DE' => 'Upper Sorbian (Germany)',
			'ur-PK' => 'Urdu (Pakistan)',
			'uz-UZ' => 'Uzbek (Uzbekistan)',
			've-ZA' => 'Venda (South Africa)',
			'vi-VN' => 'Vietnamese (Vietnam)',
			'wa-BE' => 'Walloon (Belgium)',
			'cy-GB' => 'Welsh (United Kingdom)',
			'fy-DE' => 'Western Frisian (Germany)',
			'fy-NL' => 'Western Frisian (Netherlands)',
			'wo-SN' => 'Wolof (Senegal)',
			'xh-ZA' => 'Xhosa (South Africa)',
			'yi-US' => 'Yiddish (United States)',
			'yo-NG' => 'Yoruba (Nigeria)',
			'zu-ZA' => 'Zulu (South Africa)',
			'tlh' => 'Klingon',
			'bar' => 'Viennese',
		);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguageMaintainers() {
		return $this->hasMany(LanguageMaintainer::className(), ['language_language' => 'language']);
	}

	public function getLanguageMaintainerLabel() {
		$user = [];
		foreach ($this->languageMaintainers as $m) {
			if (isset($m->user)) {
				$user[] = Html::a($m->user->givenname, Yii::$app->urlManagerFrontend->createUrl(["user/view", "id" => $m->user->id]));
			}
		}
		return implode(", ", $user);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers() {
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('language_maintainer', ['language_language' => 'language']);
	}

	public function getCover() {
		if ($this->getMessagesCount(true) == 0) {
			return 0;
		}
		return $this->getMessagesCount() / $this->getMessagesCount(true);
	}

	public function getMessagesCount($all = false) {
		$query = $this->getMessages();

		if ($all != true) {
			$query->andWhere("translation IS NOT NULL");
		}

		return $query->count();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMessages() {
		return $this->hasMany(Message::className(), ['language' => 'language']);
	}
}

