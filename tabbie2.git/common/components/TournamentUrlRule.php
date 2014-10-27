<?

namespace common\components;

Use Yii;
use yii\web\UrlRule;
use common\models\Tournament;

class TournamentUrlRule extends UrlRule {

    public function init() {
        if ($this->name === null) {
            $this->name = __CLASS__;
        }
    }

    public function createUrl($manager, $route, $params) {
        $parts = explode("/", $route);
        Yii::trace("Start with Route parts: " . print_r($parts, true) . " and params " . print_r($params, true), __METHOD__);

        //Handle tournament base
        if ($parts[0] == "tournament") {
            if ($parts[1] == "index")
                return "tournaments";

            if (isset($params['id'])) {
                $tournament = Tournament::findByPk($params['id']);
                if ($parts[1] == "view")
                    $parts[1] = null;
                $ret = $tournament->url_slug . "/" . $parts[1];
                Yii::trace("Returning Base: " . $ret, __METHOD__);
                return $ret;
            }
        }

        //Manuel Set
        if (isset($params['tournament_id'])) {
            $tournament = Tournament::findByPk($params['tournament_id']);
            if (isset($params['id'])) {
                switch ($parts[1]) {
                    case "view":
                        $parts[1] = $params['id'];
                        break;
                    default:
                        $parts[] = $params['id'];
                }
            } else {
                if ($parts[1] == "index") {
                    //index Case
                    //don't unset, end / needed
                    $parts[1] = null;
                }
            }
            $ret = $tournament->url_slug . "/" . implode("/", $parts);
            Yii::trace("Returning Sub: " . $ret, __METHOD__);
            return $ret;
        }

        Yii::trace("Returnning: FALSE", __METHOD__);
        return false;  // this rule does not apply
    }

    public function parseRequest($manager, $request) {
        $pathInfo = $request->getPathInfo();
        $params = [];
        if ($pathInfo == "tournaments")
            return ["tournament/index", $params];

        $parts = explode("/", $pathInfo);
        Yii::trace("Request URL Parts: " . print_r($parts, true), __METHOD__);

        $potential_slug = $parts[0];
        $tournament = Tournament::findByUrlSlug($potential_slug);
        if ($tournament !== null) {
            Yii::trace("Tournament found with id: #" . $tournament->id . " named " . $tournament->fullname, __METHOD__);
            unset($parts[0]);

            if (isset($parts[1]) && $parts[1] != null) {
                if (isset($parts[2])) {
                    if (is_numeric($parts[2])) {
                        Yii::trace("parts[2] is numeric", __METHOD__);
                        $params['id'] = $parts[2];
                        $parts[2] = "view";
                    }

                    if (isset($parts[3]) && is_numeric($parts[3])) {
                        Yii::trace("parts[3] is numeric", __METHOD__);
                        $params['id'] = $parts[3];
                        unset($parts[3]);
                    }

                    $params['tournament_id'] = $tournament->id;
                    $route = implode("/", $parts);
                } else {
                    $params['id'] = $tournament->id;
                    $route = "tournament/" . $parts[1];
                }
            } else {
                $params['id'] = $tournament->id;
                $route = "tournament/view";
            }

            $ret = [$route, $params];
            Yii::trace("Returning: " . print_r($ret, true), __METHOD__);
            return $ret;
        }
        // check $matches[1] and $matches[3] to see
        // if they match a manufacturer and a model in the database
        // If so, set $params['manufacturer'] and/or $params['model']
        // and return ['car/index', $params]
        Yii::trace("Returnning: FALSE", __METHOD__);
        return false;  // this rule does not apply
    }

}
