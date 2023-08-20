<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Exceptions\UnauthorizedException;
use Models\Comments\Comments;
use Models\Content\Content;
use Models\Dating\Dating;
use Models\Informer\Informer;
use Services\ExternalLinks;
use Services\Pagination;

class ContentController extends AbstractUsersAuthController
{
    /**
     * @throws NotFoundException
     */
    public function getResponse()
    {
        $data = '';
        if (!empty($_POST['cityId']) && !empty($_POST['offset']) && !empty($_POST['limit']) && !empty($_POST['object']) && $_POST['object'] =='hotels') {
            $cities = new Content();
            echo $cities->getHotelsMore((int) $_POST['cityId'], (int) $_POST['offset'], 20);
        }
        if (!empty($_POST['cityId']) && !empty($_POST['offset']) && !empty($_POST['limit']) && !empty($_POST['object']) && $_POST['object'] =='restaurants') {
            $cities = new Content();
            echo $cities->getRestaurantsMore((int) $_POST['cityId'], (int) $_POST['offset'], 20);
        }
/*        $this->view->renderHtml('json/json.php', [
            'data' => $data,
        ]);*/
    }


    public function cookiePolicy()
    {
        $this->view->renderHtml('content/cookiePolicy.php', ['title' => 'rus-trip.ru Network Cookie Policy']);
    }
    public function privacyPolicy()
    {
        $this->view->renderHtml('content/privacyPolicy.php', ['title' => 'Privacy Policy']);
    }
    public function contact()
    {
        $this->view->renderHtml('content/contact.php', ['title' => 'Contact us']);
    }

    /**
     * @throws NotFoundException
     */
    public function city($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);

        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('city', $city->id, $limit, $offset, $start, $this->user);

