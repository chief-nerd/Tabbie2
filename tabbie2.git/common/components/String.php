<?php
/**
 * Created by IntelliJ IDEA.
 * User: jakob
 * Date: 23/10/15
 * Time: 22:55
 */

namespace common\components;


use yii\base\Component;

class String extends Component {

	public function toAscii($str) {
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '-', $clean);

		return $clean;
	}
}