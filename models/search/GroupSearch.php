<?php

namespace app\models\search;

use app\models\Group;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupSearch extends Group
{
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['is_disable', 'boolean'],
            ['name', 'safe'],
        ];
    }

    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Group::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_disable' => $this->is_disable,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
