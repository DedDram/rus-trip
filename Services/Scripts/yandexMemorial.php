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





$sqlList = $conn->query("SELECT * FROM `memorials` where yandex_id = '';");
$result = $sqlList->fetchAll(PDO::FETCH_ASSOC);
if(!empty($result)){
    foreach ($result as $item){
sleep( 3);
        $user = 'ltpm';
        $key = '03.39722490:fcc0b8cd5e17ebdd35cdef0c1c6ea499';
        $go_yandex = "https://yandex.com/search/xml?l10n=en&user=$user&key=$key&query=" . urlencode($item['name']. ' '.$item['address']) . "&sortby=rlv&filter=none&maxpassages=1&groupby=attr%3D%22%22.mode%3Dflat.groups-on-page%3D100.docs-in-group%3D1";
        $result_yandex = '';
        $result_yandex = simplexml_load_file($go_yandex);
        $json_yandex = json_encode($result_yandex);
        $array_yandex = json_decode($json_yandex, true);
        $ass = $array_yandex["response"]["results"]["grouping"]["group"];
        if (!empty($ass)) {
            foreach ($ass as $value) {
                $url = '';
                if (!empty($value["doc"]["url"])) {
                    $url = $value["doc"]["url"];
                } else {
                    $url = $value["doc"][0]["url"];
                }

                if (preg_match('~(.*)yandex(.*)/maps/org~m', $url)) {
                    var_dump($item['name'].' - '.$url);
                    preg_match('~/(\d+)/~m', $url, $match);
                    if(!empty($match[1])){
                        $sql7 = "UPDATE memorials SET yandex_id = '{$match[1]}' WHERE id = '{$item['id']}'";
                        $conn->query($sql7);
                        break;
                    }
                }

            }
        }


        }
}