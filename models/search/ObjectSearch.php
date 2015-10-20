<?php

namespace app\models\search;

use app\models\Group;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Object;
use yii\db\ActiveQuery;

/**
 * ObjectSearch represents the model behind the search form about `app\models\Object`.
 */
class ObjectSearch extends Object
{
    public function rules()
    {
        return [
            [['id', 'port', 'port_udp', 'group_id', 'status'], 'integer'],
            [['is_disable'], 'boolean'],
            [['ip', 'name', 'updated'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Object::find()->with('group');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $query->joinWith(['group' => function (ActiveQuery $query) {
            $query->from(['group' => Group::tableName()]);
        }]);

        $dataProvider->sort->attributes['group_id'] = [
            'asc' => ['group.name' => SORT_ASC],
            'desc' => ['group.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Object::tableName() . '.id' => $this->id,
            Object::tableName() . '.port' => $this->port,
            Object::tableName() . '.port_udp' => $this->port_udp,
            Object::tableName() . '.group_id' => $this->group_id,
            Object::tableName() . '.status' => $this->status,
            Object::tableName() . '.is_disable' => $this->is_disable,
            Object::tableName() . '.updated' => $this->updated,
        ]);

        $query->andFilterWhere(['like', Object::tableName() . '.ip', $this->ip]);

        $query->andFilterWhere(['OR',
            ['like', Object::tableName() . '.name', $this->name],
            ['like', Object::tableName() . '.address', $this->name],
            ['like', Object::tableName() . '.note', $this->name],
        ]);

        return $dataProvider;
    }
}
