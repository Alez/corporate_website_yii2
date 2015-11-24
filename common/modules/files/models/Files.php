<?php

namespace common\modules\files\models;

use Yii;
use yii\web\UploadedFile;
use yii\imagine\Image as Imagine;
use dosamigos\transliterator\TransliteratorHelper;
use Imagine\Image\Box;
use Imagine\Image\Point;

/**
 * This is the model class for table "files".
 *
 * @property string $id
 * @property string $name
 * @property string $path
 * @property string $model
 * @property string $alt
 * @property string $title
 */
class Files extends \yii\db\ActiveRecord
{
    /* Резать пропорционально */
    const PROPORTIONAL = 'inset';

    /* Резать точно */
    const EXACT = 'outbound';

    /* Предустановленная ширина картинок, используемых в посте материала */
    const SMALL_WIDTH = 600;

    /* При загрузке файл ресайзится если он меньше этого значения */
    const IMAGE_MAX_WIDTH = 1920;

    /* При загрузке файл ресайзится если он меньше этого значения */
    const IMAGE_MAX_HEIGHT = 1080;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path', 'model'], 'required'],
            [['name', 'path', 'model', 'alt', 'title'], 'string', 'max' => 255],
            [
                'name',
                'match',
                'pattern' => '/^.*\.(jpg|jpeg|gif|png)$/i',
                'message' => 'Картинка может иметь только одно из расширений: .jpg, jpeg,.gif,.png. Например: kitty.jpg',
                'on' => 'image',
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['image'] = ['name', 'path', 'model', 'alt', 'title'];

        return $scenarios;
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

    public function init()
    {
        parent::init();

        // Путь для сохранения в файловой системе
        Yii::setAlias('@uploads', $_SERVER['DOCUMENT_ROOT'] . '/uploads');
        // Web путь для сохранения
        Yii::setAlias('@webUploads', '/uploads');
        Yii::setAlias('@thumbs', '/thumbs');
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
     * Добавляет изображение в БД и ФС
     *
     * @param $file UploadedFile Загружаемая картинка
     * @param $modelClassName string Название класса модели которая грузит картинку
     * @param $alt string|null
     * @param $title string|null
     * @param $width int
     * @param $height int
     * @param $quality int
     * @param $mode string
     *
     * @return int|false
     */
    public function addImage(
        $file,
        $modelClassName,
        $alt = null,
        $title = null,
        $width = self::IMAGE_MAX_WIDTH,
        $height = self::IMAGE_MAX_HEIGHT,
        $quality = 100,
        $mode = self::PROPORTIONAL
    ) {
        if ($fileId = $this->addFile($file, $modelClassName, $alt, $title)) {
            if ($uploadedFile = self::findOne($fileId)) {
                self::resize($uploadedFile->getFsPath(), $width, $height, $quality, $mode);
            }

            return $fileId;
        } else {
            return false;
        }
    }

    /**
     * Добавляет файл в БД и в ФС
     *
     * @param $file UploadedFile|string Загружаемый файл
     * @param $modelClassName string Название класса модели которая грузит файл
     * @param $alt string|null
     * @param $title string|null
     *
     * @return int
     */
    public function addFile($file, $modelClassName, $alt = null, $title = null)
    {
        $shortModelClassName = strtolower((new \ReflectionClass($modelClassName))->getShortName());
        $path = self::makeHashPath();

        if ($this->__addToFs($file, $shortModelClassName, $path)) {
            $this->load([
                    'model' => $shortModelClassName,
                    'name' => TransliteratorHelper::process($file->getBaseName(), '', 'en') . '.' . $file->getExtension(),
                    'path' => $path,
                    'alt' => $alt,
                    'title' => $title,
                ], '');

            if ($this->save()) {
                return (int)$this->id;
            }
        }

        return false;
    }

    /**
     * @param $file UploadedFile|string Загружаемая картинка
     * @param $model string Короткое имя модели (без namespace)
     * @param $md5Path string Путь из трёх папок по первым трём символам имени файла+время через md5
     * @param $filename string Имя файла с расширением
     *
     * @return bool Результат сохранения на файловую систему
     */
    private function __addToFs($file, $model, $md5Path, $filename = null)
    {
        $pathWoName = Yii::getAlias('@uploads') . '/' .
            $model . '/' .
            $md5Path;

        if (!file_exists($pathWoName)) {
            mkdir($pathWoName, 0775, true);
        }
        if ($file instanceof UploadedFile) {
            $fullFsPath = $pathWoName . TransliteratorHelper::process($file->getBaseName(), '', 'en') . '.' . $file->getExtension();
            return $file->saveAs($fullFsPath);
        } else {
            return !!file_put_contents($pathWoName . TransliteratorHelper::process($filename, '', 'en'), $file);
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
        $md5FileName = hash('md5', Yii::$app->security->generateRandomString(8) . mktime());
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

    public function getThumbSrc($width, $height, $mode = self::EXACT)
    {
        if ($width <= 0 && $height <= 0) {
            return false;
        }

        if ($this->makeThumbIfNotExist($width, $height, $mode)) {
            $pathinfo = pathinfo($this->name);
            $extension = $pathinfo['extension'];
            $filename = str_replace(' ', '_', $pathinfo['filename']);

            return Yii::getAlias('@webUploads') .
                Yii::getAlias('@thumbs') . '/' .
                $this->model . '/' .
                $this->path .
                $filename . '/' .
                $extension . '/' .
                $mode . '/' .
                $width . '/' . 
				$height . '/' .
                $this->name;
        }

        return false;
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

    public function getThumbFsPath($woName = false, $width = null, $height = null, $mode)
    {
        $parts = explode('.', $this->name);
        $extension = end($parts);
        $filename = preg_replace('/ /', '_', basename($this->name, '.' . $extension));

        $path = Yii::getAlias('@uploads') .
            Yii::getAlias('@thumbs') . '/' .
            $this->model . '/' .
            $this->path .
            $filename . '/' .
            $extension . '/' .
            $mode . '/';

        if (!$width && !$height) {
            return $path;
        } elseif ($woName && $width && $height) {
            return $path . "{$width}/{$height}/";
        } else {
            return $path . "{$width}/{$height}/" . $this->name;
        }
    }

    /**
     * Создаст превью если её ещё нет
     *
     * @return string
     */
    public function makeThumbIfNotExist($width, $height, $mode)
    {
        $thumbFile = $this->getThumbFsPath(false, $width, $height, $mode);
        $thumbDir = $this->getThumbFsPath(true, $width, $height, $mode);

        // Если файл уже есть в кэше
        if (file_exists($thumbFile)) {
            return true;
        }

        if (!file_exists($thumbDir)) {
            mkdir($thumbDir, 0775, true);
        }

        self::resize($this->getFsPath(), $width, $height, 80, $mode, $thumbFile);

        return true;
    }

    /**
     * Удаляем файл из БД и из файловой системы
     *
     * @return bool|int Id удалённого изображения в случае успеха или false в противном случае
     */
    protected function __deleteFile()
    {
        $imageId = $this->id;
        if (unlink($this->getFsPath())) {
            return $imageId;
        }

        return false;
    }

    /**
     * Обрежет изображение по заданным параметрам
     *
     * @param $x int Начальная позиция
     * @param $y int Начальная позиция
     * @param $width int Ширина (кто бы мог подумать О_О)
     * @param $height int Высота
     *
     * @return \Imagine\Image\ManipulatorInterface
     */
    public function cropImage($x, $y, $width, $height)
    {
        return Imagine::crop($this->getFsPath(), $width, $height, [$x, $y])
            ->save($this->getFsPath(), ['quality' => 100]);
    }

    /**
     * Обновит имя файла в файловой система в соответствии с изменениями в модели
     *
     * @return bool
     */
    private function __updateFileFsName($oldName)
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

        $result = $this->__addToFs($file, $moduleName, $md5Path);

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
        if ($this->__addToFs($file, $this->getAttribute('model'), $this->getAttribute('path'))) {
            return $this->getAttribute('id');
        } else {
            return false;
        }
    }

    /**
     * Изменение размера изображения
     *
     * @param $width
     * @param $height
     * @param $quality
     */
    public static function resize($filePath, $width, $height, $quality = 100, $mode = self::EXACT, $outputPath = null)
    {
        if (is_null($outputPath)) {
            $outputPath = $filePath;
        }
        // Без белых полос
        (new Imagine())
            ->getImagine()
            ->open($filePath)
            ->thumbnail(new Box($width, $height), $mode)
            ->save($outputPath, ['quality' => $quality]);

        // С полосами
        //Imagine::thumbnail($this->getFsPath(), $width, $height, $mode)
        //   ->save($thumbFile, ['quality' => 80]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return bool|mixed
     */
    public function makeCopy($width = self::IMAGE_MAX_WIDTH, $height = self::IMAGE_MAX_HEIGHT)
    {
        $md5Path = self::makeHashPath();
        $pathWoName = Yii::getAlias('@uploads') . '/' .
            $this->getAttribute('model') . '/' .
            $md5Path;

        if (!file_exists($pathWoName)) {
            mkdir($pathWoName, 0775, true);
        }
        if (copy($this->getFsPath(), $pathWoName . $this->name)) {
            $newfile = new Files;
            $newfile->setAttributes($this->getAttributes());
            $newfile->setAttributes([
                    'id'   => null,
                    'path' => $md5Path,
                ]);
            if ($newfile->save()) {
                self::resize($newfile->getFsPath(), $width, $height, 100, self::PROPORTIONAL);

                return $newfile->getAttribute('id');
            }
        }

        return false;
    }

    /**
     * Создаст маленькую копию большой картинки
     *
     * @return bool|mixed
     */
    public function makeSmallCopy()
    {
        return $this->makeCopy(self::SMALL_WIDTH);
    }

    /**
     * Сохранит файловую строку как картинку
     *
     * @param $filename string имя файла с расширением
     * @param $file string Файловая строка
     * @param $modelClassName
     * @param null $alt
     * @param null $title
     * @param int $width
     * @param int $height
     * @param int $quality
     * @param string $mode
     * @return bool|int
     */
    public function addRawImageFile(
        $filename,
        $file,
        $modelClassName,
        $alt = null,
        $title = null,
        $width = self::IMAGE_MAX_WIDTH,
        $height = self::IMAGE_MAX_HEIGHT,
        $quality = 100,
        $mode = self::PROPORTIONAL
    ) {
        $md5Path = self::makeHashPath();

        if ($this->__addToFs($file, $modelClassName, $md5Path, $filename)) {
            $this->load([
                'model' => $modelClassName,
                'name' => TransliteratorHelper::process($filename, '', 'en'),
                'path' => $md5Path,
                'alt' => $alt,
                'title' => $title,
            ], '');

            if ($this->save()) {
                $fileId = (int)$this->id;
                if ($uploadedFile = self::findOne($fileId)) {
                    self::resize($uploadedFile->getFsPath(), $width, $height, $quality, $mode);
                }

                return $fileId;
            }
        }
        return false;
    }
}
