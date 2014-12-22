<?php

namespace backend\modules\news\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\news\models\News;

/**
 * NewsSearch represents the model behind the search form about `common\modules\news\models\News`.
 */
class NewsSearch extends News
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'slug', 'announce', 'content', 'created_at', 'updated_at'], 'safe'],
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
        $query = News::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'announce', $this->announce])
            ->andFilterWhere(['like', 'content', $this->content]);

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
