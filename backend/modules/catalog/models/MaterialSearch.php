<?php

namespace backend\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\catalog\models\Material;
use common\modules\catalog\models\Category;

/**
 * MaterialSearch represents the model behind the search form about `common\modules\catalog\models\Material`.
 */
class MaterialSearch extends Material
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'preview_id', 'coloring_image_id', 'updated_at'], 'integer'],
            [['slug', 'name', 'annotation', 'body', 'baseCategory', 'created_at'], 'safe'],
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
        $query = Material::find()->with('baseCategory');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['baseCategory'] = [
            'asc'  => ['category.id' => SORT_ASC],
            'desc' => ['category.id' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'preview_id' => $this->preview_id,
            'coloring_image_id' => $this->coloring_image_id,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'body', $this->body]);

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

        // Специальный фильтр для поля каталога
        if ($this->baseCategory !== null && $this->baseCategory !== '') {
            if (is_numeric(stripos('главная', $this->baseCategory)) || is_numeric(stripos('Главная', $this->baseCategory))) {
                $query->andWhere(['like', '`baseCategory`.`id`', 0]);
            } else {
                $parentCategories = Category::find()->where('`name` LIKE \'%' . $this->baseCategory . '%\'')->all();
                if (count($parentCategories) !== 0) {
                    $parentIds = [];
                    foreach ($parentCategories as $parentCategory) {
                        $parentIds[] = $parentCategory->id;
                    }
                    $query->andWhere(['in', '`baseCategory`.`id`', $parentIds]);
                } else {
                    $query->andWhere(['`product`.`id`' => 0]);
                }
            }
        }

        return $dataProvider;
    }
}
