<?php
/**
 * TestController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace frontend\controllers;

use common\models\Society;
use yii\web\Controller;
use Faker;

class TestController extends Controller
{


	public function actionAdjudicators($amount = 20)
	{
		return '{"status":200,"items":[{"id":0,"firstname":"AAA","lastname":"BBB","society":{"name":"Pauls Debating","abr":"PTDS1","city":"West Carrieport","country":"Turkey"},"email":"Myrtle72@Dare.biz"},{"id":1,"firstname":"Mitchell","lastname":"Reichert","society":{"name":"Angelinestad Debating Society","abr":"ADS5","city":"North Jerod","country":"Niue"},"email":"Libbie.Ziemann@Armstrong.com"},{"id":2,"firstname":"Willis","lastname":"Watsica","society":{"name":"Elzabury Debating Society","abr":"EDS1","city":"Port Maximilianport","country":"Nauru"},"email":"Jeanne.Balistreri@yahoo.com"},{"id":3,"firstname":"Adonis","lastname":"Kassulke","society":{"name":"East Borisbury Debating Society","abr":"EBDS","city":"Port Tobinburgh","country":"Iran"},"email":"Otilia06@yahoo.com"},{"id":4,"firstname":"Everett","lastname":"Feil","society":{"name":"South Lambertbury Debating Society","abr":"SLDS","city":"West Heloise","country":"Liechtenstein"},"email":"xBoyle@yahoo.com"},{"id":5,"firstname":"Christine","lastname":"Kunde","society":{"name":"Mooreside Debating Society","abr":"MDS7","city":"Dickifort","country":"Kuwait"},"email":"yEmard@Marquardt.com"},{"id":6,"firstname":"Jermaine","lastname":"Boyer","society":{"name":"Westfurt Debating Society","abr":"WDS","city":"Lake Leland","country":"Mozambique"},"email":"Randi77@gmail.com"},{"id":7,"firstname":"Cortney","lastname":"Hammes","society":{"name":"North Maevebury Debating Society","abr":"NMDS1","city":"Port Nathanshire","country":"Honduras"},"email":"zSauer@hotmail.com"},{"id":8,"firstname":"Hazel","lastname":"Kihn","society":{"name":"Gibsonburgh Debating Society","abr":"GDS3","city":"Stanfordmouth","country":"Nepal"},"email":"Bergstrom.Ottilie@Rau.com"},{"id":9,"firstname":"Maurice","lastname":"Turner","society":{"name":"East Marjorymouth Debating Society","abr":"EMDS","city":"New Megane","country":"Egypt"},"email":"eConsidine@gmail.com"},{"id":10,"firstname":"Lilla","lastname":"Koepp","society":{"name":"North Rethaborough Debating Society","abr":"NRDS","city":"Julianfurt","country":"Myanmar"},"email":"Wilbert.Adams@Oberbrunner.com"},{"id":11,"firstname":"Cleta","lastname":"Breitenberg","society":{"name":"New Eleonoremouth Debating Society","abr":"NEDS3","city":"New Kenna","country":"Senegal"},"email":"Hubert17@hotmail.com"},{"id":12,"firstname":"Reece","lastname":"Vandervort","society":{"name":"New Nasir Debating Society","abr":"NNDS1","city":"Zulaufville","country":"United States of America"},"email":"Chaya49@hotmail.com"},{"id":13,"firstname":"Arturo","lastname":"Jerde","society":{"name":"Alycechester Debating Society","abr":"ADS5","city":"Marvinfurt","country":"Vietnam"},"email":"Mafalda66@Predovic.org"},{"id":14,"firstname":"Keanu","lastname":"Hintz","society":{"name":"Krismouth Debating Society","abr":"KDS6","city":"Brownbury","country":"Comoros"},"email":"Zackary42@yahoo.com"},{"id":15,"firstname":"Ramiro","lastname":"Mann","society":{"name":"Port Genoveva Debating Society","abr":"PGDS3","city":"New Asabury","country":"Puerto Rico"},"email":"Kaci87@yahoo.com"},{"id":16,"firstname":"Stacy","lastname":"Williamson","society":{"name":"East Albinton Debating Society","abr":"EADS","city":"Rueckerland","country":"Switzerland"},"email":"Carmel.Cummerata@Doyle.com"},{"id":17,"firstname":"Leopoldo","lastname":"Auer","society":{"name":"New Cullenborough Debating Society","abr":"NCDS","city":"Botsfordfurt","country":"Cambodia"},"email":"Alexander.Macejkovic@Trantow.com"},{"id":18,"firstname":"Gilberto","lastname":"Robel","society":{"name":"Lolitamouth Debating Society","abr":"LDS1","city":"Kovacekfort","country":"Eritrea"},"email":"Lucinda.Paucek@gmail.com"},{"id":19,"firstname":"Zachery","lastname":"King","society":{"name":"West Natalie Debating Society","abr":"WNDS","city":"Bruenside","country":"Anguilla"},"email":"oEmard@Tremblay.com"}]}';
		$faker = Faker\Factory::create();

		$items = [];

		for ($i = 0; $i < $amount; $i++) {
			$f = $faker->firstName;
			$l = $faker->lastName;
			$society = $faker->city . " Debating Society";

			$item = [
				"id"       => $i,
				"firstname" => $f,
				"lastname" => $l,
				"society"  => [
					"name" => $society,
					"abr"  => Society::generateAbr($society),
					"city" => $faker->city,
					"country" => $faker->country,
				],
				"email"    => $faker->email
			];
			$items[] = $item;
		}

		return json_encode([
			"status" => 200,
			"items" => $items,
		]);
	}

	public function actionTeams($amount = 10)
	{
		return '{"status":200,"items":[{"id":0,"name":"Gibson, Daugherty and Borer","speaker":[{"firstname":"AAA","lastname":"BBB","email":"Alta.Gutkowski@gmail.com"},{"firstname":"Marisa","lastname":"Effertz","email":"Ruby17@hotmail.com"}],"society":{"name":"North Jaiden Debating Society","abr":"NJDS1","city":"Turnermouth","country":"Guernsey"}},{"id":1,"name":"Bauch-Batz","speaker":[{"firstname":"Laurianne","lastname":"Littel","email":"Watsica.Brielle@gmail.com"},{"firstname":"Lucy","lastname":"Hane","email":"kCasper@yahoo.com"}],"society":{"name":"East Lucindachester Debating Society","abr":"ELDS1","city":"New Kacieburgh","country":"Indonesia"}},{"id":2,"name":"Lynch Group","speaker":[{"firstname":"Cooper","lastname":"Ferry","email":"Antwan.OKon@Hand.com"},{"firstname":"Jazmin","lastname":"Goodwin","email":"Barrows.Clemens@hotmail.com"}],"society":{"name":"Rogahnborough Debating Society","abr":"RDS3","city":"Aaliyahfurt","country":"Kiribati"}},{"id":3,"name":"Nader LLC","speaker":[{"firstname":"Karelle","lastname":"Harvey","email":"kWehner@Collier.com"},{"firstname":"Murphy","lastname":"Bayer","email":"Kaleigh.Wiza@gmail.com"}],"society":{"name":"Lauriemouth Debating Society","abr":"LDS1","city":"Port Wavahaven","country":"Italy"}},{"id":4,"name":"Ankunding, Ondricka and Predovic","speaker":[{"firstname":"Angela","lastname":"Kovacek","email":"Klocko.Bertha@hotmail.com"},{"firstname":"Lonie","lastname":"Kertzmann","email":"Terence.Kshlerin@Feeney.org"}],"society":{"name":"Melbamouth Debating Society","abr":"MDS7","city":"South Donald","country":"Namibia"}},{"id":5,"name":"Nikolaus-Schaden","speaker":[{"firstname":"Lauriane","lastname":"Tillman","email":"Eryn.Bins@gmail.com"},{"firstname":"Preston","lastname":"Schultz","email":"Madyson.Gorczany@Boehm.com"}],"society":{"name":"North Germanchester Debating Society","abr":"NGDS1","city":"East Valentin","country":"Djibouti"}},{"id":6,"name":"Fadel, Gerlach and Larson","speaker":[{"firstname":"Elias","lastname":"Berge","email":"Julio09@hotmail.com"},{"firstname":"Colton","lastname":"Kessler","email":"Lockman.Johnny@gmail.com"}],"society":{"name":"Noemifort Debating Society","abr":"NDS2","city":"Torphyville","country":"Argentina"}},{"id":7,"name":"Mante, Zieme and Lueilwitz","speaker":[{"firstname":"Abbey","lastname":"Witting","email":"Emmanuelle.Reichel@OHara.com"},{"firstname":"Zander","lastname":"Stokes","email":"Anabel93@Sauer.net"}],"society":{"name":"Port Enoch Debating Society","abr":"PEDS1","city":"Schusterside","country":"Latvia"}},{"id":8,"name":"Jacobs, Kilback and Wolf","speaker":[{"firstname":"Darian","lastname":"Jacobs","email":"oRomaguera@hotmail.com"},{"firstname":"Hollie","lastname":"Abshire","email":"zEmmerich@gmail.com"}],"society":{"name":"New Stanland Debating Society","abr":"NSDS1","city":"Altenwerthmouth","country":"Tokelau"}},{"id":9,"name":"Connelly-Mayert","speaker":[{"firstname":"Louie","lastname":"Labadie","email":"Santiago.Nader@yahoo.com"},{"firstname":"Chandler","lastname":"Armstrong","email":"Sawayn.Keshawn@Mante.info"}],"society":{"name":"Lake Krystal Debating Society","abr":"LKDS4","city":"West Eddie","country":"Benin"}}]}';
		$faker = Faker\Factory::create();

		$items = [];

		for ($i = 0; $i < $amount; $i++) {
			$society = $faker->city . " Debating Society";

			$item = [
				"id"      => $i,
				"name"    => $faker->company,
				"speaker" => [
					[
						"firstname" => $faker->firstName,
						"lastname" => $faker->lastName,
						"email"    => $faker->email,
					],
					[
						"firstname" => $faker->firstName,
						"lastname" => $faker->lastName,
						"email"    => $faker->email,
					]
				],
				"society" => [
					"name" => $society,
					"abr"  => Society::generateAbr($society),
					"city" => $faker->city,
					"country" => $faker->country,
				],
			];
			$items[] = $item;
		}

		return json_encode([
			"status" => 200,
			"items" => $items,
		]);
	}

}