<?php

namespace Controllers;

use Exceptions\NotFoundException;
use Models\Comments\Comments;
use Models\Maps\Maps;

class MapsController extends AbstractUsersAuthController
{

    public function getResponse()
    {
        $data = '';

        if(!empty($_POST['task']) && $_POST['task'] == 'getAddresses'){
            $maps = new Maps();
            $data = $maps->getAddresses();
        }
        if(!empty($_POST['task']) && $_POST['task'] == 'getAddressDataCategory'){
            $maps = new Maps();
            $data = $maps->getAddressDataCategory();
        }
        $this->view->renderHtml('json/json.php', [
            'data' => $data,
        ]);
    }

    /**
     * @throws NotFoundException
     */
    public function sections($section_id, $section_alias)
   {
       $sections = new Maps();
       $section = $sections->getSection((int) $section_id, (string) $section_alias);
       $categories = $sections->getCategories((int) $section_id);
       $address = $sections->getAddress((int) $section_id);
       $lang = $section->Languages;

       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ky'||$lang == 'tg'){
           $title = $section->LocalName.' - карта регионов и городов';
           $metaKey = $section->LocalName.' карта, городов';
           $metaDesc = $section->LocalName.' 🌏 регионы 🚶 и города 🚙 на карте ☑';
       }
       elseif ($lang == 'hi'){
           $title = $section->LocalName.' - क्षेत्रों और शहरों का नक्शा';
           $metaKey = $section->LocalName.' क्षेत्रों और शहरों का नक्शा';
           $metaDesc = $section->LocalName.' 🌏 🚶 क्षेत्रों और शहरों का नक्शा 🚙 ☑';
       }
       elseif ($lang == 'es'){
           $title = $section->LocalName.' - mapa de regiones y ciudades';
           $metaKey = $section->LocalName.' mapa, ciudades';
           $metaDesc = $section->LocalName.' 🌏 regiones 🚶 y ciudades 🚙 en el mapa ☑';
       }
       elseif ($lang == 'de'){
           $title = $section->LocalName.' - Karte von Regionen und Städten';
           $metaKey = $section->LocalName.' Karte, Städten';
           $metaDesc = $section->LocalName.' 🌏 Regionen 🚶 und Städte 🚙 auf der Karte ☑';
       }
       elseif ($lang == 'fr'){
           $title = $section->LocalName.' - carte des régions et des villes';
           $metaKey = $section->LocalName.' carte, villes';
           $metaDesc = $section->LocalName.' 🌏 Régions 🚶 et villes 🚙 sur la carte ☑';
       }
       elseif ($lang == 'pt'){
           $title = $section->LocalName.' - mapa de regiões e cidades';
           $metaKey = $section->LocalName.' mapa, cidades';
           $metaDesc = $section->LocalName.' 🌏 regiões 🚶 e cidades 🚙 no mapa ☑';
       }
       elseif ($lang == 'it'){
           $title = $section->LocalName.' - mappa delle regioni e delle città';
           $metaKey = $section->LocalName.' mappa, città';
           $metaDesc = $section->LocalName.' 🌏 regioni 🚶 e città 🚙 sulla mappa ☑';
       }
       elseif ($lang == 'ar'){
           $title = $section->LocalName.' - خريطة المناطق والمدن';
           $metaKey = $section->LocalName.' خريطة, المدن';
           $metaDesc = $section->LocalName.' 🌏المناطق city 🚶 والمدينة on على الخريطة ☑';
       }
       elseif ($lang == 'tl'){
           $title = $section->LocalName.' - mapa ng mga rehiyon at lungsod';
           $metaKey = $section->LocalName.' mapa, lungsod';
           $metaDesc = $section->LocalName.' 🌏 mapa 🚶 ng mga 🚙 rehiyon at lungsod ☑';
       }
       elseif ($lang == 'nl'){
           $title = $section->LocalName.' - kaart van regio\'s en steden';
           $metaKey = $section->LocalName.' kaart, steden';
           $metaDesc = $section->LocalName.' 🌏 🚶 en stads 🚙 regio\'s op de kaart ☑';
       }
       elseif ($lang == 'zh'){
           $title = $section->LocalName.' - 地区和城市的地图';
           $metaKey = $section->LocalName.' 卡, 城市';
           $metaDesc = $section->LocalName.' 🌏 🚶 地区和城市的地图 🚙 ☑';
       }
       elseif ($lang == 'ro'){
           $title = $section->LocalName.' - hărți ale regiunilor și orașelor';
           $metaKey = $section->LocalName.' hărți, orașelor';
           $metaDesc = $section->LocalName.' 🌏 🚶 hărți ale regiunilor și orașelor 🚙 ☑';
       }
       elseif ($lang == 'ja'){
           $title = $section->LocalName.' - 地域と都市の地図';
           $metaKey = $section->LocalName.' 地域と都市の地図';
           $metaDesc = $section->LocalName.' 🌏 🚶 地域と都市の地図 🚙 ☑';
       }
       elseif ($lang == 'tr'){
           $title = $section->LocalName.' - bölge ve şehir haritaları';
           $metaKey = $section->LocalName.' bölge ve şehir haritaları';
           $metaDesc = $section->LocalName.' 🌏 🚶 bölge ve şehir haritaları 🚙 ☑';
       }
       elseif ($lang == 'pl'){
           $title = $section->LocalName.' - mapy regionów i miast';
           $metaKey = $section->LocalName.' mapy regionów i miast';
           $metaDesc = $section->LocalName.' 🌏 🚶 mapy regionów i miast 🚙 ☑';
       }
       elseif ($lang == 'uk'){
           $title = $section->LocalName.' - карти регіонів і міст';
           $metaKey = $section->LocalName.' карти регіонів і міст';
           $metaDesc = $section->LocalName.' 🌏 🚶 карти регіонів і міст 🚙 ☑';
       }
       elseif ($lang == 'id'){
           $title = $section->LocalName.' - peta wilayah dan kota';
           $metaKey = $section->LocalName.' peta wilayah dan kota';
           $metaDesc = $section->LocalName.' 🌏 🚶 peta wilayah dan kota 🚙 ☑';
       }
       elseif ($lang == 'ur'){
           $title = $section->LocalName.' - علاقوں اور شہروں کے نقشے۔';
           $metaKey = $section->LocalName.' علاقوں اور شہروں کے نقشے۔';
           $metaDesc = $section->LocalName.' 🌏 🚶 علاقوں اور شہروں کے نقشے۔ 🚙 ☑';
       }
       elseif ($lang == 'th'){
           $title = $section->LocalName.' - แผนที่ของภูมิภาคและเมือง';
           $metaKey = $section->LocalName.' แผนที่ของภูมิภาคและเมือง';
           $metaDesc = $section->LocalName.' 🌏 🚶 แผนที่ของภูมิภาคและเมือง 🚙 ☑';
       }
       elseif ($lang == 'fa'){
           $title = $section->LocalName.' - نقشه مناطق و شهرها';
           $metaKey = $section->LocalName.' نقشه مناطق و شهرها';
           $metaDesc = $section->LocalName.' 🌏 🚶 نقشه مناطق و شهرها 🚙 ☑';
       }
       elseif ($lang == 'hu'){
           $title = $section->LocalName.' - régiók és városok térképei';
           $metaKey = $section->LocalName.' régiók és városok térképei';
           $metaDesc = $section->LocalName.' 🌏 🚶 régiók és városok térképei 🚙 ☑';
       }
       elseif ($lang == 'cs'){
           $title = $section->LocalName.' - mapy regionů a měst';
           $metaKey = $section->LocalName.' mapy regionů a měst';
           $metaDesc = $section->LocalName.' 🌏 🚶 mapy regionů a měst 🚙 ☑';
       }
       elseif ($lang == 'sw'){
           $title = $section->LocalName.' - ramani za mikoa na miji';
           $metaKey = $section->LocalName.' ramani za mikoa na miji';
           $metaDesc = $section->LocalName.' 🌏 🚶 ramani za mikoa na miji 🚙 ☑';
       }
       elseif ($lang == 'sv'){
           $title = $section->LocalName.' - kartor över regioner och städer';
           $metaKey = $section->LocalName.' kartor över regioner och städer';
           $metaDesc = $section->LocalName.' 🌏 🚶 kartor över regioner och städer 🚙 ☑';
       }
       elseif ($lang == 'el'){
           $title = $section->LocalName.' - χάρτες περιφερειών και πόλεων';
           $metaKey = $section->LocalName.' χάρτες περιφερειών και πόλεων';
           $metaDesc = $section->LocalName.' 🌏 🚶 χάρτες περιφερειών και πόλεων 🚙 ☑';
       }
       else {
           $title = $section->title.' - city maps';
           $metaKey = $section->title.' city, maps';
           $metaDesc = $section->title.' 🌏 city 🚶 list 🚙 maps ☑';
       }

       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ro'||$lang == 'ky'||$lang == 'tg'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=bbe8e134-9b68-440c-9769-df1a3dbf95a6&load=package.full&lang=ru_RU"></script>';
       }
       elseif ($lang == 'uk'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=uk_UA"></script>';
       }
       elseif ($lang == 'tr'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=tr_TR"></script>';
       }
       else {
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=en_US"></script>';
       }
       
       $style = '<link rel="stylesheet" href="/../templates/main/css/cattable.css">' . PHP_EOL;
       $style .= '<link rel="stylesheet" href="/../templates/maps/css/section.css">' . PHP_EOL;

       $script = '<script src="/templates/main/js/jquery-3.6.3.min.js"></script>' . PHP_EOL;
       $script .= '<script src="/../templates/maps/js/map.js"></script>' . PHP_EOL;
       $script .= '<script src="/../templates/maps/js/jquery.dataTables.1.10.7.min.js"></script>' . PHP_EOL;
       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ro'||$lang == 'ky'||$lang == 'tg') {
           $script .= '<script src="/../templates/maps/js/cattable.js"></script>' . PHP_EOL;
       }
       elseif ($lang == 'de') {
           $script .= '<script src="/../templates/maps/js/cattableDE.js"></script>' . PHP_EOL;
       }
       elseif ($lang == 'fr') {
           $script .= '<script src="/../templates/maps/js/cattableFR.js"></script>' . PHP_EOL;
       }
       else {
           $script .= '<script src="/../templates/maps/js/cattableEN.js"></script>' . PHP_EOL;
       }
       $this->view->setVar('scriptNoCompress', $scriptNoCompress);
       $this->view->setVar('style', $style);
       $this->view->setVar('script', $script);
       $this->view->renderHtml('maps/section.php',
           [   'title' => $title,
               'metaDesc' => $metaDesc,
               'metaKey' => $metaKey,
               'section' => $section,
               'categories' => $categories,
               'address' => $address,
               'lang' => $lang,
           ]);
   }

    /**
     * @throws NotFoundException
     */
    public function categories($section_id, $section_alias, $category_id, $category_alias)
   {
       $categories = new Maps();
       $category = $categories->getCategory((int) $section_id, (string) $section_alias, (int) $category_id, (string) $category_alias);
       $items = $categories->getItems((int) $category_id);
       $address = $categories->getAddressItems((int) $category_id);
       $lang = $category->counryISO;
       
       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ky'||$lang == 'tg'){
           $title = $category->LocalName.' - карты районов и городов';
           $metaKey = $category->LocalName.' карта, городов';
           $metaDesc = $category->LocalName.' 🌏 районы 🚶 и города 🚙 на карте ☑';
       }
       elseif ($lang == 'es'){
           $title = $category->LocalName.' - mapa de regiones y ciudades';
           $metaKey = $category->LocalName.' mapa, ciudades';
           $metaDesc = $category->LocalName.' 🌏 regiones 🚶 y ciudades 🚙 en el mapa ☑';
       }
       elseif ($lang == 'de'){
           $title = $category->LocalName.' - Karte von Regionen und Städten';
           $metaKey = $category->LocalName.' Karte, Städten';
           $metaDesc = $category->LocalName.' 🌏 Regionen 🚶 und Städte 🚙 auf der Karte ☑';
       }
       elseif ($lang == 'fr'){
           $title = $category->LocalName.' - carte des régions et des villes';
           $metaKey = $category->LocalName.' carte, villes';
           $metaDesc = $category->LocalName.' 🌏 Régions 🚶 et villes 🚙 sur la carte ☑';
       }
       elseif ($lang == 'pt'){
           $title = $category->LocalName.' - mapa de regiões e cidades';
           $metaKey = $category->LocalName.' mapa, cidades';
           $metaDesc = $category->LocalName.' 🌏 regiões 🚶 e cidades 🚙 no mapa ☑';
       }
       elseif ($lang == 'it'){
           $title = $category->LocalName.' - mappa delle regioni e delle città';
           $metaKey = $category->LocalName.' mappa, città';
           $metaDesc = $category->LocalName.' 🌏 regioni 🚶 e città 🚙 sulla mappa ☑';
       }
       elseif ($lang == 'ar'){
           $title = $category->LocalName.' - خريطة المناطق والمدن';
           $metaKey = $category->LocalName.' خريطة, المدن';
           $metaDesc = $category->LocalName.' 🌏المناطق city 🚶 والمدينة on على الخريطة ☑';
       }
       elseif ($lang == 'tl'){
           $title = $category->LocalName.' - mapa ng mga rehiyon at lungsod';
           $metaKey = $category->LocalName.' mapa, lungsod';
           $metaDesc = $category->LocalName.' 🌏 mapa 🚶 ng mga 🚙 rehiyon at lungsod ☑';
       }
       elseif ($lang == 'nl'){
           $title = $category->LocalName.' - kaart van regio\'s en steden';
           $metaKey = $category->LocalName.' kaart, steden';
           $metaDesc = $category->LocalName.' 🌏 🚶 en stads 🚙 regio\'s op de kaart ☑';
       }
       elseif ($lang == 'zh'){
           $title = $category->LocalName.' - 地区和城市的地图';
           $metaKey = $category->LocalName.' 卡, 城市';
           $metaDesc = $category->LocalName.' 🌏 🚶 地区和城市的地图 🚙 ☑';
       }
       elseif ($lang == 'ro'){
           $title = $category->LocalName.' - hărți ale regiunilor și orașelor';
           $metaKey = $category->LocalName.' hărți, orașelor';
           $metaDesc = $category->LocalName.' 🌏 🚶 hărți ale regiunilor și orașelor 🚙 ☑';
       }
       elseif ($lang == 'ja'){
           $title = $category->LocalName.' - 地域と都市の地図';
           $metaKey = $category->LocalName.' 地域と都市の地図';
           $metaDesc = $category->LocalName.' 🌏 🚶 地域と都市の地図 🚙 ☑';
       }
       elseif ($lang == 'tr'){
           $title = $category->LocalName.' - bölge ve şehir haritaları';
           $metaKey = $category->LocalName.' bölge ve şehir haritaları';
           $metaDesc = $category->LocalName.' 🌏 🚶 bölge ve şehir haritaları 🚙 ☑';
       }
       elseif ($lang == 'pl'){
           $title = $category->LocalName.' - mapy regionów i miast';
           $metaKey = $category->LocalName.' mapy regionów i miast';
           $metaDesc = $category->LocalName.' 🌏 🚶 mapy regionów i miast 🚙 ☑';
       }
       elseif ($lang == 'uk'){
           $title = $category->LocalName.' - карти регіонів і міст';
           $metaKey = $category->LocalName.' карти регіонів і міст';
           $metaDesc = $category->LocalName.' 🌏 🚶 карти регіонів і міст 🚙 ☑';
       }
       elseif ($lang == 'id'){
           $title = $category->LocalName.' - peta wilayah dan kota';
           $metaKey = $category->LocalName.' peta wilayah dan kota';
           $metaDesc = $category->LocalName.' 🌏 🚶 peta wilayah dan kota 🚙 ☑';
       }
       elseif ($lang == 'ur'){
           $title = $category->LocalName.' - علاقوں اور شہروں کے نقشے۔';
           $metaKey = $category->LocalName.' علاقوں اور شہروں کے نقشے۔';
           $metaDesc = $category->LocalName.' 🌏 🚶 علاقوں اور شہروں کے نقشے۔ 🚙 ☑';
       }
       elseif ($lang == 'th'){
           $title = $category->LocalName.' - แผนที่ของภูมิภาคและเมือง';
           $metaKey = $category->LocalName.' แผนที่ของภูมิภาคและเมือง';
           $metaDesc = $category->LocalName.' 🌏 🚶 แผนที่ของภูมิภาคและเมือง 🚙 ☑';
       }
       elseif ($lang == 'fa'){
           $title = $category->LocalName.' - نقشه مناطق و شهرها';
           $metaKey = $category->LocalName.' نقشه مناطق و شهرها';
           $metaDesc = $category->LocalName.' 🌏 🚶 نقشه مناطق و شهرها 🚙 ☑';
       }
       elseif ($lang == 'hu'){
           $title = $category->LocalName.' - régiók és városok térképei';
           $metaKey = $category->LocalName.' régiók és városok térképei';
           $metaDesc = $category->LocalName.' 🌏 🚶 régiók és városok térképei 🚙 ☑';
       }
       elseif ($lang == 'cs'){
           $title = $category->LocalName.' - mapy regionů a měst';
           $metaKey = $category->LocalName.' mapy regionů a měst';
           $metaDesc = $category->LocalName.' 🌏 🚶 mapy regionů a měst 🚙 ☑';
       }
       elseif ($lang == 'sw'){
           $title = $category->LocalName.' - ramani za mikoa na miji';
           $metaKey = $category->LocalName.' ramani za mikoa na miji';
           $metaDesc = $category->LocalName.' 🌏 🚶 ramani za mikoa na miji 🚙 ☑';
       }
       elseif ($lang == 'sv'){
           $title = $category->LocalName.' - kartor över regioner och städer';
           $metaKey = $category->LocalName.' kartor över regioner och städer';
           $metaDesc = $category->LocalName.' 🌏 🚶 kartor över regioner och städer 🚙 ☑';
       }
       elseif ($lang == 'el'){
           $title = $category->LocalName.' - χάρτες περιφερειών και πόλεων';
           $metaKey = $category->LocalName.' χάρτες περιφερειών και πόλεων';
           $metaDesc = $category->LocalName.' 🌏 🚶 χάρτες περιφερειών και πόλεων 🚙 ☑';
       }
       else {
           $title = $category->title.' - city maps';
           $metaKey = $category->title.' city, maps';
           $metaDesc = $category->title.' 🌏 city 🚶 list 🚙 maps ☑';
       }

       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ro'||$lang == 'ky'||$lang == 'tg'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=bbe8e134-9b68-440c-9769-df1a3dbf95a6&load=package.full&lang=ru_RU"></script>';
       }
       elseif ($lang == 'uk'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=uk_UA"></script>';
       }
       elseif ($lang == 'tr'){
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=tr_TR"></script>';
       }
       else {
           $scriptNoCompress = '<script src="https://api-maps.yandex.com/2.1/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&load=package.full&lang=en_US"></script>';
       }

       $style = '<link rel="stylesheet" href="/../templates/main/css/cattable.css">' . PHP_EOL;
       $style .= '<link rel="stylesheet" href="/../templates/maps/css/section.css">' . PHP_EOL;

       $script = '<script src="/templates/main/js/jquery-3.6.3.min.js"></script>' . PHP_EOL;
       $script .= '<script src="/../templates/maps/js/mapCategory.js"></script>' . PHP_EOL;
       $script .= '<script src="/../templates/maps/js/jquery.dataTables.1.10.7.min.js"></script>' . PHP_EOL;
       if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ro'||$lang == 'ky'||$lang == 'tg') {
           $script .= '<script src="/../templates/maps/js/cattable.js"></script>' . PHP_EOL;
       }
       elseif ($lang == 'de') {
           $script .= '<script src="/../templates/maps/js/cattableDE.js"></script>' . PHP_EOL;
       }
       elseif ($lang == 'fr') {
           $script .= '<script src="/../templates/maps/js/cattableFR.js"></script>' . PHP_EOL;
       }
       else {
           $script .= '<script src="/../templates/maps/js/cattableEN.js"></script>' . PHP_EOL;
       }
       $this->view->setVar('scriptNoCompress', $scriptNoCompress);
       $this->view->setVar('style', $style);
       $this->view->setVar('script', $script);
       $this->view->renderHtml('maps/category.php',
           [   'title' => $title,
               'metaDesc' => $metaDesc,
               'metaKey' => $metaKey,
               'category' => $category,
               'items' => $items,
               'address' => $address,
               'lang' => $lang,
           ]);
   }

    /**
     * @throws NotFoundException
     */
    public function city($section_id, $section_alias, $category_id, $category_alias, $city_id, $city_alias)
    {
        $city = new Maps();
        $item = $city->getItem((int) $section_id, (string) $section_alias, (int) $category_id, (string) $category_alias, (int) $city_id, (string) $city_alias);

        $lang = $item->preview_src;
        if (!empty($item->hideexam)){
            if ($lang == 'ru'||$lang == 'kk'||$lang == 'be'||$lang == 'uz'||$lang == 'ro'||$lang == 'ky'||$lang == 'tg'){
                $title = $item->hideexam.' - карта с улицами и номерами домов';
                $metaKey = $item->hideexam.' карта, улицы, номера, домов';
                $metaDesc = $item->hideexam.' 🌏 🚶 карта с улицами и номерами домов 🚙 ☑';
            }
            elseif ($lang == 'hi'){
                $title = $item->hideexam.' - सड़कों और घर की संख्या के साथ नक्शाा';
                $metaKey = $item->hideexam.' सड़कों और घर की संख्या के साथ नक्शा';
                $metaDesc = $item->hideexam.' 🌏 🚶 कसड़कों और घर की संख्या के साथ नक्शा 🚙 ☑';
            }
            elseif ($lang == 'es'){
                $title = $item->hideexam.' - mapa con calles y números de casas';
                $metaKey = $item->hideexam.' mapa con calles y números de casas';
                $metaDesc = $item->hideexam.' 🌏 🚶 mapa con calles y números de casas 🚙 ☑';
            }
            elseif ($lang == 'de'){
                $title = $item->hideexam.' - karte mit Straßen und Hausnummern';
                $metaKey = $item->hideexam.' Karte mit Straßen und Hausnummern';
                $metaDesc = $item->hideexam.' 🌏 🚶 Karte mit Straßen und Hausnummern 🚙 ☑';
            }
            elseif ($lang == 'fr'){
                $title = $item->hideexam.' - carte avec rues et numéros de rue';
                $metaKey = $item->hideexam.' carte avec rues et numéros de rue';
                $metaDesc = $item->hideexam.' 🌏 🚶 carte avec rues et numéros de rue 🚙 ☑';
            }
            elseif ($lang == 'pt'){
                $title = $item->hideexam.' - mapa com ruas e números de casas';
                $metaKey = $item->hideexam.' mapa com ruas e números de casas';
                $metaDesc = $item->hideexam.' 🌏 🚶 mapa com ruas e números de casas 🚙 ☑';
            }
            elseif ($lang == 'it'){
                $title = $item->hideexam.' - mappa con strade e numeri civici';
                $metaKey = $item->hideexam.' mappa con strade e numeri civici';
                $metaDesc = $item->hideexam.' 🌏 🚶 mappa con strade e numeri civici 🚙 ☑';
            }
            elseif ($lang == 'ar'){
                $title = $item->hideexam.' - الخريطة مع الشوارع وأرقام المنازل';
                $metaKey = $item->hideexam.' الخريطة مع الشوارع وأرقام المنازل';
                $metaDesc = $item->hideexam.' 🌏 🚶 الخريطة مع الشوارع وأرقام المنازل 🚙 ☑';
            }
            elseif ($lang == 'tl'){
                $title = $item->hideexam.' - mapa na may mga kalye at numero ng bahay';
                $metaKey = $item->hideexam.' mapa na may mga kalye at numero ng bahay';
                $metaDesc = $item->hideexam.' 🌏 🚶 mapa na may mga kalye at numero ng bahay 🚙 ☑';
            }
            elseif ($lang == 'nl'){
                $title = $item->hideexam.' - kaart met straten en huisnummers';
                $metaKey = $item->hideexam.' kaart met straten en huisnummers';
                $metaDesc = $item->hideexam.' 🌏 🚶 kaart met straten en huisnummers 🚙 ☑';
            }
            elseif ($lang == 'zh'){
                $title = $item->hideexam.' - 地图与街道和门牌号码';
                $metaKey = $item->hideexam.' 地图与街道和门牌号码';
                $metaDesc = $item->hideexam.' 🌏 🚶 地图与街道和门牌号码 🚙 ☑';
            }
            elseif ($lang == 'ro'){
                $title = $item->hideexam.' - hartă cu străzile și numerele casei';
                $metaKey = $item->hideexam.' hartă cu străzile și numerele casei';
                $metaDesc = $item->hideexam.' 🌏 🚶 hartă cu străzile și numerele casei 🚙 ☑';
            }
            elseif ($lang == 'ja'){
                $title = $item->hideexam.' - 通りと家の番号でマップします。';
                $metaKey = $item->hideexam.' 通りと家の番号でマップします。';
                $metaDesc = $item->hideexam.' 🌏 🚶 通りと家の番号でマップします。 🚙 ☑';
            }
            elseif ($lang == 'tr'){
                $title = $item->hideexam.' - sokak ve ev numaraları ile harita';
                $metaKey = $item->hideexam.'Sokak ve ev numaraları ile harita';
                $metaDesc = $item->hideexam.' 🌏 🚶 Sokak ve ev numaraları ile harita 🚙 ☑';
            }
            elseif ($lang == 'pl'){
                $title = $item->hideexam.' - mapa z ulicami i numerami domów';
                $metaKey = $item->hideexam.' mapa z ulicami i numerami domów';
                $metaDesc = $item->hideexam.' 🌏 🚶 mapa z ulicami i numerami domów 🚙 ☑';
            }
            elseif ($lang == 'uk'){
                $title = $item->hideexam.' - карта з вулицями і номерами будинків';
                $metaKey = $item->hideexam.' карта з вулицями і номерами будинків';
                $metaDesc = $item->hideexam.' 🌏 🚶 карта з вулицями і номерами будинків 🚙 ☑';
            }
            elseif ($lang == 'id'){
                $title = $item->hideexam.' - peta dengan jalan-jalan dan nomor rumah';
                $metaKey = $item->hideexam.' peta dengan jalan-jalan dan nomor rumah';
                $metaDesc = $item->hideexam.' 🌏 🚶 peta dengan jalan-jalan dan nomor rumah 🚙 ☑';
            }
            elseif ($lang == 'ur'){
                $title = $item->hideexam.' - گلیوں اور مکان کی تعداد کے ساتھ نقشہ';
                $metaKey = $item->hideexam.'گلیوں اور مکان کی تعداد کے ساتھ نقشہ';
                $metaDesc = $item->hideexam.' 🌏 🚶 گلیوں اور مکان کی تعداد کے ساتھ نقشہ 🚙 ☑';
            }
            elseif ($lang == 'th'){
                $title = $item->hideexam.' - แผนที่ที่มีถนนและหมายเลขบ้าน';
                $metaKey = $item->hideexam.'แผนที่ที่มีถนนและหมายเลขบ้าน';
                $metaDesc = $item->hideexam.' 🌏 🚶 แผนที่ที่มีถนนและหมายเลขบ้าน 🚙 ☑';
            }
            elseif ($lang == 'fa'){
                $title = $item->hideexam.' -نقشه با خیابان و شماره خانه';
                $metaKey = $item->hideexam.' نقشه با خیابان و شماره خانه';
                $metaDesc = $item->hideexam.' 🌏 🚶 نقشه با خیابان و شماره خانه 🚙 ☑';
            }
            elseif ($lang == 'hu'){
                $title = $item->hideexam.' - térkép utcákkal és házszámokkal';
                $metaKey = $item->hideexam.'térkép utcákkal és házszámokkal';
                $metaDesc = $item->hideexam.' 🌏 🚶 térkép utcákkal és házszámokkal🚙 ☑';
            }
            elseif ($lang == 'cs'){
                $title = $item->hideexam.' - mapa s ulicemi a čísly domů';
                $metaKey = $item->hideexam.' mapa s ulicemi a čísly domů';
                $metaDesc = $item->hideexam.' 🌏 🚶 mapa s ulicemi a čísly domů 🚙 ☑';
            }
            elseif ($lang == 'sw'){
                $title = $item->hideexam.' - ramani na mitaa na nambari za nyumba';
                $metaKey = $item->hideexam.' ramani na mitaa na nambari za nyumba';
                $metaDesc = $item->hideexam.' 🌏 🚶 ramani na mitaa na nambari za nyumba 🚙 ☑';
            }
            elseif ($lang == 'sv'){
                $title = $item->hideexam.' - karta med gator och husnummer';
                $metaKey = $item->hideexam.' karta med gator och husnummer';
                $metaDesc = $item->hideexam.' 🌏 🚶 karta med gator och husnummer 🚙 ☑';
            }
            elseif ($lang == 'el'){
                $title = $item->hideexam.' - χάρτη με δρόμους και αριθμούς κατοικιών';
                $metaKey = $item->hideexam.'χάρτη με δρόμους και αριθμούς κατοικιών';
                $metaDesc = $item->hideexam.' 🌏 🚶 χάρτη με δρόμους και αριθμούς κατοικιών 🚙 ☑';
            }
            else {
                $title = $item->title.' - map with streets and house numbers';
                $metaKey = $item->title.'map with streets and house numbers';
                $metaDesc = $item->hideexam.' 🌏 🚶 map with streets and house numbers 🚙 ☑';
            }
        }
        else{
            $title = $item->title.' - map with streets and house numbers';
            $metaKey = $item->title.'map with streets and house numbers';
            $metaDesc = $item->hideexam.' 🌏 🚶 map with streets and house numbers 🚙 ☑';
        }


        $script = '<script src="/../templates/maps/js/OpenLayers.light.js"></script>' . PHP_EOL;
        $style = '<link rel="stylesheet" href="/../templates/maps/css/section.css">' . PHP_EOL;
        $this->view->setVar('style', $style);
        $this->view->setVar('script', $script);
        $this->view->renderHtml('maps/city.php',
            [   'title' => $title,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
                'item' => $item,
                'lang' => $lang,
            ]);
    }
}