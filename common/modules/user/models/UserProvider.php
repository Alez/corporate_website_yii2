<?php

namespace common\modules\user\models;

use Yii;
use yii\authclient\clients\Facebook;
use yii\authclient\clients\GoogleOAuth;
use yii\authclient\clients\VKontakte;
use yii\authclient\clients\Twitter;
use common\modules\user\models\clients\Odnoklassniki;
use common\modules\user\models\clients\Mailru;

/**
 * This is the model class for table "user_provider".
 *
 * @property string $user_id
 * @property string $account_id
 * @property string $provider_id
 *
 * @property User $user
 */
class UserProvider extends \yii\db\ActiveRecord
{
    const EMAIL = 0;
    const FACEBOOK = 1;
    const VKONTAKTE = 2;
    const TWITTER = 3;
    const GOOGLE = 4;
    const ODNOKLASSNIKI = 5;
    const MAILRU = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_provider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'account_id', 'provider_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'account_id' => 'Account ID',
            'provider_id' => 'Provider ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Добавит пользователю соц сеть.
     * Если нет пользователя создаст нового и привяжет соц сеть.
     *
     * @param $client \yii\authclient\BaseClient
     * @param $userId int
     * @return bool|self
     */
    public static function addUsersSocialAccount($providerId, $attributes, $userId)
    {
        $account = new self();
        $account->user_id = $userId;
        $account->provider_id = $providerId;
        $account->account_id = $attributes['uid'];
        if ($account->save()) {
            return $account;
        }
        return false;
    }

    /**
     * Вернёт аккаунт провайдера по данным из колбэка соцсети
     *
     * @param $client \yii\authclient\BaseClient
     * @return $account self
     */
    public static function getAccountByCallback($providerId, $attributes)
    {
        $account = self::find()
            ->where([
                'provider_id' => $providerId,
                'account_id'  => $attributes['uid'],
            ])
            ->one();

        return $account;
    }

    /**
     * Определит номер соцсети по данным колбэка
     *
     * @param $client \yii\authclient\BaseClient
     * @return int Номер соцсети
     */
    public static function detectClientsProvider($client)
    {
        if ($client instanceof Facebook) {
            return self::FACEBOOK;
        } elseif ($client instanceof VKontakte) {
            return self::VKONTAKTE;
        } elseif ($client instanceof Twitter) {
            return self::TWITTER;
        } elseif ($client instanceof GoogleOAuth) {
            return self::GOOGLE;
        } elseif ($client instanceof Odnoklassniki) {
            return self::ODNOKLASSNIKI;
        } elseif ($client instanceof Mailru) {
            return self::MAILRU;
        } else {
            return self::EMAIL;
        }
    }

    /**
     * Добавит юзеру провайдера, который представляет из себя аккаунт email
     *
     * @param $userId
     * @return bool|UserProvider
     */
    public static function addUsersEmailAccount($userId)
    {
        $account = new self();
        $account->user_id = $userId;
        if ($account->save()) {
            return $account;
        }
        return false;
    }
}
