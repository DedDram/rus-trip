<?php

namespace Models\Users;

use Exception;
use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Models\ActiveRecordEntity;
use Services\Db;

class User extends ActiveRecordEntity
{
    /** @var string */
    protected string $username;
    /** @var string */
    protected string $name;

    /** @var string */
    protected string $email;

    /** @var int */
    protected int $isConfirmed;

    /** @var int */
    protected int $usersGroup;

    /** @var string */
    protected string $passwordHash;

    /** @var string */
    protected string $authToken;

    /** @var string */
    protected string $createdAt;

    /** @var string */
    protected string $lastvisitDate;

    /** @var string|null */
    protected ?string $fio_agent;

    /** @var string|null */
    protected ?string $position_agent;

    /** @var string|null */
    protected ?string $phone_agent;

    /** @var int */
    protected int $school_id;

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->username;
    }

    public function lastvisitDate($date)
    {
        $this->lastvisitDate = $date;
    }

    public function getSchoolId(): string
    {
        return $this->school_id;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        if ($this->usersGroup == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isAgent(): bool
    {
        if ($this->usersGroup == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsersGroup(): string
    {
        return $this->usersGroup;
    }

    protected static function getTableName(): string
    {
        return 'cl6s3_users';
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @throws NotFoundException
     */
    static function getUser(int $user_id)
    {
        $db = Db::getInstance();
        $user = $db->query("SELECT * FROM `cl6s3_users` WHERE `id` = '" . $user_id . "'  AND `is_confirmed` = 1");
        if (!empty($user)) {
            return $user[0];
        } else {
            throw new \Exceptions\NotFoundException();
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function signUp(array $userData): User
    {
        if (!isset($_SESSION)) {
            try {
                session_start();
            }catch (Exception $e) {
                sleep(1);
                session_abort(); // Закрыть текущую сессию без сохранения изменений
                session_unset(); // Удалить данные сессии
                session_destroy(); // Уничтожить сессию
                session_start();
            }
        }
        if (empty($userData['token']) || $_SESSION['token'] !== null && !hash_equals($_SESSION['token'], $userData['token'])) {
            throw new \Exceptions\ForbiddenException();
        }

        if (empty($userData['name'])) {
            throw new InvalidArgumentException('Не передано Имя');
        }

        if (empty($userData['username'])) {
            throw new InvalidArgumentException('Не передан Логин');
        }

        if (!preg_match('/^[a-zA-Z0-9]{5,20}$/', $userData['username'])) {
            throw new InvalidArgumentException('Логин может состоять только из символов латинского алфавита и цифр, от 5 до 20 символов');
        }

        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email некорректен');
        }

        if (empty($userData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }

        if (!empty($userData['password2']) && $userData['password2'] != $userData['password']) {
            throw new InvalidArgumentException('Пароли не совпадают');
        }

        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }

        if (static::findOneByColumn('username', $userData['username']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким username уже существует');
        }

        if (static::findOneByColumn('email', $userData['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже существует');
        }

        $user = new User();
        $user->username = $userData['username'];
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        //регистрация представителя школы
        if (!empty($userData['task']) && $userData['task'] == 'agent' && !empty($userData['item_id'])) {
            $user->usersGroup = 2;
            $user->school_id = (int) $userData['item_id'];
            $user->fio_agent = (string) $userData['fio_agent'];
            $user->position_agent = (string) $userData['position_agent'];
            $user->phone_agent = (string) $userData['phone_agent'];
        } else {
            $user->usersGroup = 0;
        }
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false;

        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->save();

        return $user;
    }

    public function activate(): void
    {
        $this->isConfirmed = true;
        $this->save();
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function newPassword($userId, $password): bool
    {
        $user = User::getById($userId);
        if (!empty($user)) {
            if (mb_strlen($password) < 8) {
                throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
            }
            $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $user->save();
            return true;
        }
        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function login(array $loginData): User
    {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (empty($loginData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }

        $user = User::findOneByColumn('email', $loginData['email']);
        if ($user === null) {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }

        if (!password_verify($loginData['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный пароль');
        }

        if (empty($user->isConfirmed)) {
            throw new InvalidArgumentException('Пользователь не подтверждён');
        }

        $user->refreshAuthToken();
        $user->lastvisitDate(date('Y-m-d H:i:s'));
        $user->save();

        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function updateUser(int $userId): void
    {
        $db = Db::getInstance();
        $str = 'SET ';
        if (!empty($_POST['username'])) {
            $username = addslashes($_POST['username']);
            $str .= "`username` = '$username',";
        }
        if (!empty($_POST['name'])) {
            $name = addslashes($_POST['name']);
            $str .= " `name` = '$name',";
        }
        if (!empty($_POST['password'])) {
            if (mb_strlen($_POST['password']) < 8) {
                throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
            }
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $str .= " `password_hash` = '$passwordHash'";
        }
        $str = preg_replace('~,$~m', '', $str);
        $db->query("UPDATE `cl6s3_users` ".$str." WHERE `cl6s3_users`.`id` = '".$userId."';");

    }

    public function getCommentsUser(int $userId): ?array
    {
        $db = Db::getInstance();
        return $db->query("SELECT * FROM `cl6s3_comments_items` WHERE `user_id` = '".$userId."' AND `status` = 1 ORDER BY `created` DESC;");
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function reset(array $loginData): User
    {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        $user = User::findOneByColumn('email', $loginData['email']);
        if ($user === null) {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }
        return $user;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @throws Exception
     */
    private function refreshAuthToken()
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }

}