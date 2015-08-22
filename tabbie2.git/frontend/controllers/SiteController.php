<?php

namespace frontend\controllers;

use common\components\ObjectError;
use common\models\Country;
use common\models\InSociety;
use common\models\LoginForm;
use common\models\Society;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\View;

/**
 * Site controller
 */
class SiteController extends Controller
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only'  => ['logout', 'signup'],
				'rules' => [
					[
						'actions' => ['signup'],
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
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error'   => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}


	public function actionIndex()
	{
		$tournaments = \common\models\Tournament::find()->where("start_date <= NOW() AND end_date >= NOW()")->all();
		$upcoming = \common\models\Tournament::find()->where("start_date <= DATE_ADD(NOW(), INTERVAL 30 day) AND end_date >= NOW()")->all();

		return $this->render('index', [
			"tournaments" => $tournaments,
			"upcoming"    => $upcoming,
		]);
	}

	public function actionLogin()
	{
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {

			if ($model->getUser()->time == $model->getUser()->last_change) {
				//First Login -> go to Profile
				$user = $model->getUser();
				$user->last_change = date("Y-m-d H:i:s");
				$user->save();
				Yii::$app->session->setFlash('info', Yii::t("app", "Welcome! This is your first login, please check that your information are correct"));

				return Yii::$app->getResponse()->redirect(["user/update", "id" => $user->id, "login" => "first"]);
			}

			return $this->goBack();
		}

		return $this->render('login', [
			'model' => $model,
		]);
	}

	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

	public function actionContact()
	{
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
				Yii::$app->session->setFlash('success', Yii::t("app", 'Thank you for contacting us. We will respond to you as soon as possible.'));
			} else {
				Yii::$app->session->setFlash('error', Yii::t("app", 'There was an error sending email.'));
			}

			return $this->refresh();
		} else {
			return $this->render('contact', [
				'model' => $model,
			]);
		}
	}

	public function actionAbout()
	{

		$societies = Society::find()->where("country_id != " . Country::COUNTRY_UNKNOWN_ID)->asArray()->all();
		for ($i = 0; $i < count($societies); $i++) {
			$societies[$i]["amount"] = InSociety::find()->where(["society_id" => $societies[$i]["id"]])->count();
			$societies[$i]["country"] = Country::findOne($societies[$i]["country_id"])->name;
		}

		return $this->render('about', [
			"societies" => $societies
		]);
	}

	public function actionHowTo()
	{
		return $this->render('how-to');
	}

	public function actionAddNewSociety($name = "")
	{
		$model = new Society();

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "A new society has been saved"));
				$form = unserialize(Yii::$app->session["signup"]);
				if ($form instanceof SignupForm) {
					$form->societies_id = $model->id;
					$this->finishSignup($form);
				} else {
					Yii::$app->session->addFlash("notice", Yii::t("app", "There has been an error receiving your previous input. Please enter them again."));
					$this->redirect(["site/signup"]);
				}
			}
		}

		if (!isset($model->fullname)) {
			$model->fullname = $name;
		}

		return $this->render("newSociety", [
			"model" => $model,
		]);
	}

	/**
	 * @param SignupForm $model
	 *
	 * @return static
	 */
	private function finishSignup($model)
	{
		if ($model) {
			$user = $model->signup();
			if ($user !== null) {
				if (Yii::$app->getUser()->login($user)) {
					Yii::$app->session->addFlash("success", Yii::t("app", "User registered! Welcome {user}", ["user" => $user->name]));

					return Yii::$app->getResponse()->redirect(["user/" . $user->url_slug]);
				} else
					Yii::$app->session->addFlash("error", Yii::t("app", "Login failed"));
			} else
				Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
		}
	}

	public function actionSignup()
	{
		$model = new SignupForm();

		$socid = Yii::$app->request->post("SignupForm")["societies_id"];
		if (!is_numeric($socid) && $socid != "") {
			$model->load(Yii::$app->request->post());
			Yii::$app->session["signup"] = serialize($model);

			return $this->redirect(["site/add-new-society", "name" => $model->societies_id]);
		}

		if ($model->load(Yii::$app->request->post())) {
			$this->finishSignup($model, Yii::$app->request->post());
		}

		return $this->render('signup', [
			'model' => $model,
		]);
	}

	public function actionRequestPasswordReset()
	{
		$model = new PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->getSession()
					->setFlash('success', Yii::t("app", 'Check your email for further instructions.'));

				return $this->goHome();
			} else {
				Yii::$app->getSession()
					->setFlash('error', Yii::t("app", 'Sorry, we are unable to reset password for email provided.<br>{message}', ["message" => ObjectError::getMsg($model)]));
			}
		}

		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
	}

	public function actionResetPassword($token)
	{
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

	public function beforeAction($action)
	{
		Yii::$app->view->on(View::EVENT_BEGIN_PAGE, function () {
			$view = Yii::$app->controller->view;

			$fb_Banner = "https://s3.eu-central-1.amazonaws.com/tabbie-assets/FB_banner.jpg";
			$fb_Logo = "https://s3.eu-central-1.amazonaws.com/tabbie-assets/FB_logo.jpg";

			$view->registerMetaTag(["property" => "og:type", "content" => "website"], "og:type");
			$view->registerMetaTag(["property" => "og:title", "content" => Yii::$app->params["appName"] . " - " . Yii::$app->params["slogan"]], "og:title");
			$view->registerMetaTag(["property" => "og:image", "content" => $fb_Logo], "og:image1");
			$view->registerMetaTag(["property" => "og:image", "content" => $fb_Banner], "og:image2");
			$view->registerLinkTag(["rel" => "apple-touch-icon", "href" => $fb_Logo], "apple-touch-icon");
		});

		return parent::beforeAction($action);
	}

}
