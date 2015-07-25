<?php
/**
 * GroupListView.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace common\components\widgets;

use yii\helpers\Html;
use yii\widgets\ListView;
use common\models\Venue;
use Yii;

class GroupListView extends ListView
{
	public $groupBy = "venue.group";

	public $panelClass = "panel-default";

	/**
	 * Renders all data models.
	 *
	 * @return string the rendering result
	 */
	public function renderItems()
	{
		$models = $this->dataProvider->getModels();
		$keys = $this->dataProvider->getKeys();
		$rows = [];
		$output = "";

		$exploded_path = explode(".", $this->groupBy);
		$path = "";
		foreach ($exploded_path as $part) {
			$path .= "['$part']";
		}

		foreach (array_values($models) as $index => $model) {
			/** @var Venue $model */
			$heading = eval('return $model->relatedRecords' . $path . ";");
			$rows[($heading) ? $heading : null][] = $this->renderItem($model, $keys[$index], $index);
		}

		asort($rows);

		foreach ($rows as $heading => $group) {
			$output .= Html::tag("div",
				(($heading) ? Html::tag(
					"div",
					Yii::t("app", "Group:") . " " . $heading,
					["class" => "panel-heading"]
				) : "") .
				Html::tag(
					"div",
					implode($this->separator, $group),
					["class" => "panel-body"]),
				["class" => "panel " . $this->panelClass]
			);
		}

		return $output;
	}

	/**
	 * <div class="panel panel-default">
	 * <div class="panel-heading">Heading</div>
	 * <div class="panel-body">
	 * Body
	 * </div>
	 * </div>
	 */
}
