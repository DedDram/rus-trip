<?php

namespace Models\PostError;

use Exceptions\ForbiddenException;
use Models\Content\Content;
use Services\EmailSender;
use Services\PHPMailer\Exception;

class PostError
{
    public string $msg;
    public string $adminMail;

    /**
     * @throws ForbiddenException
     * @throws Exception
     */
    function __construct($id)
    {
        if(!isset($_SESSION))
        {
            session_start();
        }

        $this->adminMail = (require __DIR__ . '/../../settings.php')['main']['mail'];

        if (!empty($_POST['object_group'])) {
            if($_POST['object_group'] == 'city'){
                $link = Content::getUrlCity($id);
            }elseif ($_POST['object_group'] == 'memorials'){
                $link = Content::getUrlCity($id).'/memorials';
            }elseif ($_POST['object_group'] == 'memorial'){
                $link = Content::getUrlMemorial($id);
            }
        }
        if (!empty($_POST['name'])) {
            $name = $_POST['name'];
        }

        $description = (string)$_POST['description'];
        $email = $_POST['mailfrom'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            if (!empty($description)) {
                if (hash_equals($_SESSION['token'], $_POST['token'])) {
                    if (preg_match('#[а-яА-Я]#u', $description)) {
                        if (!empty($link)) {
                            $description .= '<i><br/><br/>------------------------------------------------<br/><a href="https://rus-trip.ru' . $link . '">https://rus-trip.ru' . $link . '</a></i>';
                        }
                        $description .= '<i><br/><br/>------------------------------------------------<br/>e-mail: ' . $email . '</i>';

                        $message = $description;
                        $onlyText = strip_tags($description);

                        if (empty($name)) {
                            $subject = 'Ошибка в материале ' . $id;
                        } else {
                            $subject = 'Ошибка в материале ' . $name;
                        }

                        $status = EmailSender::Mailer($this->adminMail, $message, $subject, $onlyText);
                        if ($status) {
                            $this->msg = 'Ваше сообщение успешно отправлено';
                        } else {
                            $this->msg = 'Ошибка отправки уведомления';
                        }
                    } else {
                        $this->msg = 'Текст сообщения должен содержать кириллицу';
                    }
                } else {
                    throw new \Exceptions\ForbiddenException();
                }
            } else {
                $this->msg = 'Введите описание найденной ошибки';
            }
        } else {
            $this->msg = 'Вы указали неверный почтовый ящик';
        }
        return $this->msg;
    }
}