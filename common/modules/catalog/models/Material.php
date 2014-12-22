<?php

namespace common\modules\catalog\models;

use Yii;
use common\modules\files\models\Files;
use common\components\helpers\SerializeHelper;
use yii\helpers\ArrayHelper;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "material".
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $annotation
 * @property string $preview_id
 * @property string $body
 * @property string $coloring_image_id
 * @property string $coloring_image_bw_id
 * @property string $post_images_id
 * @property array $categoryIds
 * @property string $title
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 *
 * @property LinkMaterialAge[] $linkMaterialAges
 * @property Age[] $ages
 * @property LinkMaterialAuthor[] $linkMaterialAuthors
 * @property Author[] $authors
 * @property LinkMaterialCategory[] $linkMaterialCategories
 * @property Category[] $categories
 * @property MaterialAudio[] $materialAudios
 * @property MaterialBackingTrack[] $materialBackingtracks
 * @property MaterialVideo[] $materialVideos
 * @property MaterialCrosslinking[] $materialCrosslinking
 */
class Material extends \yii\db\ActiveRecord
{
    public $categoryIds = [];
    public $authorIds = [];
    public $ageIds = [];
    public $preview_file;
    public $coloring_image_file;
    public $coloring_image_bw_file;
    public $post_images_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'categoryIds'], 'required'],
            [['annotation', 'body', 'description'], 'string'],
            [['preview_id', 'coloring_image_id', 'coloring_image_bw_id', 'created_at', 'updated_at'], 'integer'],
            [['slug', 'name', 'post_images_id', 'title'], 'string', 'max' => 255],
            [['slug', 'name'], 'trim'],
            [['slug'], 'unique'],
            [['coloring_image_file', 'coloring_image_bw_file', 'categoryIds', 'authorIds', 'ageIds'], 'safe'],
            [['coloring_image_file', 'coloring_image_bw_file'], 'file',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
            ],
            [['post_images_file'], 'file',
                'maxFiles' => 30,
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'slug'                   => 'ЧПУ',
            'name'                   => 'Название',
            'annotation'             => 'Аннотация',
            'preview_id'             => 'Preview ID',
            'body'                   => 'Текст',
            'coloring_image_id'      => 'Coloring Image ID',
            'coloring_image_bw_id'   => 'coloring_image_bw_id',
            'baseCategory'           => 'Главная категория',
            'categoryIds'            => 'Категории',
            'authorIds'              => 'Авторы',
            'ageIds'                 => 'Возрасты',
            'created_at'             => 'Создано',
            'updated_at'             => 'Обновлено',
            'coloring_image_file'    => 'Цветная раскраска',
            'coloring_image_bw_file' => 'Ч/б раскраска',
            'preview_file'           => 'Картинка анонса',
            'post_images_file'       => 'Добавить изображения к посту',
            'title'                  => 'Title',
            'description'            => 'Description',
        ];
    }

    public function behaviors()
    {
        return [
            // Запись верхнего родителя
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'title',
                ],
                'value' => function () {
                    if (!$this->getAttribute('title')) {
                        return $this->getAttribute('name');
                    } else {
                        return $this->getAttribute('title');
                    }
                },
            ],
            TimestampBehavior::className(),
        ];
    }

    public function beforeDelete()
    {
        // Перед удалением атрибута страницы, нужно снести связанные с ним файлы, если они есть
        foreach (['preview_id', 'coloring_image_id', 'coloring_image_bw_id'] as $fieldName) {
            if ($fileId = $this->getAttribute($fieldName)) {
                if ($file = Files::findOne(['id' => $fileId])) {
                    $file->delete();
                }
            }
        }

        // Предварительно удалить связанные аудиозаписи
        if ($audios = $this->materialAudios) {
            foreach ($audios as $audio) {
                $audio->delete();
            }
        }
        // Предварительно удалить связанные аудиозаписи
        if ($backingtrack = $this->materialBackingtracks) {
            foreach ($audios as $audio) {
                $audio->delete();
            }
        }
        // Предварительно удалить связанные видеозаписи
        if ($videos = $this->materialVideos) {
            foreach ($videos as $video) {
                $video->delete();
            }
        }
        // Предварительно удалить связанные перелинковку
        if ($crosslinkings = $this->materialCrosslinking) {
            foreach ($crosslinkings as $crosslinking) {
                $crosslinking->delete();
            }
        }

        return parent::beforeDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (count($this->categoryIds) !== 0) {
            // Сохраним в опорную таблицу связи материала с категориями
            $oldMaterials = LinkMaterialCategory::find()->where(['material_id' => $this->getAttribute('id')])->indexBy('category_id')->all();
            foreach ($this->categoryIds as $key => $categoryId) {
                if (isset($oldMaterials[$categoryId])) {
                    unset($oldMaterials[$categoryId]);
                } else {
                    $linkMaterialCategory = new LinkMaterialCategory();
                    $linkMaterialCategory->setAttribute('category_id', $categoryId);
                    $this->link('linkMaterialCategories', $linkMaterialCategory);
                }
            }
            /* @var $oldProduct LinkMaterialCategory */
            foreach ($oldMaterials as $oldMaterial) {
                $oldMaterial->delete();
            }
        }

        if ($this->authorIds !== '') {
            // Сохраним в опорную таблицу связи материала с категориями
            $oldMaterials = LinkMaterialAuthor::find()->where(['material_id' => $this->getAttribute('id')])->indexBy('author_id')->all();
            foreach ($this->authorIds as $key => $authorId) {
                if (isset($oldMaterials[$authorId])) {
                    unset($oldMaterials[$authorId]);
                } else {
                    $linkMaterialAuthor = new LinkMaterialAuthor();
                    $linkMaterialAuthor->setAttribute('author_id', $authorId);
                    $this->link('linkMaterialAuthors', $linkMaterialAuthor);
                }
            }
            /* @var $oldProduct LinkMaterialAuthor */
            foreach ($oldMaterials as $oldMaterial) {
                $oldMaterial->delete();
            }
        }

        if ($this->ageIds !== '') {
            // Сохраним в опорную таблицу связи материала с категориями
            $oldMaterials = LinkMaterialAge::find()->where(['material_id' => $this->getAttribute('id')])->indexBy('age_id')->all();
            foreach ($this->ageIds as $key => $ageId) {
                if (isset($oldMaterials[$ageId])) {
                    unset($oldMaterials[$ageId]);
                } else {
                    $linkMaterialAge = new LinkMaterialAge();
                    $linkMaterialAge->setAttribute('age_id', $ageId);
                    $this->link('linkMaterialAges', $linkMaterialAge);
                }
            }
            /* @var $oldProduct LinkMaterialAge */
            foreach ($oldMaterials as $oldMaterial) {
                $oldMaterial->delete();
            }
        }
    }

    public function beforeSave($insert)
    {
        // Сохраним единичные файлы, если они были загружены
        foreach (
            [
                 'preview_id'           => 'preview_file',
                 'coloring_image_id'    => 'coloring_image_file',
                 'coloring_image_bw_id' => 'coloring_image_bw_file',
            ] as $dbName => $fieldName
        ) {
            if ($this->{$fieldName}) {
                if ($fileId = $this->getOldAttribute($dbName)) {
                    $oldFile = Files::findOne($fileId);
                    if ($oldFile) {
                        $oldFile->delete();
                    }
                }
                $newFileId = (new Files())->addFile($this->{$fieldName}, self::className());
                $this->setAttribute($dbName, $newFileId);
            }
        }

        // Сохраним картинки для материала
        $dbName = 'post_images_id';
        $fieldName = 'post_images_file';
        if ($this->{$fieldName} && $this->{$fieldName}[0] !== '') {
            $newFilesIdArray = [];
            foreach ($this->{$fieldName} as $file) {
                $newFileId = (new Files())->addImage($file, self::className(), null, null);
                $newSmallFileId = Files::findOne($newFileId)->makeSmallCopy();
                $newFilesIdArray[$newFileId] = $newSmallFileId;
            }

            if ($this->getAttribute($dbName)) {
                $filesIdArray = SerializeHelper::decode($this->getAttribute($dbName));
            } else {
                $filesIdArray = [];
            }
            $filesIdArray = ArrayHelper::merge($filesIdArray, $newFilesIdArray);
            $this->setAttribute($dbName, SerializeHelper::encode($filesIdArray));
        }


        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkMaterialAges()
    {
        return $this->hasMany(LinkMaterialAge::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAges()
    {
        return $this->hasMany(Age::className(), ['id' => 'age_id'])->viaTable('link_material_age', ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkMaterialAuthors()
    {
        return $this->hasMany(LinkMaterialAuthor::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::className(), ['id' => 'author_id'])->viaTable('link_material_author', ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkMaterialCategories()
    {
        return $this->hasMany(LinkMaterialCategory::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('link_material_category', ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialAudios()
    {
        return $this->hasMany(MaterialAudio::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialBackingtracks()
    {
        return $this->hasMany(MaterialBackingTrack::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialVideos()
    {
        return $this->hasMany(MaterialVideo::className(), ['material_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialCrosslinking()
    {
        return $this->hasMany(MaterialCrosslinking::className(), ['material_id' => 'id']);
    }

    /**
     * Возвращает одну основную категорию товара
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id'])
            ->via('linkMaterialCategories')
            ->from(Category::tableName() . ' baseCategory')
            ->orderBy('`baseCategory`.`order`');
    }

    /**
     * Удалит файл на основе входных параметров.
     * Если нет $fileId, то удаляем единичный файл, если есть, то удаляем множественный
     *
     * @param string $fieldName Название поля где хранятся Id файла/файлов
     * @param int|null $fileId Номер файла
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteFile($fieldName, $fileId = null)
    {
        if ($fileId) {
            if ($fieldData = $this->getAttribute($fieldName)) {
                $fileIds = SerializeHelper::decode($fieldData);
                $fileIds = array_flip($fileIds);
                // Если в массиве ключами записаны оригинальные картинки
                $originalFileId = $fileIds[$fileId] ? $fileIds[$fileId] : null;
                unset($fileIds[$fileId]);
                $fileIds = array_keys($fileIds);
                if (count($fileIds)) {
                    $fieldData = SerializeHelper::encode($fileIds);
                    $this->setAttribute($fieldName, $fieldData);
                } else {
                    $this->setAttribute($fieldName, null);
                }
                $this->updateAttributes([$fieldName]);
                if (Files::findOne($fileId)->delete()) {
                    if ($originalFile = Files::findOne($originalFileId)) {
                        $originalFile->delete();
                    }

                    return true;
                }
            }
        } else {
            $fileId = $this->getAttribute($fieldName);
            $this->setAttribute($fieldName, null);
            $this->updateAttributes([$fieldName]);
            if (Files::findOne($fileId)->delete()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Вернёт все id файлов продукта в виде массива
     *
     * @param string $fieldName Название поля откуда брать id файлов
     *
     * @return array
     */
    public function getFilesId($fieldName)
    {
        return ArrayHelper::getColumn($this->getFiles($fieldName), 'id');
    }

    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * * @param string $fieldName Название поля откуда брать id файлов
     *
     * @return Files[]
     */
    public function getFiles($fieldName)
    {
        return Files::find()->where(['IN', 'id', SerializeHelper::decode($this->getAttribute($fieldName))])->all();
    }
}
