<?php

namespace common\modules\files\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\imagine\Image as Imagine;
use dosamigos\transliterator\TransliteratorHelper;
use Imagine\Image\Box;

/**
 * Class ImageRecord
 * @package common\modules\files\models
 * @inheritdoc
 */
class ImageRecord extends FileRecord
{
    /* Резать пропорционально */
    const PROPORTIONAL = 'inset';

    /* Резать точно */
    const EXACT = 'outbound';

    /* При загрузке файл ресайзится если он больше этого значения */
    const IMAGE_MAX_WIDTH = 3840;

    /* При загрузке файл ресайзится если он больше этого значения */
    const IMAGE_MAX_HEIGHT = 2160;

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
                'message' => 'Картинка может иметь только одно из расширений: .jpg, jpeg, .gif, .png. Например: kitty.jpg',
            ],
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
        $this->deleteThumbs();

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
     * @return ImageRecord|false
     */
    public static function addImage(
        $file,
        $modelClassName,
        $alt = null,
        $title = null,
        $width = self::IMAGE_MAX_WIDTH,
        $height = self::IMAGE_MAX_HEIGHT,
        $quality = 100,
        $mode = self::PROPORTIONAL,
        $groupId = null
    ) {
        if ($uploadedFile = self::addFile($file, $modelClassName, $alt, $title, $groupId)) {
            self::resize($uploadedFile, $width, $height, $quality, $mode);

            return $uploadedFile;
        }

        return false;
    }

    public function getThumbSrc($width, $height, $mode = self::EXACT)
    {
        if ($width <= 0 && $height <= 0) {
            return false;
        }

        if ($this->__cacheThumb($width, $height, $mode)) {
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

    public function getThumbFsPath($woName = false, $width = null, $height = null, $mode)
    {
        $path = $this->getThumbFsFolder() . $mode . '/';

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
    protected function __cacheThumb($width, $height, $mode)
    {
        $thumbFile = $this->getThumbFsPath(false, $width, $height, $mode);
        $thumbDir = $this->getThumbFsPath(true, $width, $height, $mode);

        // Если файл уже есть в кэше
        if (file_exists($thumbFile)) {
            return true;
        }

        FileHelper::createDirectory($thumbDir);

        self::resize($this, $width, $height, 100, $mode, $thumbFile);

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
        if (@unlink($this->getFsPath())) {
            $this->clearPathRecursively($this->getFsPath(true));
            return $imageId;
        }

        return false;
    }

    /**
     * Обрежет изображение по заданным параметрам, удалит уже созданные превьюхи
     *
     * @param $x int Начальная позиция
     * @param $y int Начальная позиция
     * @param $width int
     * @param $height int
     *
     * @return \Imagine\Image\ManipulatorInterface
     */
    public function cropImage($x, $y, $width, $height)
    {
        self::normalizeCropCoordinates($this->getFsPath(), $x, $y, $width, $height);
        Imagine::crop($this->getFsPath(), $width, $height, [$x, $y])->save($this->getFsPath(), ['quality' => 100]);
        $this->deleteThumbs();
    }

    /**
     * Изменение размера изображения
     * @param $fileModel self
     * @param $width
     * @param $height
     * @param $quality
     */
    public static function resize($fileModel, $width, $height, $quality = 100, $mode = self::EXACT, $outputPath = null)
    {
        if (is_null($outputPath)) {
            $outputPath = $fileModel->getFsPath();
        }

        try {
            $imagineFile = Imagine::getImagine()->open($fileModel->getFsPath());
        } catch (\Exception $e) {
            //trigger_error($e->getMessage());
        }

        if (isset($imagineFile)) {
            $imagineFile->thumbnail(new Box($width, $height), $mode)
                ->save($outputPath, ['quality' => $quality]);
        }
    }

    /**
     * @param $file FileObject Файловая строка
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
        FileObject $file,
        $modelClassName,
        $alt = null,
        $title = null,
        $width = self::IMAGE_MAX_WIDTH,
        $height = self::IMAGE_MAX_HEIGHT,
        $quality = 100,
        $mode = self::PROPORTIONAL
    ) {
        $md5Path = self::makeHashPath();

        $modelClassName = strtolower((new \ReflectionClass($modelClassName))->getShortName());

        if (self::__addToFs($file, $modelClassName, $md5Path)) {
            $this->load([
                'model' => $modelClassName,
                'name'  => TransliteratorHelper::process($file->getFullName() ?: $file->getFilename(), '', 'en'),
                'path'  => $md5Path,
                'alt'   => $alt,
                'title' => $title,
            ], '');

            if ($this->save()) {
                if ($uploadedFile = self::findOne($this->id)) {
                    self::resize($uploadedFile, $width, $height, $quality, $mode);
                }

                return $this->id;
            }
        }
        return false;
    }

    /**
     * Папка содержащая ресайзенные изображения оригинальной картинки
     * @return string
     */
    public function getThumbFsFolder()
    {
        $parts = explode('.', $this->name);
        $extension = end($parts);
        $filename = preg_replace('/ /', '_', basename($this->name, '.' . $extension));

        return Yii::getAlias('@uploads') .
            Yii::getAlias('@thumbs') . '/' .
            $this->model . '/' .
            $this->path .
            $filename . '/' .
            $extension . '/';
    }

    /**
     * Удалит все превью картинки
     */
    public function deleteThumbs()
    {
        FileHelper::removeDirectory($this->getThumbFsFolder());
        $this->clearPathRecursively($this->getThumbFsFolder() . '..');
    }

    /**
     * Нормализация кординат при кропе.
     * Если стартовая точка имеет отрицательные значения, исправит на нулевые.
     * Если ширина и высота больше оригинальной изображения, исправит на размер изображения.
     * @param $fsPath
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     */
    public static function normalizeCropCoordinates($fsPath, &$x, &$y, &$width, &$height)
    {
        if ($x < 0) {
            $width += $x;
            $x = 0;
        }
        if ($y < 0) {
            $height += $y;
            $y = 0;
        }

        $imageParams = getimagesize($fsPath);
        list($imageWidth, $imageHeight) = $imageParams;

        if ($width > $imageWidth) {
            $width = $imageWidth;
        }

        if ($height > $imageHeight) {
            $height = $imageHeight;
        }
    }
}
