<?php
namespace Models\Content;

use Controllers\AbstractUsersAuthController;
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
            "{$alias}/hotels" => 'Гостиницы',
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
        $result = $this->db->query("SELECT t1.*, t3.descr, t3.thumb FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'memorials' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id");
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

    public function getPhoto(int $id, string $object_group)
    {
        return $this->db->query("SELECT * FROM `photos` WHERE `object_group` = ".$this->db->quote($object_group)." AND `object_id` = ".$this->db->quote($id));
    }

    /**
     * @throws NotFoundException
     */
    public function getHotels(int $cityId)
    {
        $result = $this->db->query("SELECT t1.*, t3.descr, t3.thumb FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'hotels' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id ORDER BY t1.average  DESC;");
        if(!empty($result)){
            return $result;
        }else{
            throw new NotFoundException();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getHotelsMore(int $cityId, int $offset, int $limit): string
    {
        $result = $this->db->query("SELECT t1.*, t3.descr, t3.thumb,t2.alias as cityAlias FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'hotels' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id ORDER BY t1.average  DESC LIMIT ".$offset.", ".$limit.";");
        if(!empty($result)){
            $html = '';
            foreach ($result as $hotel){
                $value = ($hotel->rating_votes > 0) ? round($hotel->average / $hotel->rating_votes, 2) : 0;
                $width = round($value / 5 * 100, 2);
                $word = \Services\stString::declension($hotel->rating_votes, array('голос', 'голоса', 'голосов'));
                $html .= '<div class="separator"></div>
            <div class="list-entry regular-list-entry hotel-entry">
                <div class="city_rating_wrapper">
                    <div class="rating_wrapper" data-rating-width="'.$width.'" itemprop="aggregateRating"
                         itemscope="'.$hotel->name.'" itemtype="https://schema.org/AggregateRating">
                        <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                        <div class="rating_value" style="width:'.$width.'%"></div></div>
                        <span itemprop="itemReviewed">'.$hotel->name.'</span>
                        (<b itemprop="ratingCount">'.$word.'</b>)
                        <meta itemprop="ratingValue" content="'.$value.'">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="worstRating" content="0">
                    </div>
                </div>
                <strong><a href="/'.$hotel->cityAlias.'/hotel-'.$hotel->alias.'-'.$hotel->id.'">'.$hotel->name.'</a></strong>
                <span>Адрес: <span itemprop="address">'.$hotel->address.'</span></span><br>';

                if (!empty($hotel->thumb)) {
                    $html .= '<div class="entry-thumbs"><div><img src="/'.$hotel->thumb.'" loading="lazy" alt="'.$hotel->descr.'"></div></div>';
                }

                $html .= '<span>Телефон: <span itemprop="telephone">'.($hotel->phone ?? 'неизвестно').'</span></span><br>
                <span class="sylka-adres">E-mail: '.($hotel->email ?? 'неизвестно').'</span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url">'.($hotel->website ?? 'неизвестно').'</span></span>
            </div>
            </div>';
            }
            return $html;
        }else{
            throw new NotFoundException();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getHotel(string $city_alias, string $hotel_alias, int $hotelId)
    {
        $result = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($hotelId));
        if(!empty($result)){
            if($hotel_alias != $result[0]->alias || $city_alias != $result[0]->cityAlias){
                header("Location: /".$result[0]->cityAlias."/hotel-".$result[0]->alias."-".$hotelId, true, 301);
            }
            return $result[0];
        }else{
            throw new NotFoundException();
        }
    }

    public static function getUrlCity(int $city_id): string
    {
        $db = Db::getInstance();
        $result = $db->query("SELECT * FROM `cities` WHERE `id` = ".$db->quote($city_id));
        if(!empty($result)){
            return '/'.$result[0]->alias;
        }else{
            return '/';
        }
    }

    public static function getUrlMemorial(int $memorial_id): string
    {
        $db = Db::getInstance();
        $result = $db->query("SELECT t2.alias as cityAlias,t1.alias FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id WHERE t1.id = ".$db->quote($memorial_id));
        if(!empty($result)){
            return '/'.$result[0]->cityAlias.'/memorial-'.$result[0]->alias.'-'.$memorial_id;
        }else{
            return '/';
        }
    }
    public static function getUrlHotel(int $memorial_id): string
    {
        $db = Db::getInstance();
        $result = $db->query("SELECT t2.alias as cityAlias,t1.alias FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id WHERE t1.id = ".$db->quote($memorial_id));
        if(!empty($result)){
            return '/'.$result[0]->cityAlias.'/hotel-'.$result[0]->alias.'-'.$memorial_id;
        }else{
            return '/';
        }
    }
}