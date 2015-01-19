<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback_has_answer".
 *
 * @property integer $feedback_id
 * @property integer $answer_id
 *
 * @property Answer $answer
 * @property Feedback $feedback
 */
class FeedbackHasAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback_has_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedback_id', 'answer_id'], 'required'],
            [['feedback_id', 'answer_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feedback_id' => Yii::t('app', 'Feedback ID'),
            'answer_id' => Yii::t('app', 'Answer ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['id' => 'feedback_id']);
    }
}
