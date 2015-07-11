<?php

namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model {

	public $email;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['email', 'filter', 'filter' => 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'exist',
				'targetClass' => '\common\models\User',
				'filter' => ['status' => User::STATUS_ACTIVE],
				'message' => 'There is no user with such email.'
			],
		];
	}

	/**
	 * Sends an email with a link, for resetting the password.
	 *
	 * @return boolean whether the email was send
	 */
	public function sendEmail() {
		/* @var $user User */
		$user = User::findOne([
			'status' => User::STATUS_ACTIVE,
			'email' => $this->email,
		]);

		if ($user instanceof User) {
			if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
				$user->generatePasswordResetToken();
			}

			if ($user->save()) {
				return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
					->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params["appName"] . ' robot'])
				                         ->setTo($this->email)
					->setSubject(Yii::t("app", 'Password reset for {user}', ["user" => $user->getName()]))
				                         ->send();
			}
			else
				$this->addError("user", $user->getErrors());
		}
		else
			$this->addError("email", Yii::t("app", "User not found with this Email"));

		return false;
	}

}
