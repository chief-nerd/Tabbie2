<?
use  yii\widgets\DetailView;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'hostedby.fullname:text:Hosted By',
        [
            "attribute" => 'convenor',
            'label' => "Convenor",
            'format' => 'raw',
            'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->convenors, "name")),
        ],
        [
            "attribute" => 'CATeam',
            'label' => "CA Team",
            'format' => 'raw',
            'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->cAs, "name")),
        ],
        [
            "attribute" => 'tabmaster',
            'label' => "Tab Master",
            'format' => 'raw',
            'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->tabmasters, "name")),
        ],
        [
            "attribute" => 'start_date',
            'format' => 'raw',
            'value' => Yii::$app->formatter->asDateTime($model->start_date, "short"),
        ],
        [
            "attribute" => 'end_date',
            'format' => 'raw',
            'value' => Yii::$app->formatter->asDateTime($model->end_date, "short"),
        ],
        [
            "attribute" => 'timezone',
            'format' => 'raw',
            'value' => $model->getFormatedTimeZone(),
        ],
    ],
])
?>