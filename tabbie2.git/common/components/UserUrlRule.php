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
                return "user";

            if (isset($params['id'])) {
                $user = User::findOne($params['id']);
                if ($user instanceof User) {
                    if ($parts[1] == "view")
                        $parts[1] = null;
                    $ret = "user/" . $user->username . "/" . $parts[1];
                    //Yii::trace("Returning Base: " . $ret, __METHOD__);
                    return $ret;
                } else
                    return "user/#";
            }
        }

        //Yii::trace("Returnning: FALSE", __METHOD__);
        return false;  // this rule does not apply
    }

    public function parseRequest($manager, $request) {
        $pathInfo = $request->getPathInfo();
        $params = [];
        if ($pathInfo == "user")
            return ["user/index", $params];

        $parts = explode("/", $pathInfo);
        //Yii::trace("Request URL Parts: " . print_r($parts, true), __METHOD__);
        if ($parts[0] == "user") {
            $potential_username = $parts[1];
            $user = User::findByUsername($potential_username);
            if ($user !== null) {
                //Yii::trace("User found with id: #" . $user->id . " named " . $user->name, __METHOD__);
                unset($parts[1]);

                if (isset($parts[2]) && $parts[2] != null) {
                    if (isset($parts[3])) {
                        if (is_numeric($parts[3])) {
                            //Yii::trace("parts[3] is numeric", __METHOD__);
                            $params['id'] = $parts[3];
                            $parts[3] = "view";
                        }
                        $route = implode("/", $parts);
                    } else {
                        $params['id'] = $user->id;
                        $route = "user/" . $parts[2];
                    }
                } else {
                    $params['id'] = $user->id;
                    $route = "user/view";
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
