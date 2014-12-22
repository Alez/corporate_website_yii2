<?php

namespace common\modules\pages\models;

use Yii;
use common\modules\pages\models\PagesTemplates;
use common\modules\files\models\Files;
use common\components\helpers\SerializeHelper;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "pages_params".
 *
 * @property string $id
 * @property string $page_id
 * @property string $value
 * @property string $type
 *
 * @property PagesTemplatesParams $pagesTemplatesParams
 * @property Pages $page
 */
class PagesParams extends \yii\db\ActiveRecord
{
    /* @var \yii\web\UploadedFile */
    public $uploadFile;

    /* @var PagesParams[] Параметры страниц используемые в этом шаблоне */
    public static $pagesParams;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'type'], 'required'],
            [['page_id'], 'integer'],
            [['type'], 'string'],
            [['value'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'page_id' => 'Page ID',
            'value'   => 'Value',
            'type'    => 'Type',
        ];
    }

    /**
     * @param array $row
     *
     * @return FileParams|ImageParams|MultifileParams|MultiimageParams|RedactorParams|TextParams|static
     * @throws NotFoundHttpException
     */
    public static function instantiate($row)
    {
        switch ($row['type']) {
            case TextParams::TYPE:
                return new TextParams();
            case RedactorParams::TYPE:
                return new RedactorParams();
            case FileParams::TYPE:
                return new FileParams();
            case MultifileParams::TYPE:
                return new MultifileParams();
            case ImageParams::TYPE:
                return new ImageParams();
            case MultiimageParams::TYPE:
                return new MultiimageParams();
            default:
                throw new NotFoundHttpException('Тип параметра не найден');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesTemplatesParams()
    {
        return $this->hasOne(PagesTemplatesParams::className(), ['id' => 'pages_templates_params_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Pages::className(), ['id' => 'page_id']);
    }

    /**
     * Создаст массив пустых параметров для новой таблицы, сджоинит с шаблоном
     *
     * @param $template string|null Id шаблона страницы
     *
     * @return PagesParams[]
     * @throws NotFoundHttpException;
     */
    public static function newPageParams($template)
    {
        $template = $template ? (int)$template : (int)PagesTemplates::EMPTY_TEMPLATE_ID;
        $pagesTemplatesParams = PagesTemplatesParams::find()->where(['pages_templates_id' => $template])->all();
        $newParams = [];

        /* @var $pagesTemplatesParam PagesTemplatesParams  */
        foreach ($pagesTemplatesParams as $pagesTemplatesParam) {
            $type = __NAMESPACE__ . '\\' . ucfirst($pagesTemplatesParam->getAttribute('type')) . 'Params';
            try {
                $newParam = new $type();
            } catch (\Exception $e) {
                throw new NotFoundHttpException('Тип параметра не найден');
            }
            $newParam->setAttribute('pages_templates_params_id', $pagesTemplatesParam->getAttribute('id'));
            $newParam->setAttribute('slug', $pagesTemplatesParam->getAttribute('slug'));
            $newParam->setAttribute('type', $pagesTemplatesParam->getAttribute('type'));
            $newParam->setAttribute('id', '');
            $newParams[] = $newParam;
        }

        return $newParams;
    }

    /**
     * Достанет все id фалов на которые ссылаются модели в этом массиве
     *
     * @param $pageParams TextParams[]|mixed Массив параметров страницы
     *
     * @return array
     */
    public static function extractAllFilesId($pageParams)
    {
        $filesId = [];
        // Достанем id одиночных файлов
        foreach ($pageParams as $param) {
            if (!$param->isFileType()) {
                continue;
            }
            if ($param->isMultifileType()) {
                $filesId = array_merge($filesId, $param->getValue());
            } else {
                if ($singleFileId = (int)$param->getAttribute('value')) {
                    $filesId[] = $singleFileId;
                }
            }
        }

        return $filesId;
    }

    /**
     * Дёрнет параметры страницы по её Id
     *
     * @param $pageId
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function retrieveParams($page)
    {
        self::$pagesParams[$page['slug']] = PagesParams::find()
            ->where(['page_id' => $page['id']])
            ->indexBy('slug')
            ->all();
    }

    /**
     * Найдет параметр страницы по его slug.
     * TODO это пока не сделано
     * Если используется несколько страницы как источник параметров, можно указать вторым параметром, с какой страницы
     * значение нас интересует. В противном случае первое найденное будет возвращено.
     *
     * @param string $slug
     * @param string|null $pageSlug
     *
     * @return string|null
     */
    public static function get($slug, $pageSlug = null)
    {
        if (!is_array(self::$pagesParams)) {
            return null;
        }

        if ($pageSlug) {
            if (isset(self::$pagesParams[$pageSlug][$slug])) {
                return self::$pagesParams[$pageSlug][$slug]->getAttribute('value');
            }
        } else {
            foreach (self::$pagesParams as $pageParams) {
                if ($pageParams[$slug]) {
                    return $pageParams[$slug]->getAttribute('value');
                }
            }
        }

        return null;
    }
}
