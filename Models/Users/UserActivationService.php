<?php

namespace Models\Users;

use Services\Db;

class UserActivationService
{
    private const TABLE_NAME = 'cl6s3_users_activation_codes';

    /**
     * @throws \Exception
     */
    public static function createActivationCode(User $user): string
    {
        // Генерируем случайную последовательность символов
        $code = bin2hex(random_bytes(16));

        $db = Db::getInstance();
        $db->query(
            'INSERT INTO ' . self::TABLE_NAME . ' (user_id, code) VALUES (:user_id, :code)',
            [
                'user_id' => $user->getId(),
                'code' => $code
            ]
        );

        return $code;
    }

    public static function checkActivationCode(User $user, string $code, $isAgent = false): bool
    {
        $db = Db::getInstance();
        //чистим базу от юзеров, которые не активировали себя более 1 месяца
        $db->query('DELETE FROM `cl6s3_users` WHERE is_confirmed = 0 AND created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)');
        $result = $db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE user_id = :user_id AND code = :code',
            [
                'user_id' => $user->getId(),
                'code' => $code
            ]
        );
        if(!empty($result)){
            if(!$isAgent){
                $db->query(
                    'DELETE FROM ' . self::TABLE_NAME . ' WHERE user_id = :user_id ',
                    [
                        'user_id' => $user->getId(),
                    ]
                );
            }
            return true;
        }else{
            return false;
        }
    }
}