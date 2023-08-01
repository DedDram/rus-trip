<?php

namespace Controllers;

use Exceptions\NotFoundException;
use Models\Comments\Comments;

class PostCommentsController extends AbstractUsersAuthController
{
    /** @var string */
    protected string $object_group;
    /** @var int */
    protected int $object_id;
    /** @var string */
    protected string $ip;
    /** @var int */
    protected int $user_id;
    /** @var string */
    protected string $email;
    /** @var string */
    protected string $comment_id;

    function __construct()
    {
        parent::__construct();
        if (!empty($_POST['object_group']) && ($_POST['object_group'] === 'city' || $_POST['object_group'] === 'memorial' || $_POST['object_group'] === 'hotel')) {
            $this->object_group = $_POST['object_group'];
        }
        if (!empty($_POST['object_id'])) {
            $this->object_id = (int)$_POST['object_id'];
        }
        if (!empty($_POST['item_id'])) {
            $this->comment_id = (int)$_POST['item_id'];
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getResponse(): void
    {
        if(!empty($_POST['task'])){
            $task = (string)$_POST['task'];
        }else{
            throw new NotFoundException();
        }
        $data = [];

        if ($task == 'create') {
            $data = (new Comments)->create();
        }
        if ($task == 'vote') {
            $data = (new Comments)->vote();
        }
        if ($task == 'votes') {
            $data = (new Comments)->votes();
        }
        if ($task == 'cut') {
            $data = (new Comments)->cut();
        }
        if ($task == 'add') {
            $data = (new Comments)->addImage();
        }
        if ($task == 'removeImage') {
            $data = (new Comments)->removeImage();
        }

        if (!empty($this->user)) {
            if ($this->user->isAdmin()) {
                if ($task == 'publish') {
                    $data = (new Comments)->publishItems($this->comment_id);
                }
                if ($task == 'unpublish') {
                    $data = (new Comments)->unpublishItems($this->comment_id);
                }
                if ($task == 'remove') {
                    $data = (new Comments)->remove($this->comment_id);
                }
                if ($task == 'blacklist') {
                    $data = (new Comments)->blacklist($this->comment_id);
                }
            }
            if ($task == 'unsubscribe') {
                $data = (new Comments)->unsubscribe($this->object_group, $this->object_id, $this->user_id);
            }
            if ($task == 'edit') {
                $data = (new Comments)->edit($this->comment_id);
            }
        }

        $this->view->renderHtml('json/json.php', [
            'data' => $data,
        ]);
    }

}