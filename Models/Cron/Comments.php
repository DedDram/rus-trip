<?php
namespace Models\Cron;

use Services\Db;
use Services\EmailSender;
use Services\PHPMailer\Exception;

class Comments
{
    protected object $db;
    private string $adminMail;

    private string $siteName;

    function __construct()
    {
        $this->adminMail = (require __DIR__ . '/../../settings.php')['main']['mail'];
        $this->siteName = (require __DIR__ . '/../../settings.php')['main']['site'];
        $this->db = Db::getInstance();

        //обновление геолокации отзывов
        $items = $this->db->query("SELECT * FROM `cl6s3_comments_items` WHERE country is null AND ip != 0 ORDER BY id DESC LIMIT 10");

        if(!empty($items[0]))
        {
            foreach($items as $item)
            {
                $geo = self::YandexLocation(long2ip($item->ip));

                if(empty($geo->country))
                {
                    $geo->country = 'unknown';
                }
                if($geo->country == 'Россия' && !empty($geo->city))
                {
                    //$geo->country = $geo->country.' / '.$geo->city;
                    $geo->country = $geo->city;
                }
                $this->db->query("UPDATE `cl6s3_comments_items` SET country = '".$geo->country."' WHERE id = '".$item->id."' LIMIT 1");
            }
        }
    }

    /**
     * Отправка писем уведомлений
     * @throws Exception
     */
    public function getResponse(): void
    {
        $db = Db::getInstance();
        $items = $db->query("SELECT t1.id AS ids, t1.type, t1.title, t1.url, t2.id, t2.images, t2.status, t2.object_group, t2.object_id, t2.ip, t2.user_id, t2.country, t2.created, t2.description, IF(t2.user_id > 0, t3.name, t2.username) AS username, IF(t2.user_id > 0, t3.email, t2.email) AS useremail, t4.email FROM `cl6s3_comments_cron` AS t1 INNER JOIN `cl6s3_comments_items` AS t2 ON t1.item_id = t2.id LEFT JOIN `cl6s3_users` AS t3 ON t2.user_id = t3.id LEFT JOIN `cl6s3_users` AS t4 ON t1.user_id = t4.id ORDER BY t1.type ASC LIMIT 5");

        $ids = array();

        if(!empty($items[0]))
        {
            foreach($items as $item)
            {
                if($item->type == 1)
                {
                    $temp = self::user($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }

                if($item->type == 2 || $item->type == 3)
                {
                    $temp = self::admin($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }
            }
        }

        if(!empty($ids))
        {
            $db->query("DELETE FROM `cl6s3_comments_cron` WHERE id IN (".implode(', ', $ids).")");
        }
    }


    /**
     * Отправка письму уведомления юзеру подписанному на отзывы
     * @throws Exception
     */
    private function user($item): bool
    {
        return EmailSender::send($item->email, 'Новый отзыв: '.$item->title, 'userNotification.php', [
            'item' => $item,
            'siteName' => $this->siteName,
        ]);
    }


    /**
     * * Отправка письму уведомления админу
     * @throws Exception
     */
    private function admin($item): bool
    {
        if($item->type == 2)
        {
            $title = 'Добавлен новый отзыв';
        }else{
            $title = 'Редактирование отзыва';
        }
        $item->country = (!empty($item->country)) ? $item->country : 'Страна не определена';

        if(!empty($item->images))
        {
            $images = $this->db->query("SELECT * FROM `cl6s3_comments_images` WHERE item_id = '".$item->id."';");

        }else{
            $images = null;
        }

        return EmailSender::send($this->adminMail, $title.': '.$item->title, 'adminNotification.php', [
            'item' => $item,
            'images' => $images,
            'siteName' => $this->siteName,
        ]);
    }

    private static function YandexLocation($ip): object
    {
        $yandex_api_key = 'ABvrmkwBAAAAMG8HdwIAOuIhmdroVhAsutIPfPXaWNwDDqMAAAAAAAAAAACYaQU04MKqqq7kiXYPr1nN2z0P8w==';

        $data = (object) array(
            'common' => (object) array(
                'version' => '1.0',
                'api_key' => $yandex_api_key
            ),
            'ip' => (object) array(
                'address_v4' => $ip
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.lbs.yandex.net/geolocation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'identity');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'json='.json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $geo = json_decode($response, true);
        if(!empty($geo['position'])){
            $content = file_get_contents('https://geocode-maps.yandex.ru/1.x/?apikey=4fc9a882-21a7-4713-9b80-6d7e4c355526&format=json&geocode='.$geo['position']['longitude'].','.$geo['position']['latitude']);
            $data = json_decode($content, true);

            if(!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName']))
            {
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'];
            }else{
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
            }
            return (object) array('country' => $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['CountryName'] ?? '', 'city' => $city ?? '');
        }else{
            return (object) array();
        }
    }

}