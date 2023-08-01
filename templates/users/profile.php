<?php include __DIR__ . '/../header.php'; ?>
<article class="box">
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
        <hr>
    </div>
</article>
<span class="clear"></span>
</section>

<?php include __DIR__ . '/../footer.php'; ?>

