<?php

namespace common\modules\files\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use dosamigos\transliterator\TransliteratorHelper;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $model
 * @property string $alt
 * @property string $title
 * @property integer $group_id
 * @property string $mime
 */
class FileRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path', 'model'], 'required'],
            [['name', 'path', 'model', 'alt', 'title'], 'string', 'max' => 255],
            [['group_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'path' => 'Путь',
            'model' => 'Модель добавившая файл',
            'alt' => 'Alt',
            'title' => 'Title',
        ];
    }

    public static function instantiate($row)
    {
        switch ($row['mime']) {
            case 'image/gif':
            case 'image/jpeg':
            case 'image/pjpeg':
            case 'image/png':
            case 'image/svg+xml':
            case 'image/tiff':
            case 'image/vnd.microsoft.icon':
            case 'image/vnd.wap.wbmp':
                return new ImageRecord();
            default:
                return new self;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (isset($changedAttributes['name'])) {
            if (!is_null($changedAttributes['name'])) {
                $this->__updateFileFsName($changedAttributes['name']);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        // Перед удалением из БД нужно снести и файл
        $this->__deleteFile();

        return parent::beforeDelete();
    }

    /**
     * Добавляет файл в БД и в ФС
     *
     * @param $file UploadedFile|FileObject Загружаемый файл
     * @param $modelClassName string Название класса модели которая грузит файл
     * @param $alt string|null
     * @param $title string|null
     *
     * @return FileRecord|false
     */
    public static function addFile($file, $modelClassName, $alt = null, $title = null, $groupId = null)
    {
        $shortModelClassName = strtolower((new \ReflectionClass($modelClassName))->getShortName());
        $path = self::makeHashPath();

        if (self::__addToFs($file, $shortModelClassName, $path)) {
            if ($file instanceof FileObject && $file->getFullName()) {
                $name = TransliteratorHelper::process($file->getFullName(), '', 'en');
            } else {
                $name = TransliteratorHelper::process($file->getBaseName(), '', 'en') . '.' . $file->getExtension();
            }
            $fileRecord = new self();
            $fileRecord->load([
                'model'    => $shortModelClassName,
                'name'     => $name,
                'path'     => $path,
                'alt'      => $alt,
                'title'    => $title,
                'group_id' => $groupId,
            ], '');

            if ($fileRecord->save()) {
                return $fileRecord;
            }
        }

        return false;
    }

    /**
     * @param $file UploadedFile|FileObject Загружаемая картинка
     * @param $model string Короткое имя модели (без namespace)
     * @param $md5Path string Путь из трёх папок по первым трём символам имени файла+время через md5
     *
     * @return bool Результат сохранения на файловую систему
     */
    protected static function __addToFs($file, $model, $md5Path)
    {
        $pathWoName = Yii::getAlias('@uploads') . '/' . $model . '/' . $md5Path;
        FileHelper::createDirectory($pathWoName);
        if ($file instanceof UploadedFile) {
            $fullFsPath = $pathWoName . TransliteratorHelper::process($file->getBaseName(), '', 'en') . '.' . $file->getExtension();
            return $file->saveAs($fullFsPath);
        } else {
            return $file->saveAs($pathWoName);
        }
    }

    /**
     * Создаст MD5 путь для файла при помощи рандомной строки и микротайма.
     * Привер: 1/15/9c/
     *
     * @return string
     */
    public static function makeHashPath()
    {
        $md5FileName = hash('md5', Yii::$app->security->generateRandomString(8) . time());
        $path = substr($md5FileName, 0, 1) . '/';
        $path .= substr($md5FileName, 1, 2) . '/';
        $path .= substr($md5FileName, 3, 2) . '/';

        return $path;
    }

    /**
     * Склеивает путь до картинки для веб
     *
     * @return string
     */
    public function getSrc()
    {
        return Yii::getAlias('@webUploads') . '/' .
        $this->model . '/' .
        $this->path .
        $this->name;
    }

    /**
     * Склеивает путь до файла картинки
     *
     * @param $woName bool Возвращать путь с именем файла или без
     *
     * @return string
     */
    public function getFsPath($woName = false)
    {
        $path = Yii::getAlias('@uploads') . '/' .
            $this->model . '/' .
            $this->path;

        if ($woName) {
            return $path;
        } else {
            return $path . $this->name;
        }
    }

    /**
     * Удаляем файл из БД и из файловой системы
     *
     * @return bool|int Id удалённого изображения в случае успеха или false в противном случае
     */
    protected function __deleteFile()
    {
        $fileId = $this->id;
        if (@unlink($this->getFsPath())) {
            $this->clearPathRecursively($this->getFsPath(true));
            return $fileId;
        }

        return false;
    }

    /**
     * Обновит имя файла в файловой система в соответствии с изменениями в модели
     *
     * @return bool
     */
    protected function __updateFileFsName($oldName)
    {
        return rename($this->getFsPath(true) . $oldName, $this->getFsPath());
    }

    /**
     * @param $file UploadedFile загружаемый файл
     *
     * @return string|bool
     */
    public function saveUncategorizedAssets($file)
    {
        $moduleName = 'uncategorized';
        $md5Path = self::makeHashPath();

        $result = self::__addToFs($file, $moduleName, $md5Path);

        if ($result) {
            return Yii::$app->params['baseUrl'] . Yii::getAlias('@webUploads') . '/' . $moduleName . '/' . $md5Path .
            TransliteratorHelper::process($file->getBaseName(), '', 'en') . '.' . $file->getExtension();
        }

        return false;
    }

    /**
     * Перезапишет файл с сохранением его пути и имени
     *
     * @param $file UploadedFile загружаемый файл
     *
     * @return int|bool
     */
    public function replaceWith($file)
    {
        $file->name = $this->getAttribute('name');
        if (self::__addToFs($file, $this->getAttribute('model'), $this->getAttribute('path'))) {
            return $this->getAttribute('id');
        }

        return false;
    }

    /**
     * Создаст копию файла на жёстком диске по другому пути и новую запись в БД
     * @return bool|FileRecord
     */
    public function makeCopy()
    {
        $md5Path = self::makeHashPath();
        $pathWoName = Yii::getAlias('@uploads') . '/' . $this->getAttribute('model') . '/' . $md5Path;

        FileHelper::createDirectory($pathWoName);
        if (copy($this->getFsPath(), $pathWoName . $this->name)) {
            $newfile = new self();
            $newfile->setAttributes($this->getAttributes());
            $newfile->setAttributes([
                'id'   => null,
                'path' => $md5Path,
            ]);
            if ($newfile->save()) {
                return $newfile;
            }
        }

        return false;
    }

    /**
     * Почистит каталоги снизу вверх до каталога загрузки файлов
     * @param string $path
     */
    public function clearPathRecursively($path)
    {
        $path = FileHelper::normalizePath($path);
        $uploadPath = FileHelper::normalizePath(Yii::getAlias('@uploads'));


        if (is_dir($path) && count(scandir($path)) === 2 && $path !== $uploadPath) {
            FileHelper::removeDirectory($path);
            $path = FileHelper::normalizePath($path . DIRECTORY_SEPARATOR . '..');
            $this->clearPathRecursively($path);
        }
    }

    /**
     * Размер файла
     * @return int|false
     */
    public function getLength()
    {
        return filesize($this->getFsPath());
    }

    /**
     * MIME-тип файла
     * @return string|false
     */
    public function getFileType()
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->getFsPath());
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->mime = $this->getFileType();
        }

        return parent::beforeSave($insert);
    }
}
