<?php

namespace app\models\search;

use app\models\Group;
use app\models\PingObject;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class PingObjectSearch extends PingObject
{
    public function rules(): array
    {
        return [
            [['id', 'port', 'port_udp', 'group_id', 'status'], 'integer'],
            ['is_disable', 'boolean'],
            [['ip', 'name', 'updated_at'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = PingObject::find()->with('group');

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
            PingObject::tableName() . '.id' => $this->id,
            PingObject::tableName() . '.port' => $this->port,
            PingObject::tableName() . '.port_udp' => $this->port_udp,
            PingObject::tableName() . '.group_id' => $this->group_id,
            PingObject::tableName() . '.status' => $this->status,
            PingObject::tableName() . '.is_disable' => $this->is_disable,
            PingObject::tableName() . '.updated' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', PingObject::tableName() . '.ip', $this->ip]);

        $query->andFilterWhere(['OR',
            ['like', PingObject::tableName() . '.name', $this->name],
            ['like', PingObject::tableName() . '.address', $this->name],
            ['like', PingObject::tableName() . '.note', $this->name],
        ]);

        return $dataProvider;
    }
}
