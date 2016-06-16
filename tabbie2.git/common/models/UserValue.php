<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_value".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $user_attr_id
 * @property string $value
 *
 * @property User $user
 * @property UserAttr $userAttr
 */
class UserValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_value';
    }

    /**
     * @param string[] $customValues
     * @param Tournament $tournament
     *
     * @return bool
     */
    public static function SaveCustomValues($customValues, $userID, $tournament)
    {
        foreach ($customValues as $key => $value) {

            $attr = UserAttr::findOne([
                "tournament_id" => $tournament->id,
                "name" => $key
            ]);

            if (!$attr instanceof UserAttr) {

                $attr = new UserAttr([
                    "name" => $key,
                    "tournament_id" => $tournament->id,
                ]);
                if (!$attr->save()) {
                    Yii::error("Import CustomAttr userA: " . print_r($attr->getErrors(), true), __METHOD__);
                    Yii::$app->session->addFlash("error",
                        Yii::t("app", "Error Saving Custom Attribute: {name}", ["name" => $key]));
                }
            }

            $user_value = UserValue::findOne([
                "user_id" => $userID,
                "user_attr_id" => $attr->id,
            ]);

            if (!$user_value instanceof UserValue) {
                $user_value = new UserValue([
                    "user_id" => $userID,
                    "user_attr_id" => $attr->id,
                ]);
            }
            $user_value->value = $value;

            if (!$user_value->save()) {
                Yii::error("Import CustomAttr User: " . print_r($user_value->getErrors(), true), __METHOD__);
                Yii::$app->session->addFlash("error",
                    Yii::t("app", "Error Saving Custom Value '{key}': {value}", [
                        "key" => $key,
                        "value" => $value,
                    ]));
                return false;
            }

            return true;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_attr_id', 'value'], 'required'],
            [['user_id', 'user_attr_id'], 'integer'],
            [['value'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'user_attr_id' => Yii::t('app', 'User Attr ID'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAttr()
    {
        return $this->hasOne(UserAttr::className(), ['id' => 'user_attr_id']);
    }
}
