<?php

namespace frontend\controllers;

use common\models\LoginForm;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout', 'signup', 'list-societies'],
				'rules' => [
					[
						'actions' => ['signup', 'list-societies'],
						'allow' => true,
						'roles' => ['?'],
					],
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	/**
	 * Dirty Dirty Dirty Fix for anonymous access
	 *
	 * @param null $search
	 * @param null $id
	 *
	 * @return mixed
	 * @throws \yii\base\InvalidRouteException
	 */
	public function actionListSocieties($search = null, $id = null) {
		$soc_controller = new SocietyController('society', Yii::$app);
		return $soc_controller->runAction('list', ["search" => $search, "id" => $id]);
	}

	public function actionIndex() {

		$tournaments = \common\models\Tournament::find()->where("start_date <= NOW() AND end_date >= NOW()")->all();

		return $this->render('index', [
			"tournaments" => $tournaments
		]);
	}

	public function actionLogin() {
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}
		else {
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionLogout() {
		Yii::$app->user->logout();

		return $this->goHome();
	}

	public function actionContact() {
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
				Yii::$app->session->setFlash('success', Yii::t("app", 'Thank you for contacting us. We will respond to you as soon as possible.'));
			}
			else {
				Yii::$app->session->setFlash('error', Yii::t("app", 'There was an error sending email.'));
			}

			return $this->refresh();
		}
		else {
			return $this->render('contact', [
				'model' => $model,
			]);
		}
	}

	public function actionAbout() {
		return $this->render('about');
	}

	public function actionSignup() {
		$model = new SignupForm();
		if ($model->load(Yii::$app->request->post())) {
			$user = $model->signup();
			if ($user !== null) {
				if (Yii::$app->getUser()->login($user)) {
					Yii::$app->session->addFlash("success", Yii::t("app", "User registered! Welcome {user}", ["user" => $user->name]));
					return $this->goHome();
				}
			}
			else
				Yii::$app->session->addFlash("error", print_r($model->getErrors(), true));
		}

		return $this->render('signup', [
			'model' => $model,
		]);
	}

	public function actionRequestPasswordReset() {
		$model = new PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->getSession()
				         ->setFlash('success', Yii::t("app", 'Check your email for further instructions.'));

				return $this->goHome();
			}
			else {
				Yii::$app->getSession()
					->setFlash('error', Yii::t("app", 'Sorry, we are unable to reset password for email provided.<br>{message}', ["message" => print_r($model->getErrors(), true)]));
			}
		}

		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
	}

	public function actionResetPassword($token) {
		try {
			$model = new ResetPasswordForm($token);
		} catch (InvalidParamException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}

		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->getSession()->setFlash('success', Yii::t("app", 'New password was saved.'));

			return $this->goHome();
		}

		return $this->render('resetPassword', [
			'model' => $model,
		]);
	}

}
