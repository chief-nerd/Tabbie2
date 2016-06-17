<?php
namespace api\controllers;

use api\models\ApiUser;
use Yii;
use yii\authclient\OAuth2;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function init()
    {
        parent::init();
        //We want a session in this controller, for profile and stuff
        Yii::$app->user->enableSession = true;
        Yii::$app->user->loginUrl = "login";
        Yii::$app->user->enableAutoLogin = true;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['jwt'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'error', 'jwt'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->isAdmin());
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Return the allowed action for this object
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            if (count(Yii::$app->user->identity->apiUser) == 0) {
                $api = new \common\models\ApiUser([
                    "user_id" => Yii::$app->user->id,
                    "access_token" => Yii::$app->getSecurity()->generateRandomString(20),
                ]);
                $api->save();
            }
            return $this->redirect("profile");

        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string
     */
    public function actionProfile()
    {
        $model = Yii::$app->user->identity;

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    public function beforeAction($action)
    {
        if ($action->id == "jwt")
            $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionJwt()
    {
        $model = new LoginForm([
            "email" => Yii::$app->request->post('email'),
            "password" => Yii::$app->request->post('password')
        ]);
        if ($model->login()) {
            return ["status" => 200, "authorization" => Yii::$app->user->identity->apiUser->getAuthorization()];
        }
        return ["status" => 403, "errors" => $model->getErrors()];
    }
}
