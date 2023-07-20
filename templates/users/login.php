<?php include __DIR__ . '/../header.php'; ?>
<div id="main">
    <div id="aCenter">
        <div style="text-align: center;">
            <h1>Вход</h1>
            <?php if (!empty($error)): ?>
                <div style="background-color: #ffbdbd;padding: 5px;margin: 15px"><?= $error ?></div>
            <?php endif; ?>
            <?php if (!empty($successful)): ?>
                <div style="background-color: greenyellow;padding: 5px;margin: 15px"><?= $successful ?></div>
            <?php endif; ?>
            <form action="/users/login" method="post">
                <label>Email <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>"></label>
                <br><br>
                <label>Пароль <input type="password" name="password" value="<?= $_POST['password'] ?? '' ?>"></label>
                <br> <br>
                Запомнить меня <input type="checkbox" name="rememberMe" value="Yes" />
                <br><br>
                <input type="submit" value="Войти">
                <br><br>
                <a href="/users/reset">Забыли пароль?</a>
            </form>
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
