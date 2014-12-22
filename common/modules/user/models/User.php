<?php

namespace common\modules\user\models;

use common\modules\files\models\Files;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string  $password_hash
 * @property string  $password_reset_token
 * @property string  $email
 * @property string  $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string  $password write-only password
 * @property string  $repeat write-only password
 * @property string  $first_name
 * @property string  $last_name
 * @property integer $gender
 * @property string  $city
 * @property string  $country
 * @property integer $photo_id
 * @property integer $birth
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const ROLE_USER = 10;
    const ROLE_ADMIN = 1;

    public $password;
    public $repeat;
    public $generate = true;

    public $scenarios = ['manual'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => 'Email',
            'password'   => 'Пароль',
            'repeat'     => 'Повторите пароль',
            'generate'   => 'Сгенерировать пароль',
            'first_name' => 'Имя',
            'last_name'  => 'Фамилия',
            'city'       => 'Город',
            'gender'     => 'Пол',
            'country'    => 'Страна',
            'birth'      => 'Дата рождения',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'first_name', 'last_name', 'city'], 'filter', 'filter' => 'trim'],
            [['email'], 'required', 'on' => 'default,manual'],
            [['email'], 'email'],
            [['email'], 'unique', 'message' => 'Email занят.'],
            [['password', 'repeat'], 'required', 'on' => 'manual'],
            [['password', 'repeat'], 'string', 'min' => 6],
            [['repeat'], 'compare', 'compareAttribute' => 'password', 'on' => 'manual'],
            //[['generate'], 'safe'],
            [['first_name', 'last_name', 'city', 'country', 'birth'], 'string'],
            [['gender'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['role'], 'default', 'value' => self::ROLE_USER],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status'               => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signUp()
    {
        if ($this->validate()) {
            if ($this->generate && $this->email) {
                $this->setPassword(Yii::$app->security->generateRandomString(6));
            } elseif ($this->email) {
                $this->setPassword($this->password);
            }
            $this->generateAuthKey();
            if ($this->save()) {
                if ($this->email) {
                    UserProvider::addUsersEmailAccount($this->id);
                    // TODO: Отправить почту с именем и паролем
                }

                return $this;
            }
        }

        return null;
    }

    /**
     * Добавить пользователю информацию из соц сетей
     *
     * @param $providerId int Номер соцсети
     * @param $attributes array Атрибуты которые вернула соцсеть
     */
    public function addDataFromSocialAccount($providerId, $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            if ($this->hasAttribute($key)) {
                $this->setAttribute($key, $attribute);
            }
        }

        // Загрузка картинки
        if (isset($attributes['photo']) && !empty($attributes['photo'])) {
            $ch = curl_init($attributes['photo']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $raw = curl_exec($ch);
            curl_close ($ch);

            $filename = $providerId . '_' . $attributes['uid'];
            $ext = pathinfo($attributes['photo'], PATHINFO_EXTENSION) ?
                pathinfo($attributes['photo'], PATHINFO_EXTENSION) : 'jpg';
            $model = strtolower((new \ReflectionClass($this))->getShortName());

            $file = new Files();
            if ($fileId = $file->addRawImageFile($filename . '.' . $ext, $raw, $model)) {
                $this->photo_id = $fileId;
            }
        }

        return $this->save();
    }
}