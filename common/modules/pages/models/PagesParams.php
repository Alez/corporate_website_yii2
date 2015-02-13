<?php

namespace common\modules\pages\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "pages_params".
 *
 * @property string $id
 * @property string $page_id
 * @property string $value
 *
 * @property PagesTemplatesParams $template
 * @property Pages $page
 */
class PagesParams extends \yii\db\ActiveRecord
{
    /* @var \yii\web\UploadedFile */
    public $uploadFile;

    /* @var PagesParams[] Параметры страниц используемые в этом шаблоне */
    public static $pagesParams;

    /* @var PagesTemplatesParams[] Кэш всех возможных параметров для определения типа при создании экземпляра */
    public static $pagesParamsTemplateCache;

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
            [['page_id'], 'required'],
            [['page_id'], 'integer'],
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
        ];
    }

    /**
     * @param array $row
     *
     * @return PagesParamsInterface
     * @throws NotFoundHttpException
     */
    public static function instantiate($row)
    {
        if (is_null(self::$pagesParamsTemplateCache)) {
            self::$pagesParamsTemplateCache = PagesTemplatesParams::find()->indexBy('id')->asArray()->all();
        }

        switch (self::$pagesParamsTemplateCache[$row['pages_templates_params_id']]['type']) {
            case TextParams::TYPE:
                $param = new TextParams();
                break;
            case RedactorParams::TYPE:
                $param =  new RedactorParams();
                break;
            case FileParams::TYPE:
                $param =  new FileParams();
                break;
            case MultifileParams::TYPE:
                $param =  new MultifileParams();
                break;
            case ImageParams::TYPE:
                $param =  new ImageParams();
                break;
            case MultiimageParams::TYPE:
                $param =  new MultiimageParams();
                break;
            case TextareaParams::TYPE:
                $param =  new TextareaParams();
                break;
            case DatetimeParams::TYPE:
                $param =  new DatetimeParams();
                break;
            default:
                throw new NotFoundHttpException('Тип параметра не найден');
        }

        return $param;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
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
        $template = $template ? (int)$template : PagesTemplates::EMPTY_TEMPLATE_ID;
        $pagesTemplatesParams = PagesTemplatesParams::find()->where(['pages_templates_id' => $template])->all();
        $newParams = [];

        /* @var $template PagesTemplatesParams  */
        foreach ($pagesTemplatesParams as $template) {
            $type = __NAMESPACE__ . '\\' . ucfirst($template->getAttribute('type')) . 'Params';
            try {
                $newParam = new $type();
            } catch (\Exception $e) {
                throw new NotFoundHttpException('Тип параметра не найден');
            }
            $newParam->setAttribute('pages_templates_params_id', $template->getAttribute('id'));
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
            ->with('template')
            ->where(['page_id' => $page['id']])
            ->all();

        self::$pagesParams[$page['slug']] = ArrayHelper::index(self::$pagesParams[$page['slug']], function($model) {
            return $model->template->slug;
        });
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
