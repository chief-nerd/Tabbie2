<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tournament_has_question".
 *
 * @property integer $tournament_id
 * @property integer $questions_id
 *
 * @property Tournament $tournament
 * @property Question $questions
 */
class TournamentHasQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tournament_has_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'questions_id'], 'required'],
            [['tournament_id', 'questions_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'questions_id' => Yii::t('app', 'Questions ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasOne(Question::className(), ['id' => 'questions_id']);
    }
}
