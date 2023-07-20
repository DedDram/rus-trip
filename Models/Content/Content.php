<?php
namespace Models\Content;

use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Models\ActiveRecordEntity;
use Services\Db;

class Content
{
    protected object $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * @throws NotFoundException
     */
    public function gatPageById(int $id)
    {
        $result = $this->db->query("SELECT * FROM `pages` WHERE id = ".$this->db->quote($id));
        if(!empty($result)){
            return $result[0];
        }else{
            throw new NotFoundException();
        }
    }
}