<?php
ini_set("memory_limit", "9000M");
set_time_limit(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$database = 'rustrip_new'; // имя базы данных
$user = 'root'; // имя пользователя
$password = 'Lechis13131'; // пароль

$conn = new PDO("mysql:host=localhost;dbname=" . $database . ";charset=UTF8", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function transliterate($string): string
{
    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

        'А' => 'A', 'Б' => 'B', 'В' => 'V',
        'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U',
        'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
        'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    );
    $str = strtr($string, $converter);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = preg_replace('/(\S)\1{2,}/', '$1', $str);
    return trim($str, "-");
}



$sqlList = $conn->query("SELECT id,name from cities where id not in (Select city_id from memorials) ");
$result = $sqlList->fetchAll(PDO::FETCH_ASSOC);
if(!empty($result)){
    foreach ($result as $item){
        $city = trim($item['name']);
        var_dump($city);

//проверяем есть ли новые отели для этого города
        $sql2 = $conn->query("SELECT * FROM `objects` WHERE `Город` = '{$city}'");
        $result2 = $sql2->fetchAll(PDO::FETCH_ASSOC);
//если есть отели для этого города удаляем старые отели и записываем новые
        if(!empty($result2)){
            //$sql = "DELETE FROM `restaurants` WHERE `city_id` = '{$item['id']}'";
           // $conn->query($sql);

            foreach ($result2 as $value){
                if(mb_strlen($value['Название'], "UTF-8") > 130){
                    continue;
                }
                $alias = transliterate($value['Название']);
                $value['Название'] = addslashes($value['Название']);
                $city = $value['Город'];
                $street = trim(str_replace($value['Город'], '', $value['Адрес']));

                if(preg_match('~\,$~', $street)){
                    $street = preg_replace('~,$~', '', $street);
                }
                $address = $city.', '.$street;

                if(!empty($value['Телефон']) && !empty($value['Мобильный телефон'])){
                    $phone = $value['Телефон'].', '.$value['Мобильный телефон'];
                }elseif (!empty($value['Телефон']) && empty($value['Мобильный телефон'])){
                    $phone = $value['Телефон'];
                }elseif (empty($value['Телефон']) && !empty($value['Мобильный телефон'])){
                    $phone = $value['Мобильный телефон'];
                }else{
                    $phone = 'нет данных';
                }

                if(!empty($value['Email'])){
                    $email = $value['Email'];
                    if(preg_match('~(.*)\,~', $value['Email'], $match0)){
                        $email = $match0[1];
                    }
                    if(mb_strlen($email, "UTF-8") > 150){
                        $email = 'нет данных';
                    }
                }else{
                    $email = 'нет данных';
                }
                if(!empty($value['Сайт'])){
                    $website = $value['Сайт'];
                    if(preg_match('~(.*)\,~', $website, $match)){
                        $website = $match[1];
                    }
                    if(mb_strlen($website, "UTF-8") > 200){
                        $url = parse_url($value['Сайт']);
                        $website = $url['host'];
                    }
                }else{
                    $website = 'нет данных';
                }
                if(!empty($value['yandex_id'])){
                    $yandex_id = $value['yandex_id'];
                }else{
                    $yandex_id = '';
                }


                $sql2 = "INSERT INTO `memorials` (`id`, `name`, `alias`, `about`, `address`, `phone`, `email`, `website`, `yandex_id`, `city_id`, `rate`, `vote`, `comments`, `geo_lat`, `geo_long`, `average`, `row_created`, `sid`) VALUES
(NULL, '{$value['Название']}', '{$alias}', '{$value['Время работы']}', '{$address}', '{$phone}', '{$email}', '{$website}', '{$yandex_id}', '{$item['id']}', 0, 0, 0, '{$value['Широта']}', '{$value['Долгота']}', 0, CURRENT_TIMESTAMP, 0);";

                $conn->query($sql2);
            }
        }


    }
}