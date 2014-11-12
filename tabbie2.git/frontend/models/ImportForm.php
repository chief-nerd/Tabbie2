<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ImportForm extends Model {

    public $csvFile;
    public $type;
    public $tempImport;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            // name, email, subject and body are required
            [['csvFile', 'type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'csvFile' => '*.csv File',
            'type' => 'Type of Import Syntax',
        ];
    }

    public static function typeOptions() {
        return [
            0 => "Teams",
            1 => "Adjudicators",
        ];
    }

    public function importTeams() {

    }

    public function importAdjudicators() {

    }

}
