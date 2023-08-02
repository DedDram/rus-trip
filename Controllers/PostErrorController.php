<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Models\PostError\PostError;
use Services\PHPMailer\Exception;

class PostErrorController extends AbstractUsersAuthController
{
    /**
     * @throws ForbiddenException
     * @throws Exception
     */

    public function getResponse()
    {
        $data = [];
        $id = (int) $_POST['id'];
        // отправка сообщения об ошибке
        if(!empty($_POST['task']) && $_POST['task'] == 'postError'){
            $data = new PostError($id);
        }

        $this->view->renderHtml('json/json.php', [
            'data' => $data,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getError($id, $object_group)
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        $this->view->renderHtml('postError/postError.php', ['id' => $id, 'object_group' => $object_group, 'token' => $token]);
    }
}