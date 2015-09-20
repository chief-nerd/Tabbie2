<?php

namespace common\models\search;

use common\models\Message;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Panel;

/**
 * PanelSearch represents the model behind the search form about `\common\models\Panel`.
 */
class MessageSearch extends Message {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id'], 'integer'],
			[['translation'], 'string'],
			[['translation'], 'trim'],
			[['language'], 'string', 'max' => 16]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params, $id) {
		$query = Message::find()
			->leftJoin("source_message", "source_message.id = message.id")
			->where(["message.language" => $id]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 80,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'or',
			['like', 'translation', $this->translation],
			['like', 'message', $this->translation],
		]);

		return $dataProvider;
	}
}
