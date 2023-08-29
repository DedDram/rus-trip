<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Models\Cron\Comments;
use Models\Content\Content;
use Models\Cron\SiteMap;
use Services\PHPMailer\Exception;

class CronController extends AbstractUsersAuthController
{

    private string $server_ip;

    public function __construct()
    {
        $this->server_ip = (require __DIR__ . '/../settings.php')['main']['server_ip'];
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function getResponse(): void
    {
        //чтобы не кешировался редирект ниже
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $site = (require __DIR__ . '/../settings.php')['main']['site'];
        $object = new Comments();
        if (!empty($_GET['task'])) {
            $task = (string)$_GET['task'];
        } else {
            $task = '';
        }
        //уведомления о новых отзывах
        if (!empty($_GET['commentsMail'])) {
            $object->getResponse();
            echo 'ok';
            exit();
        }
        if (!empty($this->user) && $this->user->isAdmin() && !empty($task) && !empty($_GET['item_id']) && !empty($_GET['object_group']) && !empty($_GET['object_id'])) {
            if ($task === 'unpublish') {
                (new \Models\Comments\Comments)->unpublishItems((int)$_GET['item_id']);
            }
            if ($task === 'publish') {
                (new \Models\Comments\Comments)->publishItems((int)$_GET['item_id']);
            }
            if ($task === 'remove') {
                (new \Models\Comments\Comments)->remove((int)$_GET['item_id']);
            }
            if ($task === 'blacklist') {
                (new \Models\Comments\Comments)->blacklist((int)$_GET['item_id']);
            }
            //Получаем ссылку на страницу, где отзыв и редирект туда
            if ($_GET['object_group'] == 'city') {
                $url = Content::getUrlCity((int)$_GET['object_id']);
            }elseif ($_GET['object_group'] == 'memorial') {
                $url = Content::getUrlMemorial((int)$_GET['object_id']);
            }elseif ($_GET['object_group'] == 'hotel') {
                $url = Content::getUrlHotel((int)$_GET['object_id']);
            }elseif ($_GET['object_group'] == 'restaurant') {
                $url = Content::getUrlRestaurant((int)$_GET['object_id']);
            }else {
                $url = '/';
            }
            header('Location: ' . $site . $url . '?task=' . $_GET['task'], true, 301);
        } else if (!empty($this->user) && !empty($_GET['task']) && $task == 'unsubscribe' && !empty($_GET['object_group']) && !empty($_GET['object_id'])) {
            (new \Models\Comments\Comments)->unsubscribe($_GET['object_group'], $_GET['object_id'], $this->user->getId());
            //Получаем ссылку на страницу, где отзыв и редирект туда
            if ($_GET['object_group'] == 'com_content') {
                $url = Content::getUrlCity((int)$_GET['object_id']);
            } else {
                $url = '/';
            }
            header('Location: ' . $site . $url . '?task=' . $_GET['task'], true, 301);
        } else {
            header('Location: /users/login', true, 301);
        }
    }

    /**
     * @throws ForbiddenException
     */
    public function siteMap(): void
    {
        if ($this->server_ip != $_SERVER['REMOTE_ADDR']) {
            throw new ForbiddenException();
        }
        new SiteMap;
    }
}