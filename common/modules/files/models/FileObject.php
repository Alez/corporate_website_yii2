<?php

namespace common\modules\files\models;

use SplFileObject;
use dosamigos\transliterator\TransliteratorHelper;
use yii\helpers\FileHelper;

/**
 * Обёртка на класс стандартной библиотеки для ОО работы с файлами
 * Class FileObject
 * @package common\modules\files\models
 */
class FileObject extends SplFileObject
{
    const MEMORY_LIMIT = 10485760;

    /** @var string Имя файла */
    private $_fullName = '';

    public static function createFromString($string, $filename, $open_mode = 'r', $use_include_path = false, $context = null)
    {
        $store = 'php://temp/maxmemory:' . self::MEMORY_LIMIT;
        $file = new self($store, $open_mode, $use_include_path, $context);
        $file->fwrite($string);
        $file->setFullName($filename);

        return $file;
    }

    /**
     * Установить имя файла. Пример: example.txt
     * @param string $fullname Имя файла с расширением
     */
    public function setFullName(string $fullname)
    {
        $this->_fullName = $fullname;
    }

    /**
     * Получить имя файла. Пример: example.txt
     * @return string|null
     */
    public function getFullName() : string
    {
        return $this->_fullName;
    }

    public function __construct($file_name, $open_mode = 'r', $use_include_path = false, $context = null)
    {
        parent::__construct($file_name, $open_mode, $use_include_path, $context);
        $fullName = pathinfo($file_name, PATHINFO_FILENAME) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
        $this->setFullName($fullName);
    }

    /**
     * Пересохранить файл в другое место
     * @param string $pathWoName Путь до папки сохранения без имени файла
     * @return FileObject
     */
    public function saveAs(string $pathWoName)
    {
        $pathWoName = FileHelper::normalizePath($pathWoName);
        $fileName = TransliteratorHelper::process($this->getFullName(), '', 'en');
        $saveAsFile = new FileObject($pathWoName . DIRECTORY_SEPARATOR . $fileName, 'a+');
        while (!$this->eof()) {
            $saveAsFile->fwrite($this->fgets());
        }

        return $saveAsFile;
    }
}
