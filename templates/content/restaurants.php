<?php include __DIR__ . '/../header.php'; ?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $city_alias; ?>" itemprop="url"><span><?php echo $city->name; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <span>Рестораны и кафе</span>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <script type="text/javascript">
            let items = <?php echo json_encode($addresses); ?>;
            ymaps.ready(function () {
                map.init('map');
                map.setClusterer(items);
                map.setCenter([items[0].geo_lat, items[0].geo_long], 10);
            });
        </script>
        <div id="map"></div>
        <h1>Рестораны и кафе <?php echo $cityGenitive->genitive; ?> - где можно покушать</h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <div>
            <?php
            if (preg_match('~\[restaurants\](.*)html(.*)"(<p>(.*)</p>)"~msU', $city->meta, $matches)) {
                echo trim($matches[3]);
            }
            $i = 0;
            ?>
        </div>
        <?php if (!empty($restaurants)): ?>
            <?php foreach ($restaurants as $restaurant): ?>
                <?php
                $i++;
                if ($i > 20) {
                    break;
                }
                $value = ($restaurant->vote > 0) ? round($restaurant->rate / $restaurant->vote, 2) : 0;
                $width = round($value / 5 * 100, 2);
                $word = \Services\stString::declension($restaurant->vote, array('голос', 'голоса', 'голосов'));
                ?>
                <div class="separator"></div>
                <div class="list-entry regular-list-entry hotel-entry">
                    <div class="city_rating_wrapper">
                        <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%"
                             itemprop="aggregateRating"
                             itemscope="<?php echo $restaurant->name; ?>" itemtype="https://schema.org/AggregateRating">
                            <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                                <div class="rating_value" style="width:<?php echo $width; ?>%"></div>
                            </div>
                            <span itemprop="itemReviewed"><?php echo $restaurant->name; ?></span>
                            (<b itemprop="ratingCount"><?php echo $word; ?></b>)
                            <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                            <meta itemprop="bestRating" content="5">
                            <meta itemprop="worstRating" content="0">
                        </div>
                    </div>
                    <strong><a href="/<?php echo $city_alias; ?>/restaurant-<?php echo $restaurant->alias; ?>-<?php echo $restaurant->id; ?>"><?php echo $restaurant->name; ?></a></strong>
                    <span>Адрес: <span itemprop="address"><?php echo $restaurant->address; ?></span></span><br>
                    <?php if (!empty($restaurant->thumb)): ?>
                        <div class="entry-thumbs">
                            <div><img src="/<?php echo $restaurant->thumb; ?>" loading="lazy"
                                      alt="<?php echo $restaurant->descr; ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    <span>Телефон: <span
                                itemprop="telephone"><?php echo $restaurant->phone ?? 'неизвестно'; ?></span></span><br>
                    <span class="sylka-adres">E-mail: <?php echo $restaurant->email ?? 'неизвестно'; ?></span><br>
                    <span class="sylka-adres">Сайт: <span
                                itemprop="url"><?php echo $restaurant->website ?? 'неизвестно'; ?></span></span>
                </div>
                </div>
            <?php endforeach; ?>
            <div id="hotel-container"></div>
            <?php if (count($restaurants) > 20): ?>
                <div id="load-more-wrapper">
                    <button class="load-more-button">Показать еще</button>
                    <span id="city_id" style="display: none"><?php echo $city->id; ?></span>
                </div>
            <?php endif; ?>
        <?php else: ?>
        К сожалению мы не знаем ресторанов в этом городе. Вы можете прислать ссылку на сайт ресторана на info@rus-trip.ru
        <?php endif; ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>