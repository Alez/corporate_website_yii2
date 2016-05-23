<?php

namespace common\modules\files\traits;

use common\modules\files\models\FileRecord;
use Yii;

trait FileTrait
{
    /**
     * Удалит файл на основе входных параметров.
     * Если нет $fileId, то удаляем единичный файл, если есть, то удаляем группу
     * @param string $fieldName Название поля где хранятся Id файла/файлов
     * @param int|null $fileId Номер файла
     * @return bool
     * @throws \Exception
     */
    public function deleteFile($fieldName, $fileId = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$fileId) {
                $fileId = $this->getAttribute($fieldName);
                $this->setAttribute($fieldName, null);
                $this->updateAttributes([$fieldName]);
            }


            if ($file = FileRecord::findOne($fileId)) {
                $file->delete();
                $transaction->commit();
                return true;
            }

            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw  $e;
        }
    }
}
