<?php

namespace Controllers;

use Models\Users\User;
use View\View;

abstract class AbstractUsersAuthController
{
    /** @var View */
    protected View $view;

    /** @var User|null */
    protected ?User $user;
    private static $cachedUser;

    public function __construct()
    {
        $this->user = $this->getUserByToken();
        $this->view = new View(__DIR__ . '/../templates');
        $this->view->setVar('user', $this->user);
    }

    protected function getUserByToken(): ?User
    {

        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

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
        self::$cachedUser = $user;

        return self::$cachedUser;
    }
}