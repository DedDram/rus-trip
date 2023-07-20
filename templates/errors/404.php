<?php include __DIR__ . '/../header.php'; ?>
    <div id="main">
        <div id="aCenter">
            <h1 style="text-align: center;">Страница не найдена</h1>
            <p style="text-align: center;"><img src="/images/404.jpg" alt="404" width="406" height="272"></p>
            <p style="text-align: center;">К сожалению, запрошенная вами страница недоступна <br> или не существует.</p>
            <div style="text-align: center;">Возможные причины: <br> <br>
                <div style="text-align: center;">Вам прислали неверную ссылку</div>
                <div style="text-align: center;">Вы ошиблись в наборе адреса</div>
                <div style="text-align: center;">Страница была удалена или перемещена <br>
                    Рекомендуем перейти <a href="<?php echo $siteName; ?>">на главную страницу</a>,
                    откуда есть доступ ко всем разделам сайта.
                </div>

            </div>
            <!-- code -->
        </div>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>