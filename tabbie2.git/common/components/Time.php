<?php
/**
 * Created by IntelliJ IDEA.
 * User: jareiter
 * Date: 01/09/15
 * Time: 01:01
 */

namespace common\components;


use yii\base\Component;

class Time extends Component
{
	public function UTC($time = 'now')
	{
		return (new \DateTime($time, new \DateTimeZone('UTC')))->format(\DateTime::W3C);
	}
}