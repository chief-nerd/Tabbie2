<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property integer $id
 * @property integer $debate_id
 * @property string $time
 *
 * @property Debate $debate
 * @property FeedbackHasAnswer[] $feedbackHasAnswers
 * @property Answer[] $answers
 */
class Feedback extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['debate_id'], 'required'],
            [['debate_id'], 'integer'],
            [['time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'debate_id' => Yii::t('app', 'Debate ID'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebate() {
        return $this->hasOne(Debate::className(), ['id' => 'debate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbackHasAnswers() {
        return $this->hasMany(FeedbackHasAnswer::className(), ['feedback_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers() {
        return $this->hasMany(Answer::className(), ['id' => 'answer_id'])->viaTable('feedback_has_answer', ['feedback_id' => 'id']);
    }

    /**
     *
     * @param Tournament $tournament
     * @return Question[]
     * @throws Exception
     */
    public function getQuestions($tournament) {

        $model = Yii::$app->user->getRoleModel($tournament->id);
        $lastRound = $tournament->getLastRound();

        if ($model instanceof Adjudicator) {
            //Check if was chair
            $lastRound->id;
        } else if ($model instanceof Team) {

        } else
            throw new Exception("No Role");

        $filter = array();
        $filter = array_merge($filter, [
            "tournament_id" => $tournament->id
        ]);
        return Question::find()->joinWith("tournamentHasQuestion")->where($filter)->all();
    }

}
