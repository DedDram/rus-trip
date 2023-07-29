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

    /**
     * @throws NotFoundException
     */
    public function getCity(string $alias)
    {
        $result = $this->db->query("SELECT * FROM `cities` WHERE `alias` = ".$this->db->quote($alias));
        if(!empty($result)){
            return $result[0];
        }else{
            throw new NotFoundException();
        }
    }

    public function getNavLinks($alias): array
    {
        return (array(
            "{$alias}" => 'О городе',
            "{$alias}/karta" => 'Карта',
            "{$alias}/memorials" => 'Достопримечательности',
            "{$alias}/gostinicy" => 'Гостиницы',
            "{$alias}/restorany" => 'Рестораны',
            "{$alias}/znakomstva" => 'Знакомства',
            "{$alias}/foto" => 'Фото',
            "{$alias}/taxi" => 'Такси',
        ));
    }

    /**
     * @throws NotFoundException
     */
    public function getCityGenitive(string $cityName)
    {
        $result = $this->db->query("SELECT * FROM `cities_names` WHERE `nominative` = ".$this->db->quote($cityName));
        if(!empty($result)){
            return $result[0];
        }else{
            throw new NotFoundException();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getMemorials(int $cityId)
    {
        $result = $this->db->query("SELECT t1.*, t3.descr, t3.thumb FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id");
        if(!empty($result)){
            return $result;
        }else{
            throw new NotFoundException();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getMemorial(string $city_alias, string $memorial_alias, int $memorialId)
    {
        $result = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($memorialId));
        if(!empty($result)){
            if($memorial_alias != $result[0]->alias || $city_alias != $result[0]->cityAlias){
                header("Location: /".$result[0]->cityAlias."/memorial-".$result[0]->alias."-".$memorialId, true, 301);
            }
            return $result[0];
        }else{
            throw new NotFoundException();
        }
    }
}