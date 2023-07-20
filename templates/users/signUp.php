<?php include __DIR__ . '/../header.php';
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
        <div>
            <h1>Регистрация</h1>
            <?php if (!empty($error)): ?>
                <div style="background-color: #f59f9f;padding: 5px;margin: 15px"><?= $error ?></div>
            <?php endif; ?>
            <form action="/users/register" method="post">
                <label><input size="30" minlength="4" maxlength="25" type="text" name="name" placeholder="Это имя будет видно в отзывах" value="<?= $_POST['name'] ?? '' ?>"> Имя</label>
                <br><br>
                <label><input size="30" minlength="5" maxlength="20" type="text" name="username" placeholder="Придумайте логин для авторизации" value="<?= $_POST['username'] ?? '' ?>"> Логин</label>
                <br><br>
                <label><input size="30" type="email" maxlength="50" name="email" value="<?= $_POST['email'] ?? '' ?>"> Email</label>
                <br><br>
                <label><input size="30" minlength="8" maxlength="25" type="password" name="password" value="<?= $_POST['password'] ?? '' ?>"> Пароль </label>
                <br><br>
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" value="Зарегистрироваться">
            </form>
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
