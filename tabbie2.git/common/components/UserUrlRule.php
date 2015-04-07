<?php

namespace common\components;

Use Yii;
use yii\web\UrlRule;
use common\models\User;

class UserUrlRule extends UrlRule {

	public function init() {
		if ($this->name === null) {
			$this->name = __CLASS__;
		}
	}

	public function createUrl($manager, $route, $params) {
		$parts = explode("/", $route);
		//Yii::trace("Start with Route parts: " . print_r($parts, true) . " and params " . print_r($params, true), __METHOD__);
		//Handle tournament base
		if ($parts[0] == "user") {
			if ($parts[1] == "index")
				$route = "users";

			if (isset($params['id'])) {
				$user = User::findOne($params['id']);
				if ($user instanceof User) {
					unset($params['id']);
					if ($parts[1] == "view")
						$parts[1] = null;

					$route = "user/" . $user->username . "/" . $parts[1];
				}
				else
					return "user/#";
			}

			$paramsString = "";
			foreach ($params as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$paramsString .= "/" . $key . "[" . $k . "]/" . $v;
					}
				}
				else
					$paramsString .= "/" . $key . "/" . $value;
			}

			$ret = $route . $paramsString;
			//Yii::trace("Returning Base: " . $ret, __METHOD__);
			return $ret;
		}

		//Manuel Set
		if (isset($params['user_id'])) {
			$user = User::findOne($params['user_id']);
			if (isset($params['id'])) {
				switch ($parts[1]) {
					case "view":
						$parts[1] = $params['id'];
						unset($params['id']);
						break;
					default:
						$parts[] = $params['id'];
						unset($params['id']);
				}
			}
			else {
				if ($parts[1] == "index") {
					//index Case
					//don't unset, end / needed
					$parts[1] = null;
				}
			}
			unset($params['user_id']);
			$paramsString = "";
			foreach ($params as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$paramsString .= "/" . $key . "[" . $k . "]/" . $v;
					}
				}
				else
					$paramsString .= "/" . $key . "/" . $value;
			}
			$ret = "user/" . $user->username . "/" . implode("/", $parts) . $paramsString;
			//Yii::trace("Returning Sub: " . $ret, __METHOD__);
			return $ret;
		}

		//Yii::trace("Returnning: FALSE", __METHOD__);
		return false;  // this rule does not apply
	}

	public function parseRequest($manager, $request) {
		$pathInfo = $request->getPathInfo();
		$params = [];
		$route = "";

		$parts = explode("/", $pathInfo);

		if ($parts[0] == "users") {
			for ($i = 1; $i <= count($parts); $i = $i + 2) {
				if (isset($parts[$i]) && isset($parts[$i + 1]))
					$params[$parts[$i]] = $parts[$i + 1];
			}
			return ["user/index", $params];
		}

		//Yii::trace("Request URL Parts: " . print_r($parts, true), __METHOD__);
		if ($parts[0] == "user") {
			$potential_username = $parts[1];
			$user = User::findByUsername($potential_username);
			if ($user !== null) {
				//Yii::trace("User found with id: #" . $user->id . " named " . $user->name, __METHOD__);

				$modul = "other";

				if (count($parts) <= 3)
					$modul = "user";

				if (isset($parts[3]) && $parts[3] == "")
					$modul = "other";

				if ($modul == "user") //Base User Class
				{
					if (isset($parts[2]) && $parts[2] != null)
						$route = "user/" . $parts[2];
					else
						$route = "user/view";

					$params['id'] = $user->id;
					$offset = 3;
				}
				else { //Context Classes

					//case index
					if ($parts[3] == "")
						$parts[3] = "index";

					$route = implode("/", [$parts[2], $parts[3]]);

					if (isset($parts[4]) && is_numeric($parts[4])) {
						$params['id'] = $parts[4];
					}

					$params['user_id'] = $user->id;
					$offset = 4;
				}

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
