<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;

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
				'rules' => [
					[
						'actions' => ['login', 'error', 'deploy'],
						'allow' => true,
					],
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['index'],
						'allow' => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isAdmin());
						}
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
		];
	}

	public function actionIndex() {
		return $this->render('index');
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

	public function actionDeploy() {
		// set the secret key
		$hookSecret = Yii::$app->params["hookSecret"];

		// set the exception handler to get error messages
		set_exception_handler(function ($e) {
			header('HTTP/1.1 500 Internal Server Error');
			echo "Error on line {$e->getLine()}: " . htmlSpecialChars($e->getMessage());
			die();
		});

		// check if we have a signature
		if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE']))
			throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
		else if (!extension_loaded('hash'))
			throw new \Exception("Missing 'hash' extension to check the secret code validity.");

		// check if the algo is supported
		list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
		if (!in_array($algo, hash_algos(), true))
			throw new \Exception("Hash algorithm '$algo' is not supported.");

		// check if the key is valid
		$rawPost = file_get_contents('php://input');
		if ($hash !== hash_hmac($algo, $rawPost, $hookSecret))
			throw new \Exception('Hook secret does not match.');

		// execute
		exec('cd /home/tabbie/html && git pull', $out);
		print_r($out);
	}
}
