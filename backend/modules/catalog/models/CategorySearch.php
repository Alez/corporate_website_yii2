<?php

namespace backend\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\catalog\models\Category;

/**
 * CategorySearch represents the model behind the search form about `common\modules\catalog\models\Category`.
 */
class CategorySearch extends Category
{
    public $parent;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order', 'parent_id', 'updated_at'], 'integer'],
            [['name', 'description_title', 'description_body', 'price_id', 'slug', 'seo_title', 'seo_description', 'parent', 'created_at'], 'safe'],
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
        $query = Category::find()->select('`category`.`id`, `category`.`name`, `category`.`parent_id`')->joinWith('parent');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['parent'] = [
            'asc'  => ['parent_id' => SORT_ASC],
            'desc' => ['parent_id' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
                '`category`.id'        => $this->id,
                '`category`.order'     => $this->order,
                '`category`.parent_id' => $this->parent_id,
        ]);

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

        $query->andFilterWhere(['like', '`category`.`name`', $this->name]);

        // Специальный фильтр для поля родительского каталога
        if ($this->parent !== null && $this->parent !== '') {
            if (is_numeric(stripos('главная', $this->parent)) || is_numeric(stripos('Главная', $this->parent))) {
                $query->andWhere(['like', '`category`.`parent_id`', 0]);
            } else {
                $parentCategory = Category::find()->where('`name` LIKE \'%' . $this->parent . '%\'')->one();
                if ($parentCategory) {
                    $query->andWhere(['`category`.`parent_id`' => $parentCategory->id]);
                } else {
                    $query->andWhere(['id' => 0]);
                }
            }
        }

        return $dataProvider;
    }
}
