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
class DeployController extends Controller {

	public $enableCsrfValidation = false;

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
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

	public function actionGitPushHook() {
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

		/** @var string $git_root BasePath to the Root git directory */
		$git_root = Yii::$app->basePath . "/../../";

		$out[] = "<h3>=== Git Pulls ===</h3>";
		// execute
		exec("cd $git_root && git pull", $out);

		exec("cd $git_root && git submodule foreach git pull origin master", $out);

		//make migrations
		$out[] = "<h3>=== Migrate ===</h3>";
		exec("php $git_root/tabbie2.git/yii migrate/up --interactive=0", $out);

		//Flush Caches
		$out[] = "<h3>=== Flush Cache ===</h3>";
		exec("php $git_root/tabbie2.git/yii cache/flush-schema --interactive=0", $out);
		exec("php $git_root/tabbie2.git/yii cache/flush-all --interactive=0", $out);

		//output
		print_r($out);

		$html = "<h2>Git Pull Report $out[1]</h2>\n";
		$html .= implode("<br>\n", $out);

		Yii::$app->mailer->compose()
		                 ->setFrom(['git-report@tabbie.org' => "Git Pull Report"])
		                 ->setTo(Yii::$app->params["supportEmail"])
			->setSubject($out[1] . " " . $out[2])
		                 ->setHtmlBody($html)
		                 ->send();

	}
}
