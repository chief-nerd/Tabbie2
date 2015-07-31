<?php
namespace console\controllers;

use Faker;
use Yii;
/**
 * Created by PhpStorm.
 * User: jareiter
 * Date: 29/04/15
 * Time: 18:11
 */
use yii\console\Controller;

class SampledataController extends Controller
{


	public function actionGenerate($amount_teams, $amount_adj = null, $amount_venues = null)
	{

		if (!is_numeric($amount_teams)) {
			echo "Parameter Teams must be an integer";
			exit();
		}

		if (!is_int($amount_venues)) {
			$amount_venues = $amount_teams / 4;
		}

		if (!is_int($amount_adj)) {
			$amount_adj = $amount_venues * 3;
		}

		echo "Run with:\n";
		echo "Venues:\t" . $amount_venues . "\n";
		echo "Teams:\t" . $amount_teams . "\n";
		echo "Judges:\t" . $amount_adj . "\n";

// use the factory to create a Faker\Generator instance
		$faker = Faker\Factory::create();

		$output_venue = "Name;Active;Group\n";
		$output_teams = "Team Name;Society Name;A.Givenname;A.Surename;A.Email;B.Givenname;B.Surename;B.Email\n";
		$output_adjudicator = "Society;Givenname;Surename;Email;Strength\n";
		$save = [];

		echo "\nVenues:";
		for ($v = 0; $v < $amount_venues; $v++) {
			$group = ($v < $amount_venues / 2) ? "1 Floor" : "2 Floor";
			$line = "Room " . ($v + 1) . ";1;$group\n";
			//echo $line;
			$output_venue .= $line;
			echo ".";
		}
		echo "\tgenerated\n";

		echo "Teams:";
		for ($t = 0; $t < $amount_teams; $t++) {
			$city = $faker->city;
			$save[] = $city;
			$line = $city . " " . strtoupper($faker->randomLetter) . ";" .
				$city . " Debating Society;" .
				addslashes($faker->firstName) . ";" .
				addslashes($faker->lastName) . ";" .
				$faker->email . ";" .
				addslashes($faker->firstName) . ";" .
				addslashes($faker->lastName) . ";" .
				$faker->email . "\n";
			//echo $line;
			$output_teams .= $line;
			echo ".";
		}
		echo "\tgenerated\n";
		echo "Adjudicator:";
		for ($a = 0; $a < $amount_adj; $a++) {

			if ($faker->numberBetween(0, 100) > 40)
				$society = $save[$faker->numberBetween(0, count($save) - 1)] . " Debating Society;";
			else
				$society = $faker->city . " Debating Society;";

			$line = $society .
				addslashes($faker->firstName) . ";" .
				addslashes($faker->lastName) . ";" .
				$faker->email . ";" .
				$faker->numberBetween(0, 99) . "\n";
			//echo $line;
			$output_adjudicator .= $line;
			echo ".";
		}
		echo "\tgenerated\n";

		$base = Yii::getAlias("@frontend/assets/csv/");
		$handle_venue = fopen($base . "Sample_Venues.csv", "w");
		$handle_teams = fopen($base . "Sample_Teams.csv", "w");
		$handle_adj = fopen($base . "Sample_Adjudicators.csv", "w");

		fwrite($handle_venue, $output_venue);
		fclose($handle_venue);

		fwrite($handle_teams, $output_teams);
		fclose($handle_teams);

		fwrite($handle_adj, $output_adjudicator);
		fclose($handle_adj);

	}
}