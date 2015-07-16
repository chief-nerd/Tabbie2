<?php
/**
 * ObjectError.php File
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace common\components;


use yii\base\Component;

class ObjectError extends Component
{

    public static function getMsg($object)
    {

        $errors = $object->getErrors();
        $msg = "";

        foreach ($errors as $attribute => $attributeArray) {
            foreach ($attributeArray as $index => $error) {
                if ($index > 0) $msg .= "<br>";
                $msg .= $error . " (" . $attribute . ")";
            }
        }
        return $msg;
    }
}