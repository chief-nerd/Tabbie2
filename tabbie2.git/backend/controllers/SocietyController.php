<?php

namespace backend\controllers;

use common\models\Adjudicator;
use common\models\InSociety;
use common\models\search\UserSearch;
use common\models\Team;
use common\models\Tournament;
use kartik\helpers\Html;
use Yii;
use common\models\Society;
use common\models\search\SocietySearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Country;
use yii\filters\AccessControl;

/**
 * SocietyController implements the CRUD actions for Society model.
 */
class SocietyController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->isMaintainer());
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
     * Lists all Society models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SocietySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Society model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchBySociety(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'memberSearchModel' => $searchModel,
            'memberDataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Society model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Society the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Society::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * A society with another Society model.
     *
     * @param integer $id
     * @param integer $other
     *
     * @return mixed
     */
    public function actionMerge($id, $other, $backURL = null)
    {

        //This updates the individuals that are only members of the child society.

        $inSociety = Yii::$app->db->createCommand('UPDATE in_society A LEFT JOIN in_society B ON B.user_id = A.user_id AND B.society_id =:other SET A.society_id =:other WHERE B.society_id IS NULL AND A.society_id =:id')
            ->bindValue('other', $other)
            ->bindValue('id', $id)
            ->query();

        //The following updates those individuals that have both societies to have the longest possible time that they've been in the society, to avoid the unique key constraint.

        $inSociety = Yii::$app->db->createCommand('UPDATE in_society A LEFT JOIN in_society B ON B.user_id = A.user_id AND B.society_id =:other SET A.ending = (
CASE 
        WHEN A.ending IS NULL OR B.ending IS NULL THEN NULL
        WHEN A.ending >= B.ending THEN A.ending
        WHEN B.ending >= A.ending THEN B.ending
        ELSE                                        NULL
    END)
WHERE B.society_id IS NOT NULL AND A.society_id =:id')
            ->bindValue('other', $other)
            ->bindValue('id', $id)
            ->query();

        $inSociety = Yii::$app->db->createCommand('UPDATE in_society A
LEFT JOIN in_society B ON B.user_id = A.user_id AND B.society_id =:other
SET A.starting = (CASE
        WHEN A.ending IS NULL OR B.ending IS NULL THEN NULL
        WHEN A.starting >= B.starting THEN B.starting
        WHEN B.starting >= A.starting THEN A.starting
        ELSE                                        NULL
    END)
WHERE B.society_id IS NOT NULL AND A.society_id =:id')
            ->bindValue('other', $other)
            ->bindValue('id', $id)
            ->query();

        //Once they are combined, we can delete.

        $inSociety = Yii::$app->db->createCommand('DELETE A.* FROM in_society A LEFT JOIN in_society B ON B.user_id = A.user_id AND B.society_id =:other WHERE B.society_id IS NOT NULL AND A.society_id =:id')
            ->bindValue('other', $other)
            ->bindValue('id', $id)
            ->query();

        //InSociety::updateAll(["society_id" => $other], ["society_id" => $id]);
        Tournament::updateAll(["hosted_by_id" => $other], ["hosted_by_id" => $id]);
        Team::updateAll(["society_id" => $other], ["society_id" => $id]);
        Adjudicator::updateAll(["society_id" => $other], ["society_id" => $id]);
        Society::deleteAll(["id" => $id]);

        if ($backURL === null) {
            $backURL = "index";
        }

        return $this->redirect($backURL);
    }

    /**
     * Creates a new Society model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Society();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Society model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Society model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionImport()
    {

        echo "<table>";
        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstanceByName('csvFile');
            $import = [];
            $row = 0;
            if ($file && ($handle = fopen($file->tempName, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ";")) !== false) {

                    if ($row == 0) { //Don't use first column
                        $row++;
                        continue;
                    }

                    if (($num = count($data)) < 4) {
                        throw new \yii\base\Exception("500", Yii::t("app", "File Syntax Wrong"));
                    }
                    $import[] = [
                        "fullname" => utf8_encode(str_replace("_", "", $data[0])),
                        "abr" => utf8_encode($data[1]),
                        "city" => utf8_encode($data[2]),
                        "country_id" => utf8_encode($data[3]),
                    ];
                    $row++;
                }
                fclose($handle);
            }
            $c_import = count($import);
            for ($i = 0; $i < $c_import; $i++) {
                $l = $import[$i];

                if (trim($l['fullname']) != "") {
                    $country = Country::find()->where(["LIKE", "name", $l['country_id']])->one();
                    if ($country instanceof Country)
                        $l["country_id"] = $country->id;
                    else
                        $l["country_id"] = Country::COUNTRY_UNKNOWN_ID;

                    $name = trim($l['fullname']);
                    $socMatch = Society::find()
                        ->where(["LIKE", "fullname", $name]);

                    if ($l['abr'] != "")
                        $socMatch->orWhere(["abr" => $l['abr']]);

                    $socMatch = $socMatch->all();

                    if (count($socMatch) == 0) {

                        $soc = new Society($l);
                        if ($l["city"] == "")
                            $l["city"] = null;

                        if ($l["abr"] == "")
                            $l["abr"] = $soc->generateAbr($l['fullname']);
                        else {
                            $l["abr"] = Society::uniqueAbr($l["abr"]);
                        }

                        $soc = new Society($l);
                        if (!$soc->save()) {
                            $string = print_r($soc->getErrors(), true) . " " . print_r($l, true);
                            throw new Exception($string);
                        }
                    } else if (count($socMatch) == 1) {
                        //already exist
                        $soc = $socMatch[0];
                        $soc->load($l);
                        if (!$soc->save())
                            throw new Exception(print_r($soc->getErrors(), true));
                    } else {
                        $debugLine = "<tr><th>" . $l['fullname'] . " (" . $l['abr'] . ")</th><td>";
                        $items = ArrayHelper::map($socMatch, "id", "fullname");
                        $debugLine .= Html::ul($items);
                        $debugLine .= "</td></tr>";

                        echo $debugLine;
                    }
                }
            }
        }
        echo "</table>";

        return $this->render("import");
    }

    /**
     * Returns 20 countries in an JSON List
     *
     * @param type $search
     * @param type $id
     */
    public function actionCountries(array $search = null, $cid = null)
    {
        $search["term"] = HtmlPurifier::process($search["term"]);
        $out = ['more' => false];
        if (!is_null($search["term"]) && $search["term"] != "") {
            $query = new \yii\db\Query;
            $query->select(["id", "name as text"])
                ->from('country')
                ->where('name LIKE "%' . $search["term"] . '%"')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($cid > 0) {
            $out['results'] = ['id' => $cid, 'text' => Country::findOne($cid)->name];
        } else {
            $out['results'] = ['id' => 0, 'text' => 'No matching records found'];
        }
        echo Json::encode($out);
    }

}