        $style = '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script = '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/comments/js/comments.js"></script>' . PHP_EOL;
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/comments/js/moderation.js"></script>' . PHP_EOL;
        }
        $this->view->setVar('style', $style);
        $this->view->setVar('script', $script);

        $this->view->renderHtml('content/city.php',
            [
                'title' => $city->name.' - путеводитель, отзывы',
                'city' => $city,
                'navLinks' => $navLinks,
                'object_id' => $city->id,
                'comments' => $comments,
                'object_group' => 'city',
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function map($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);
        $this->view->renderHtml('content/map.php',
            [
                'title' => 'Карта '.$cityGenitive->genitive.' с улицами и номерами домов',
                'map' => $city->map,
                'city' => $city,
                'navLinks' => $navLinks,
                'city_alias' => $city_alias,
                'cityGenitive' => $cityGenitive,
                'metaKey' => 'карта, '.$cityGenitive->genitive.', с, улицами, и, номерами, домов',
                'metaDesc' => 'Карта '.$cityGenitive->genitive.' с улицами и номерами домов',
            ]);
    }


    /**
     * @throws NotFoundException
     */
    public function memorials($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);
        $memorials = $cities->getMemorials((int) $city->id);
        $addresses = array();
        if(!empty($memorials)){
            foreach ($memorials as $memorial){
                $addresses[] = array(
                    'geo_lat' => $memorial->geo_lat,
                    'geo_long' => $memorial->geo_long,
                    'url' => '/'.$city_alias .'/memorial-'. $memorial->alias.'-'. $memorial->id,
                    'text' => $memorial->name,
                    'icon' => 'islands#lightBlueStretchyIcon'
                );
            }
        }else{
            preg_match('~\[(.*)\, (.*)\], controls~msU', $city->map, $geo);
            $addresses[] = array(
                'geo_lat' => $geo[1],
                'geo_long' => $geo[2],
                'url' => '/',
                'text' => '',
                'icon' => 'islands#lightBlueStretchyIcon'
            );
        }
        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/map.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->renderHtml('content/memorials.php',
            [
                'title' => 'Достопримечательности '.$cityGenitive->genitive,
                'metaKey' => 'достопримечательности, '.$cityGenitive->genitive,
                'metaDesc' => 'Достопримечательности '.$cityGenitive->genitive,
                'city' => $city,
                'navLinks' => $navLinks,
                'city_alias' => $city_alias,
                'memorials' => $memorials,
                'cityGenitive' => $cityGenitive,
                'addresses' => $addresses,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function memorial($city_alias, $memorial_alias, $memorial_id)
    {
        $cities = new Content();
        $memorial = $cities->getMemorial((string)$city_alias, (string)$memorial_alias, (int) $memorial_id);
        $photos = $cities->getPhoto((int) $memorial_id, 'memorials');
        $navLinks = $cities->getNavLinks((string) $city_alias);
        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('memorial', $memorial->id, $limit, $offset, $start, $this->user);
        $pagesCount = $comments['total'];
        $pagination = new Pagination($page, $limit, $pagesCount);
        $addresses = array(
            'geo_lat' => $memorial->geo_lat,
            'geo_long' => $memorial->geo_long,
            'text' => $memorial->name,
            'icon' => 'islands#lightBlueStretchyIcon'
        );
        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/mapMemorial.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/magnific.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/photos.js"></script>' . PHP_EOL;
        $style = '<link rel="stylesheet" href="/../templates/content/css/magnific.css">' . PHP_EOL;

        $style .= '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/guess_the_city.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/comments/js/comments.js"></script>' . PHP_EOL;
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/comments/js/moderation.js"></script>' . PHP_EOL;
        }
        $commentsWord = \Services\stString::declension($memorial->comments, array('отзыв', 'отзыва', 'отзывов'));
        $title = $memorial->name.' '.$memorial->cityName.' - '.$commentsWord;

        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->setVar('style', $style);
        $this->view->renderHtml('content/memorial.php',
            [
                'title' => $title,
                'metaKey' => $memorial->name.' '.$memorial->cityName.' расположение на карте, отзывы, адрес',
                'metaDesc' => $memorial->name.', '.$memorial->cityName.' расположение, карта, отзывы, адрес',
                'addresses' => $addresses,
                'memorial' => $memorial,
                'photos' => $photos,
                'object_id' => $memorial->id,
                'comments' => $comments,
                'object_group' => 'memorial',
                'pagination' => $pagination,
                'pagesCount' => $pagesCount,
                'navLinks' => $navLinks,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function hotels($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);
        $hotels = $cities->getHotels((int) $city->id);
        $addresses = array();
        foreach ($hotels as $hotel){
            $addresses[] = array(
                'geo_lat' => $hotel->geo_lat,
                'geo_long' => $hotel->geo_long,
                'url' => '/'.$city_alias .'/hotel-'. $hotel->alias.'-'. $hotel->id,
                'text' => $hotel->name,
                'icon' => 'islands#lightBlueStretchyIcon'
            );
        }
        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/map.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/hotels.js"></script>' . PHP_EOL;
        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->renderHtml('content/hotels.php',
            [
                'title' => 'Гостиницы '.$cityGenitive->genitive.' - отзывы, цены',
                'metaKey' => 'гостиницы, '.$cityGenitive->genitive.', отзывы, цены',
                'metaDesc' => 'Отзывы о гостиницах '.$cityGenitive->genitive.', расположение на карте, цены',
                'city' => $city,
                'navLinks' => $navLinks,
                'city_alias' => $city_alias,
                'hotels' => $hotels,
                'cityGenitive' => $cityGenitive,
                'addresses' => $addresses,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function hotel($city_alias, $hotel_alias, $hotel_id)
    {
        $cities = new Content();
        $hotel = $cities->getHotel((string)$city_alias, (string)$hotel_alias, (int) $hotel_id);
        $photos = $cities->getPhoto((int) $hotel_id, 'hotels');
        $navLinks = $cities->getNavLinks((string) $city_alias);
        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('hotel', $hotel->id, $limit, $offset, $start, $this->user);
        $pagesCount = $comments['total'];
        $pagination = new Pagination($page, $limit, $pagesCount);
        $addresses = array(
            'geo_lat' => $hotel->geo_lat,
            'geo_long' => $hotel->geo_long,
            'text' => $hotel->name,
            'icon' => 'islands#lightBlueStretchyIcon'
        );
        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/mapMemorial.js"></script>' . PHP_EOL;
        $style = '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/comments/js/comments.js"></script>' . PHP_EOL;
        if(!empty($photos)){
            $script .= '<script src="/../templates/content/js/photos.js"></script>' . PHP_EOL;
            $style .= '<link rel="stylesheet" href="/../templates/content/css/magnific.css">' . PHP_EOL;
            $script .= '<script src="/../templates/content/js/magnific.js"></script>' . PHP_EOL;
        }
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/comments/js/moderation.js"></script>' . PHP_EOL;
        }
        $commentsWord = \Services\stString::declension($hotel->comments, array('отзыв', 'отзыва', 'отзывов'));
        if(!preg_match('~Гостини|Отель|Hotel|Гостев|посуточно|Апартаменты~m', $hotel->name)){
            $title = 'Гостиница '.$hotel->name.' '.$hotel->cityName.' - '.$commentsWord;
        }else{
            $title = $hotel->name.' '.$hotel->cityName.' - '.$commentsWord;
        }
        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->setVar('style', $style);
        $this->view->renderHtml('content/hotel.php',
            [
                'title' => $title,
                'metaKey' => 'Гостиница '.$hotel->name.' отзывы, адрес, расположение на карте, телефон',
                'metaDesc' => 'Гостиница '.$hotel->name.' отзывы, адрес, телефон',
                'addresses' => $addresses,
                'hotel' => $hotel,
                'photos' => $photos,
                'object_id' => $hotel->id,
                'comments' => $comments,
                'object_group' => 'hotel',
                'pagination' => $pagination,
                'pagesCount' => $pagesCount,
                'navLinks' => $navLinks,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function restaurants($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);
        $restaurants = $cities->getRestaurants((int) $city->id);
        $addresses = array();
        if(!empty($restaurants)){
            foreach ($restaurants as $restaurant){
                $addresses[] = array(
                    'geo_lat' => $restaurant->geo_lat,
                    'geo_long' => $restaurant->geo_long,
                    'url' => '/'.$city_alias .'/restaurant-'. $restaurant->alias.'-'. $restaurant->id,
                    'text' => $restaurant->name,
                    'icon' => 'islands#lightBlueStretchyIcon'
                );
            }
        }else{
            preg_match('~\[(.*)\, (.*)\], controls~msU', $city->map, $geo);
            $addresses[] = array(
                'geo_lat' => $geo[1],
                'geo_long' => $geo[2],
                'url' => '/',
                'text' => '',
                'icon' => 'islands#lightBlueStretchyIcon'
            );
        }

        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/map.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/restaurants.js"></script>' . PHP_EOL;
        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->renderHtml('content/restaurants.php',
            [
                'title' => 'Рестораны '.$cityGenitive->genitive.' - отзывы, цены',
                'metaKey' => 'Рестораны, '.$cityGenitive->genitive.', отзывы, цены',
                'metaDesc' => 'Отзывы о ресторанах и кафе '.$cityGenitive->genitive.', расположение на карте, цены',
                'city' => $city,
                'navLinks' => $navLinks,
                'city_alias' => $city_alias,
                'restaurants' => $restaurants,
                'cityGenitive' => $cityGenitive,
                'addresses' => $addresses,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function restaurant($city_alias, $restaurant_alias, $restaurant_id)
    {
        $cities = new Content();
        $restaurant = $cities->getRestaurant((string)$city_alias, (string)$restaurant_alias, (int) $restaurant_id);
        $photos = $cities->getPhoto((int) $restaurant_id, 'restaurants');
        $navLinks = $cities->getNavLinks((string) $city_alias);
        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('restaurant', $restaurant->id, $limit, $offset, $start, $this->user);
        $pagesCount = $comments['total'];
        $pagination = new Pagination($page, $limit, $pagesCount);
        $addresses = array(
            'geo_lat' => $restaurant->geo_lat,
            'geo_long' => $restaurant->geo_long,
            'text' => $restaurant->name,
            'icon' => 'islands#lightBlueStretchyIcon'
        );
        $scriptNoCompress = '<script src="https://api-maps.yandex.ru/2.1/?apikey=0fdafffc-ec9c-499a-87f9-8f19d053bb3e&lang=ru_RU"></script>' . PHP_EOL;
        $script = '<script src="/../templates/main/js/mapMemorial.js"></script>' . PHP_EOL;
        $style = '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/comments/js/comments.js"></script>' . PHP_EOL;
        if(!empty($photos)){
            $script .= '<script src="/../templates/content/js/photos.js"></script>' . PHP_EOL;
            $style .= '<link rel="stylesheet" href="/../templates/content/css/magnific.css">' . PHP_EOL;
            $script .= '<script src="/../templates/content/js/magnific.js"></script>' . PHP_EOL;
        }
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/comments/js/moderation.js"></script>' . PHP_EOL;
        }
        $commentsWord = \Services\stString::declension($restaurant->comments, array('отзыв', 'отзыва', 'отзывов'));
        if(!preg_match('~Ресторан|Кафе |Бар |Закусочная|Пельменная|KFC|Пицц|Шашлыч~m', $restaurant->name)){
            $title = 'Ресторан '.$restaurant->name.' '.$restaurant->cityName.' - '.$commentsWord;
        }else{
            $title = $restaurant->name.' '.$restaurant->cityName.' - '.$commentsWord;
        }


        $this->view->setVar('script', $script);
        $this->view->setVar('scriptNoCompress', $scriptNoCompress);
        $this->view->setVar('style', $style);
        $this->view->renderHtml('content/restaurant.php',
            [
                'title' => $title,
                'metaKey' => 'Ресторан '.$restaurant->name.' отзывы, адрес, расположение на карте, телефон',
                'metaDesc' => 'Ресторан '.$restaurant->name.' отзывы, адрес, телефон',
                'addresses' => $addresses,
                'restaurant' => $restaurant,
                'photos' => $photos,
                'object_id' => $restaurant->id,
                'comments' => $comments,
                'object_group' => 'restaurant',
                'pagination' => $pagination,
                'pagesCount' => $pagesCount,
                'navLinks' => $navLinks,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function znakomstva($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string)$city_alias);
        $datings = new Dating();
        $Fields = $datings->dating($city);

        $navLinks = $cities->getNavLinks((string) $city_alias);
        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('dating', $city->id, $limit, $offset, $start, $this->user);
        $pagesCount = $comments['total'];
        $pagination = new Pagination($page, $limit, $pagesCount);

        $style = '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script = '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/comments/js/comments.js"></script>' . PHP_EOL;
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/comments/js/moderation.js"></script>' . PHP_EOL;
        }
        $this->view->setVar('script', $script);
        $this->view->setVar('style', $style);
        $this->view->renderHtml('content/znakomstva.php',
            [
                'title' => 'Знакомства в '.$city->name_morphy.' без регистрации бесплатно',
                'metaKey' => 'Знакомства, в, '.$city->name_morphy.', без, регистрации, бесплатно',
                'metaDesc' => 'Знакомства в '.$city->name_morphy.' без регистрации бесплатно',
                'Fields' => $Fields,
                'object_id' => $city->id,
                'comments' => $comments,
                'object_group' => 'dating',
                'pagination' => $pagination,
                'pagesCount' => $pagesCount,
                'navLinks' => $navLinks,
                'city' => $city,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function foto($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string)$city_alias);
        $photos = $cities->getPhotos((int) $city->id, 'cities');
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);

        $script = '<script src="/../templates/content/js/magnific.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/photos.js"></script>' . PHP_EOL;
        $style = '<link rel="stylesheet" href="/../templates/content/css/magnific.css">' . PHP_EOL;

        $style .= '<link rel="stylesheet" href="/../templates/comments/css/style.css">' . PHP_EOL;
        $script .= '<script src="/../templates/content/js/jquery.form.js"></script>' . PHP_EOL;
        $script .= '<script src="/../templates/main/js/jquery.simplemodal.js"></script>' . PHP_EOL;

        $this->view->setVar('script', $script);
        $this->view->setVar('style', $style);
        $this->view->renderHtml('content/foto.php',
            [
                'title' => 'Фото '.$cityGenitive->genitive,
                'metaKey' => 'Фото, фотографии '.$cityGenitive->genitive,
                'metaDesc' => 'Красивые фотографии города '.$cityGenitive->genitive,
                'photos' => $photos,
                'city' => $city,
                'navLinks' => $navLinks,
                'city_alias' => $city_alias,
                'cityGenitive' =>  $cityGenitive
            ]);
    }

    public function guessCity()
    {
        $cities = new Content();
        $citiesRandom = $cities->getRandomCity();

        $this->view->renderHtml('json/json.php', [
            'data' => $citiesRandom,
        ]);
    }
}