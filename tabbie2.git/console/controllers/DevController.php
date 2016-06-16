<?php
/**
 * Created by IntelliJ IDEA.
 * User: jareiter
 * Date: 29/09/15
 * Time: 15:46
 */

namespace console\controllers;

use yii\base\Exception;
use yii\console\Controller;

/**
 * Developer Controller for helper functions
 *
 * Class DevController
 * @package console\controllers
 */
class DevController extends Controller
{

    public $defaultAction = "copy-db";

    /**
     * Update the API documentation under /api/web/doc
     */
    public function actionUpdateDoc()
    {
        $out = [];
        $git_root = \Yii::$app->basePath . "/../../";

        $out[] = "\nUpdating API Documentation \n----------------";
        exec("cd $git_root && rm -rf ./tabbie2.git/api/web/doc/ && php ./tabbie2.git/yii api/index ./tabbie2.git/api/controllers,./tabbie2.git/api/models/ ./tabbie2.git/api/web/doc", $out);

        echo join("\n", $out);
    }

    /**
     * Copy the live DB to the local DB
     * ! overrides local data !
     */
    public function actionCopyDb()
    {
        $dump_path = "/Applications/XAMPP/bin/mysqldump";
        $mysql_path = "/Applications/XAMPP/bin/mysql";

        $filepath = false;

        try {

            $test = exec("[ -f $dump_path ] && echo 1 || echo 0");
            if (!$test) {
                throw new Exception("mysqldump not found in $dump_path");
            }

            $test = exec("[ -f $mysql_path ] && echo 1 || echo 0");
            if (!$test) {
                throw new Exception("mysql not found in $mysql_path");
            }

            //***** EXPORT *******/

            $live_config_path = \Yii::getAlias("@app") . "/../environments/prod/common/config/main-local.php";
            $live_config = require($live_config_path);

            $db_live = $live_config["components"]["db"];

            $dsn = $this->split_dsn($db_live["dsn"]);
            $username = $db_live["username"];
            $password = $db_live["password"];
            $url = $dsn[1];
            $database = $dsn[3];
            $time = date("Y.m.d.H.i.s");
            $filename = "$database.$time.sql";
            $filepath = \Yii::getAlias("@runtime/$filename");


            $execute_string = "$dump_path --user=$username --password=$password --host=$url --port=3306 --protocol=tcp --default-character-set=utf8 --single-transaction=TRUE \"$database\" > $filepath";
            echo "... downloading ...\n";
            $result = exec($execute_string);

            //****** IMPORT ********//

            $dev_config_path = \Yii::getAlias("@app") . "/../common/config/main-local.php";
            $dev_config = require($dev_config_path);

            $db_dev = $dev_config["components"]["db"];

            $dsn = $this->split_dsn($db_dev["dsn"]);
            $username = $db_dev["username"];
            $password = $db_dev["password"];
            $url = $dsn[1];
            $database = $dsn[3];

            $execute_string = "$mysql_path --protocol=tcp --host=$url --user=$username --password=$password --port=3306 --default-character-set=utf8 --database=$database  < \"$filepath\"
";
            echo "\n ... deploying locally ...\n";
            $result = exec($execute_string);

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        //**** CLEAN UP *****/
        if ($filepath !== false) {
            echo "\n ... clean up ...\n";
            unlink($filepath);
        }

        echo "\n=== Finished! ===";
        return 0;
    }

    private function split_dsn($dsn)
    {
        return preg_split("/[;,=]+/", $dsn);
    }
}