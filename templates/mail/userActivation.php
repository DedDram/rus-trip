<?php
if(empty($agent)){
    $agent = '';
}
?>

Добро пожаловать на портал rus-trip.ru!<br>
Для активации вашего аккаунта нажмите <a href="https://rus-trip.ru/users/<?= $userId ?>/activate<?= $agent ?>/<?= $code ?>">сюда</a>.<br><br>
p.s. если вы не регистрировались на этом сайте, просто проигнорируйте это письмо.