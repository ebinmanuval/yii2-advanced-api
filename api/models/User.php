<?php
namespace api\models;


class User extends \common\models\User
{
    /**
     * {@inheritdoc}
     * @param \Lcobucci\JWT\Token $token
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user_id = $token->getClaim('uid');
        return static::findOne(['id' => $user_id, 'status' => self::STATUS_ACTIVE]);
    }

}