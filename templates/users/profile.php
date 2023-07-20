<?php include __DIR__ . '/../header.php';
if (!isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];
?>
<style>
    input#show, span#content {
        display: none;
    }

    input#show:checked ~ span#content {
        display: block;
    }
</style>
<div id="main">
    <div id="aCenter">
        <div>
            <h1>Ваш профиль</h1>
            <?php if (!empty($error)): ?>
                <div style="background-color: #f59f9f;padding: 5px;margin: 15px"><?= $error ?></div>
            <?php endif; ?>
            <form action="/users/profile" method="post">
                Имя (видно в отзывах, можете изменить)<br>
                <input size="30" minlength="4" maxlength="25" type="text" name="name"
                       value="<?= $user->name ?? '' ?>"><br>
                Email (менять уже нельзя)<br>
                <input size="30" type="email" maxlength="50" name="email" value="<?= $user->email ?? '' ?>"
                       disabled><br>
                Пароль (если оставите пустым, пароль будет прежний)<br>
                <input size="30" minlength="8" maxlength="25" type="password" name="password" value=""><br>
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" value="Сохранить">
            </form>
            <?php if (!empty($user) && $user->isAgent()): ?>
                <hr>
                <h2>Вы представитель этой школы у нас на сайте:</h2>
            <h5><a href="/schools/<?php echo $school[0]->section_id . '-' . $school[0]->section_alias . '/' . $school[0]->category_id . '-' . $school[0]->category_alias . '/' . $school[0]->id . '-' . $school[0]->alias ?>"> <?php echo $school[0]->name; ?></a></h5>
            <?php endif; ?>
            <hr>
            <h2>Ваши отзывы</h2>
            <?php if (!empty($comments)): ?>
                <?php
                $firstComments = '';
                $nextComments = '';
                $num = 0;
                foreach ($comments as $comment) {
                    if($comment->object_group == 'com_schools'){
                        $link = "https://rus-trip.ru/schools/9-russia/129-kaliningradskaya/$comment->object_id-shkola#scomment-$comment->id";
                    }else{
                        $link = "https://rus-trip.ru/news/$comment->object_id-zarplata-uchiteley#scomment-$comment->id";
                    }
                    $num++;
                    if ($num < 4) {
                        $firstComments .= '<p><b><a href="'.$link.'"> '.$comment->created .'</a>:</b> ' . strip_tags($comment->description). '</p>';
                    } else {
                        $nextComments .= '<p><b><a href="'.$link.'"> '.$comment->created .'</a>:</b> ' . strip_tags($comment->description). '</p>';
                    }
                }
                ?>
                <?php echo $firstComments; ?>
                <label for="show" class="show">
                    <p>[показать все отзывы]</p>
                </label>
                <input type=radio id="show" name="group">
                <span id="content"><?php echo $nextComments; ?></span>
            <?php else: ?>
                <p>Вы пока не написали отзывов...</p>
            <?php endif; ?>
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
