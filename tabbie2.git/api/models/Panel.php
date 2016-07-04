<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Panel
 * @package api\models
 */
class Panel extends \common\models\Panel implements Linkable
{
    /**
     * @return array
     */
    public function fields()
    {
        $fields = [
            "strength",
            "used",
        ];

        $fields["adjudicators"] = function ($model) {
            /** @var Panel $model */
            $adju = [];
            foreach ($model->adjudicatorInPanels as $aip) {

                $section = "";
                switch ($aip->function) {
                    case Panel::FUNCTION_CHAIR:
                        $section = "chair";
                        break;
                    case Panel::FUNCTION_WING:
                        $section = "wings";
                        break;
                    case Panel::FUNCTION_TRAINEE:
                        $section = "trainees";
                        break;
                }

                /**    @var \common\models\Adjudicator $a */
                $adju[$section][] = $aip->adjudicator->user->name;
            }
            return $adju;
        };

        return $fields;
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        $fields = $this->fields();

        $fields["adjudicators"] = function ($model) {
            /** @var Panel $model */
            $adju = [];
            foreach ($model->adjudicatorInPanels as $aip) {

                $section = "";
                switch ($aip->function) {
                    case Panel::FUNCTION_CHAIR:
                        $section = "chair";
                        break;
                    case Panel::FUNCTION_WING:
                        $section = "wings";
                        break;
                    case Panel::FUNCTION_TRAINEE:
                        $section = "trainees";
                        break;
                }

                /**    @var \common\models\Adjudicator $a */
                $aa = new Adjudicator($aip->adjudicator);
                $adju[$section][] = $aa->toArray();
            }
            return $adju;
        };

        return $fields;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        $debateID = $this->debate->id;
        $links = [
            Link::REL_SELF => [
                'api' => Url::to(['user/view', "id" => $this->id], true),
                //no web
            ],
            'debate' => Url::to(['debate/view', "id" => $debateID], true),
        ];

        return $links;
    }

}
