<?php
$servername = "localhost";
$username = "root";
$password = "Lechis13131";
$dbname = "rustrip_new";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$csvFile = "/var/www/kazantsev_new/Services/Scripts/yamap.csv";
$file = fopen($csvFile, "r");

// Skip the first line (headers)
fgets($file);

while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
    $yandex_id = addslashes($data[0]);
    $name = addslashes($data[1]);
    $country = addslashes($data[2]);
    $region = addslashes($data[3]);
    $city = addslashes($data[4]);
    $address = addslashes($data[5]);
    $index = addslashes($data[6]);
    $phone = addslashes($data[7]);
    $mobile_phone = addslashes($data[8]);
    $fax = addslashes($data[9]);
    $email = addslashes($data[10]);
    $website = addslashes($data[11]);
    $rubric = addslashes($data[12]);
    $subrubric = addslashes($data[13]);
    $opening_hours = addslashes($data[14]);
    $whatsapp = addslashes($data[15]);
    $viber = addslashes($data[16]);
    $telegram = addslashes($data[17]);
    $facebook = addslashes($data[18]);
    $instagram = addslashes($data[19]);
    $vkontakte = addslashes($data[20]);
    $odnoklassniki = addslashes($data[21]);
    $youtube = addslashes($data[22]);
    $twitter = addslashes($data[23]);
    $foursquare = addslashes($data[24]);
    $livejournal = addslashes($data[25]);
    $latitude = addslashes($data[26]);
    $longitude = addslashes($data[27]);

    $sql = "INSERT INTO objects (id, yandex_id, Название, Страна, Регион, Город, Адрес, Индекс, Телефон, `Мобильный телефон`, Факс, Email, Сайт, Рубрика, Подрубрика, `Время работы`, whatsapp, viber, telegram, facebook, instagram, vkontakte, odnoklassniki, youtube, twitter, foursquare, livejournal, Широта, Долгота)
            VALUES (null, '$yandex_id', '$name', '$country', '$region', '$city', '$address', '$index', '$phone', '$mobile_phone', '$fax', '$email', '$website', '$rubric', '$subrubric', '$opening_hours', '$whatsapp', '$viber', '$telegram', '$facebook', '$instagram', '$vkontakte', '$odnoklassniki', '$youtube', '$twitter', '$foursquare', '$livejournal', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully. ID: $id<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

fclose($file);
$conn->close();