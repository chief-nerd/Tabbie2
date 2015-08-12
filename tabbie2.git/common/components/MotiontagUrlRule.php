<?php

namespace common\components;

use common\models\MotionTag;
Use Yii;
use yii\web\UrlRule;
use common\models\User;

class MotiontagUrlRule extends UrlRule
{

	public function init()
	{
		if ($this->name === null) {
			$this->name = __CLASS__;
		}
	}

	public function createUrl($manager, $route, $params)
	{
		$parts = explode("/", $route);
		//Yii::trace("Start with Route parts: " . print_r($parts, true) . " and params " . print_r($params, true), __METHOD__);
		//Handle tournament base
		if ($parts[0] == "motiontag") {
			if ($parts[1] == "index")
				$route = "motions";

			if (isset($params['id'])) {
				$tag = MotionTag::findOne($params['id']);
				if ($tag instanceof MotionTag) {
					unset($params['id']);
					if ($parts[1] == "view")
						$parts[1] = null;

					$route = "motiontag/" . urlencode($tag->name) . "/" . $parts[1];
				} else
					return "motiontag/#";
			}

			$paramsString = "";
			foreach ($params as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$paramsString .= "/" . $key . "[" . $k . "]/" . $v;
					}
				} else
					$paramsString .= "/" . $key . "/" . $value;
			}

			$ret = $route . $paramsString;

			//Yii::trace("Returning Base: " . $ret, __METHOD__);
			return $ret;
		}

		//Yii::trace("Returnning: FALSE", __METHOD__);
		return false;  // this rule does not apply
	}

	public function parseRequest($manager, $request)
	{
		$pathInfo = $request->getPathInfo();
		$params = [];
		$route = "";

		$parts = explode("/", $pathInfo);

		if ($parts[0] == "motions") {
			for ($i = 1; $i <= count($parts); $i = $i + 2) {
				if (isset($parts[$i]) && isset($parts[$i + 1]))
					$params[$parts[$i]] = $parts[$i + 1];
			}

			return ["motiontag/index", $params];
		}

		//Yii::trace("Request URL Parts: " . print_r($parts, true), __METHOD__);
		if ($parts[0] == "motiontag") {
			$potential_slug = urldecode($parts[1]);
			$tag = MotionTag::find()->where(["name" => $potential_slug])->one();
			if ($tag !== null) {
				//Yii::trace("User found with id: #" . $user->id . " named " . $user->name, __METHOD__);

				if (isset($parts[2]) && $parts[2] != null)
					$route = "motiontag/" . $parts[2];
				else
					$route = "motiontag/view";

				$params['id'] = $tag->id;
				$offset = 3;

				for ($i = $offset; $i <= count($parts); $i = $i + 2) {
					if (isset($parts[$i]) && isset($parts[$i + 1]))
						$params[$parts[$i]] = $parts[$i + 1];
				}

				$ret = [$route, $params];

				//Yii::trace("Returning: " . print_r($ret, true), __METHOD__);
				return $ret;
			}
		}
		// check $matches[1] and $matches[3] to see
		// if they match a manufacturer and a model in the database
		// If so, set $params['manufacturer'] and/or $params['model']
		// and return ['car/index', $params]
		//Yii::trace("Returnning: FALSE", __METHOD__);
		return false;  // this rule does not apply
	}

}
