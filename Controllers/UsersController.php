<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Models\Comments\Comments;
use Models\Users\User;
use Models\Users\UserActivationService;
use Models\Users\UsersAuthService;
use Services\EmailSender;

class UsersController extends AbstractUsersAuthController
{
    public function getResponse()
    {
        if(!empty($_GET['task']) && $_GET['task'] == 'unsubscribe' && !empty($_GET['object_group']) && !empty($_GET['object_id']) && !empty($this->user)){
            $comments = new Comments();
            $comments->unsubscribe((string) $_GET['object_group'], (int) $_GET['object_id'], $this->user->getId());
            $this->view->renderHtml('users/login.php', ['successful' => 'Вы отписались от уведомлений о новых комментариях!', 'title' => 'Вы отписались от уведомлений о новых комментариях!']);
        }elseif (!empty($_GET['task']) && $_GET['task'] == 'unsubscribe' && !empty($_GET['object_group']) && !empty($_GET['object_id']) && empty($this->user)){
            $this->view->renderHtml('users/login.php', ['error' => 'Чтобы отписаться от рассылки, нужно авторизоваться и снова перейти по ссылке из письма', 'title' => 'Авторизация']);
        }
    }

    /**
     * @throws \Exception
     */
    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            if (!empty($user)) {
                $code = UserActivationService::createActivationCode($user);

                EmailSender::send($user->getEmail(), 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(),
                    'code' => $code,
                ]);
                $this->view->renderHtml('users/signUpSuccessful.php', ['title' => 'Регистрация подтверждена!']);
                return;
            }
        }

        $this->view->renderHtml('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode)
    {
        $user = User::getById($userId);
        if (!empty($user)) {
            $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
            if ($isCodeValid) {
                $user->activate();
                $this->view->renderHtml('users/singUpConfirm.php');
            } else {
                $this->view->renderHtml('errors/noCritical.php', ['error' => 'код активации не найден', 'title' => 'Код активации не найден']);
            }
        } else {
            $this->view->renderHtml('errors/noCritical.php', ['error' => 'Пользователь не найден', 'title' => 'Пользователь не найден']);
        }
    }

    public function login()
    {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                if (!empty($_POST['rememberMe']) && $_POST['rememberMe'] == 'Yes') {
                    $rememberMe = true;
                } else {
                    $rememberMe = false;
                }
                UsersAuthService::createToken($user, $rememberMe);
                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/login.php', ['title' => 'Авторизация']);
    }

    public function logOut()
    {
        if (!empty($_COOKIE['tokenAuthCook'])) {
            setcookie('tokenAuthCook', '', 0, '/', '', false, true);
            header('Location: /');
        } else {
            $this->view->renderHtml('errors/noCritical.php', ['error' => 'Вы не авторизованы!', 'title' => 'Вы не авторизованы!']);
        }
    }

    public function reset()
    {
        if (!empty($_POST['token'])) {
            try {
                if (!isset($_SESSION)) {
                    session_start();
                }
                if (empty($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
                    throw new \Exceptions\ForbiddenException();
                }
                $user = User::reset($_POST);
                $code = UserActivationService::createActivationCode($user);
                EmailSender::send($user->getEmail(), 'Восстановление пароля', 'resetPassword.php', [
                    'userId' => $user->getId(),
                    'code' => $code
                ]);
                $this->view->renderHtml('users/reset.php', ['successful' => 'Ссылка на восстановление пароля успешно отправлена']);
                return;
            } catch (InvalidArgumentException|\Exception $e) {
                $this->view->renderHtml('users/reset.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/reset.php');
    }

    public function resetCheck(int $userId, string $activationCode)
    {
        $user = User::getById($userId);
        if (!empty($user)) {
            $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
            if ($isCodeValid) {
                $user->activate();
                $this->view->renderHtml('users/newPassword.php');
            } else {
                $this->view->renderHtml('errors/noCritical.php', ['error' => 'код подтверждения не найден или устарел, повторите попытку.']);
            }
        } else {
            $this->view->renderHtml('errors/noCritical.php', ['error' => 'Пользователь не найден', 'title' => 'Пользователь не найден']);
        }
    }


    public function newPassword(int $userId)
    {
        if (!empty($_POST)) {
            try {
                User::newPassword($userId, $_POST['password']);
                $this->view->renderHtml('users/login.php', ['successful' => 'пароль успешно изменен!', 'title' => 'Пароль успешно изменен!']);
                return;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('errors/noCritical.php', ['error' => $e->getMessage()]);
                return;
            }
        } else {
            $this->view->renderHtml('errors/noCritical.php', ['error' => 'Пароль не передан', 'title' => 'Пароль не передан']);
        }
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function profile()
    {
        $userObj = new User;
        if (!empty($_POST)) {
            if (!isset($_SESSION)) {
                session_start();
            }
            if (!hash_equals($_SESSION['token'], $_POST['token'])) {
                throw new ForbiddenException();
            }
            $userObj->updateUser($this->user->getId());
        }
        if (!empty($this->user)) {
            $school = [];
            if($this->user->isAgent()){
                $school = (new \Models\Schools\Schools)->getItemSchool($this->user->getSchoolId());
            }
            $comments = $userObj->getCommentsUser($this->user->getId());
            $this->view->renderHtml('users/profile.php',
                ['user' => $this->user,
                    'title' => 'Личный кабинет',
                    'maps' => $comments,
                    'school' => $school,
                ]);
        } else {
            throw new ForbiddenException();
        }
    }
}