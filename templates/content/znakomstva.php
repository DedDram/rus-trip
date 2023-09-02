<?php include __DIR__ . '/../header.php'; ?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $city->alias; ?>" itemprop="url"><span><?php echo $city->name; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <span>Знакомства</span>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <h1>Знакомства в <?php echo $city->name_morphy; ?> - онлайн и бесплатно</h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <div>
            <?php
            if (preg_match('~\[dating\](.*)html(.*)"(<p>(.*)</p>)"~msU', $city->meta, $matches)) {
                echo trim($matches[3]). ' Поможем найти любовника или любовницу для секса или более в '.$city->name_morphy.' на ночь или всю жизнь!';
            }
            ?>
        </div>

        <form method="post" action="https://love.rus-trip.ru/a-search/" data-objectid="<?php echo $city->id; ?>">
            <h1>Найти знакомства в <?php echo $city->name_morphy; ?><span></h1>
            <input type="hidden" name="a" value="search" />
            <input type="hidden" name="s" value="" />
            <input type="hidden" name="p" value="0" />
            <input type="hidden" name="d" value="1" />
            <input type="hidden" name="affiliate_id" value="48568" />
            <br/>
            <table align="center" cellspacing="0" cellpadding="5" border="0">
                <tbody>
                <tr>
                    <td>Я</td>
                    <td>
                        <select name="pol">
                            <option selected="selected" value="0">&nbsp;</option>
                            <option value="1">Парень</option>
                            <option value="2">Девушка</option>
                        </select>
                    </td>
                    <td>ищу</td>
                    <td>
                        <select name="spol">
                            <option selected="selected" value="0">&nbsp;</option>
                            <option value="1">Парня</option>
                            <option value="2">Девушку</option>
                        </select>
                    </td>
                    <td>в возрасте от</td>
                    <td><input type="text" name="bage" value="" size="3" maxlength="2" /></td>
                    <td>до</td>
                    <td><input type="text" name="tage" value="" size="3" maxlength="2" /></td>
                    <td><button type="submit" name="sub">искать</button></td>
                </tr>
                </tbody>
            </table>
            <br/>

            <table align="center">
                <tr>
                    <td>Страна:</td>
                    <td>Регион:</td>
                    <td>Город:</td>
                </tr>
                <tr id="dating-location-fields">
                    <td><?php echo $Fields->country; ?></td>
                    <td><?php echo $Fields->region; ?></td>
                    <td><?php echo $Fields->city; ?></td>
                </tr>
            </table>
        </form>
        <?php include __DIR__ . '/../comments/comments.php'; ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>