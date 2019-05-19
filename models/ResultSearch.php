<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Result;

/**
 * ResultSearch represents the model behind the search form of `app\models\Result`.
 */
class ResultSearch extends Result
{
    public $task;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'task_id'], 'integer'],
            [['avito_url', 'data', 'created_at', 'task'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Result::find()->orderBy('created_at');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('task');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'task_id' => $this->task_id,
            'created_at' => $this->created_at,
        ]);

        $dataProvider->sort->attributes['task'] = [
            'asc' => ['task.name' => SORT_ASC],
            'desc' => ['task.name' => SORT_DESC],
        ];

        $query->andFilterWhere(['like', 'tasks.name', $this->task]);

        return $dataProvider;
    }
}
