<?php
namespace Models\Content;

use Exceptions\NotFoundException;
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
            "{$alias}/restaurants" => 'Рестораны',
            "{$alias}/znakomstva" => 'Знакомства',
            "{$alias}/foto" => 'Фото',
        ));
    }

    public function getCityGenitive(string $cityName)
    {
        $result = $this->db->query("SELECT * FROM `cities_names` WHERE `nominative` = ".$this->db->quote($cityName));
        if(!empty($result)){
            return $result[0];
        }else{
            return array();
        }
    }

    public function getMemorials(int $cityId)
    {
        return $this->db->query("SELECT t1.*, t3.descr, t3.thumb FROM `memorials` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'memorials' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id ORDER BY t1.average  DESC;");
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
                $value = ($hotel->comments > 0) ? round($hotel->average / $hotel->comments, 2) : 0;
                $width = round($value / 5 * 100, 2);
                $word = \Services\stString::declension($hotel->comments, array('отзыв', 'отзыва', 'отзывов'));
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
    public function getRestaurantsMore(int $cityId, int $offset, int $limit): string
    {
        $result = $this->db->query("SELECT t1.*, t3.descr, t3.thumb,t2.alias as cityAlias FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'restaurants' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id ORDER BY t1.average  DESC LIMIT ".$offset.", ".$limit.";");
        if(!empty($result)){
            $html = '';
            foreach ($result as $restaurant){
                $value = ($restaurant->comments > 0) ? round($restaurant->average / $restaurant->comments, 2) : 0;
                $width = round($value / 5 * 100, 2);
                $word = \Services\stString::declension($restaurant->comments, array('отзыв', 'отзыва', 'отзывов'));
                $html .= '<div class="separator"></div>
            <div class="list-entry regular-list-entry hotel-entry">
                <div class="city_rating_wrapper">
                    <div class="rating_wrapper" data-rating-width="'.$width.'" itemprop="aggregateRating"
                         itemscope="'.$restaurant->name.'" itemtype="https://schema.org/AggregateRating">
                        <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                        <div class="rating_value" style="width:'.$width.'%"></div></div>
                        <span itemprop="itemReviewed">'.$restaurant->name.'</span>
                        (<b itemprop="ratingCount">'.$word.'</b>)
                        <meta itemprop="ratingValue" content="'.$value.'">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="worstRating" content="0">
                    </div>
                </div>
                <strong><a href="/'.$restaurant->cityAlias.'/restaurant-'.$restaurant->alias.'-'.$restaurant->id.'">'.$restaurant->name.'</a></strong>
                <span>Адрес: <span itemprop="address">'.$restaurant->address.'</span></span><br>';

                if (!empty($restaurant->thumb)) {
                    $html .= '<div class="entry-thumbs"><div><img src="/'.$restaurant->thumb.'" loading="lazy" alt="'.$restaurant->descr.'"></div></div>';
                }

                $html .= '<span>Телефон: <span itemprop="telephone">'.($restaurant->phone ?? 'неизвестно').'</span></span><br>
                <span class="sylka-adres">E-mail: '.($restaurant->email ?? 'неизвестно').'</span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url">'.($restaurant->website ?? 'неизвестно').'</span></span>
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

    public function getRestaurants(int $cityId)
    {
        return $this->db->query("SELECT t1.*, t3.descr, t3.thumb FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id LEFT JOIN `photos` as t3 on t3.object_id = t1.id and t3.object_group = 'restaurants' WHERE t2.id = ".$this->db->quote($cityId)." GROUP BY t1.id ORDER BY t1.average  DESC;");
    }

    public function getPhotos(int $object_id, string $object_group)
    {
        return $this->db->query("SELECT * FROM `photos` WHERE object_id= ".$this->db->quote($object_id)." AND object_group=".$this->db->quote($object_group));
    }

    /**
     * @throws NotFoundException
     */
    public function getRestaurant(string $city_alias, string $restaurant_alias, int $restaurant_Id)
    {
        $result = $this->db->query("SELECT t1.*, t2.alias as cityAlias,t2.name as cityName FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id = t1.city_id WHERE t1.id = ".$this->db->quote($restaurant_Id));
        if(!empty($result)){
            if($restaurant_alias != $result[0]->alias || $city_alias != $result[0]->cityAlias){
                header("Location: /".$result[0]->cityAlias."/restaurant-".$result[0]->alias."-".$restaurant_Id, true, 301);
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
    public static function getUrlHotel(int $hotel_id): string
    {
        $db = Db::getInstance();
        $result = $db->query("SELECT t2.alias as cityAlias,t1.alias FROM `hotels` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id WHERE t1.id = ".$db->quote($hotel_id));
        if(!empty($result)){
            return '/'.$result[0]->cityAlias.'/hotel-'.$result[0]->alias.'-'.$hotel_id;
        }else{
            return '/';
        }
    }

    public static function getUrlRestaurant(int $restaurant_id): string
    {
        $db = Db::getInstance();
        $result = $db->query("SELECT t2.alias as cityAlias,t1.alias FROM `restaurants` as t1 INNER JOIN `cities` as t2 on t2.id= t1.city_id WHERE t1.id = ".$db->quote($restaurant_id));
        if(!empty($result)){
            return '/'.$result[0]->cityAlias.'/restaurant-'.$result[0]->alias.'-'.$restaurant_id;
        }else{
            return '/';
        }
    }
}