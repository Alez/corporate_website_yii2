<?php

namespace backend\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\catalog\models\Age;

/**
 * AgeSearch represents the model behind the search form about `common\modules\catalog\models\Age`.
 */
class AgeSearch extends Age
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'updated_at'], 'integer'],
            [['name', 'slug', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Age::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        // Специальный фильтр для даты
        if ($this->getAttribute('created_at') !== '') {
            $createDateStart = \DateTime::createFromFormat('d-m-y', $this->created_at);
            if ($createDateStart) {
                $createDateStart->modify('Today');
                $createDateEnd = clone $createDateStart;
                $createDateEnd->modify('+1 day');

                $query->andWhere(['between', 'created_at', $createDateStart->getTimestamp(), $createDateEnd->getTimestamp()]);
            }
        }

        return $dataProvider;
    }
}
