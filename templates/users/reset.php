<?php include __DIR__ . '/../header.php'; ?>
<?php
if(!isset($_SESSION))
{
    session_start();
}
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];
?>
<div id="main">
    <div id="aCenter">
        <div style="text-align: center;">
            <h1>Забыли пароль?</h1>
            <p>Введите e-mail и нажмите "Отправить". Если в нашей базе есть пользователь с таким e-mail, на него придет ссылка для восстановления пароля.</p>
            <?php if (!empty($error)): ?>
                <div style="background-color: #ffbdbd;padding: 5px;margin: 15px"><?= $error ?></div>
            <?php endif; ?>
            <?php if (!empty($successful)): ?>
                <div style="background-color: greenyellow;padding: 5px;margin: 15px"><?= $successful ?></div>
            <?php endif; ?>
            <form action="/users/reset" method="post">
                <label>Email <input type="email" name="email" value="" placeholder="введите e-mail" required></label>
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <br><br>
                <input type="submit" value="Отправить">
            </form>
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
