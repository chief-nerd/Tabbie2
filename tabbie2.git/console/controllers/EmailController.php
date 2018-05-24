<?php
/**
 * Created by PhpStorm.
 * User: richardcoates
 * Date: 24/05/2018
 * Time: 10:43
 */

namespace console\controllers;

use yii\console\Controller;
use Yii;
use common\models\User;

/**
 * Cron controller
 */
class EmailController extends Controller {

	public function actionIndex() {

		$offset = 0;

		do {
			$emailBatch = [];

			$allUsers = User::find()->where(['gdprconsent' => 0, 'id' => 37])->limit(99)->offset($offset)->all();

			foreach ($allUsers as $user) {
				$emailBatch[] = Yii::$app->mailer->compose('@frontend/views/emails/gdpr', [])
					->setFrom([Yii::$app->params['supportEmail'] => 'Tabbie.org Admin'])
					->setSubject('Don\'t lose your Tabbie history! Tabbie.org and GDPR')
					->setTo($user->email);
			}
			Yii::$app->mailer->sendMultiple($emailBatch);
			$offset += sizeof($allUsers);
		} while (sizeof($allUsers) > 0);
	}
}