<?php

namespace Models\Users;

class UsersAuthService
{
    public static function createToken(User $user, $rememberMe): void
    {
        $token = $user->getId() . ':' . $user->getAuthToken();
        if($rememberMe){
            //запоминаем юзера на 15 дней
            setcookie('tokenAuthCook', $token, time()+60*60*24*15, '/', '', false, true);
        }else{
            setcookie('tokenAuthCook', $token, 0, '/', '', false, true);
        }

    }

    public static function getUserByToken(): ?User
    {
        $token = $_COOKIE['tokenAuthCook'] ?? '';

        if (empty($token)) {
            return null;
        }

        [$userId, $authToken] = explode(':', $token, 2);

        $user = User::getById((int) $userId);

        if ($user === null) {
            return null;
        }

        if ($user->getAuthToken() !== $authToken) {
            return null;
        }

        return $user;
    }
}