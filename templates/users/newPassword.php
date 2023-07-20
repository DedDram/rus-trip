<?php include __DIR__ . '/../header.php';
preg_match('~/users/(.*)/reset/~m', $_SERVER['REQUEST_URI'], $match);
?>
<div id="main">
        <div id="aCenter">
            <div style="text-align: center;">
                <div style="text-align: center;">
                    <h1>Сброс пароля</h1>
                    <p>Пароль должен быть не менее 8 символов </p>
                    <?php if (!empty($error)): ?>
                        <div style="background-color: #ffbdbd;padding: 5px;margin: 15px"><?= $error ?></div>
                    <?php endif; ?>
                    <form action="/users/<?php echo $match[1]; ?>/password" method="post">
                        <label>Пароль <input type="text" name="password" value="" placeholder="введите новый пароль" required></label>
                        <br><br>
                        <input type="submit" value="Отправить">
                    </form>
                </div>
            </div>
            <!-- code -->
        </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>