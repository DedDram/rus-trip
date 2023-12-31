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
                            <span>Достопримечательности</span>
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
        <h1>Достопримечательности <?php echo $cityGenitive->genitive; ?></h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <div>
            <?php
            if (preg_match('~\[memorials\](.*)html(.*)"(<p>(.*)</p>)"~msU', $city->meta, $matches)) {
                if(!empty($matches[3])){
                    echo trim($matches[3]);
                }
            }
            ?>
        </div>
        <div>
            <a href="/posterror?id=<?php echo $city->id; ?>&object_group=memorials"
               class="simplemodal"
               data-width="450" data-height="380"
               style="vertical-align: middle;float: right"
               rel="nofollow">Нашли ошибку?</a><br>
        </div>
        <?php foreach ($memorials as $memorial): ?>
            <?php
            $value = ($memorial->vote > 0) ? round($memorial->rate / $memorial->vote, 2) : 0;
            $width = round($value / 5 * 100, 2);
            $word = \Services\stString::declension($memorial->vote, array('отзыв', 'отзыва', 'отзывов'));
            ?>
        <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <div class="city_rating_wrapper">
                    <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%" itemprop="aggregateRating"
                         itemscope="<?php echo $memorial->name; ?>" itemtype="https://schema.org/AggregateRating">
                        <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                            <div class="rating_value" style="width:<?php echo $width; ?>%"></div>
                        </div>
                        <meta itemprop="itemReviewed" content="<?php echo $memorial->name; ?>">
                        (<b><?php echo $word; ?></b>)
                        <meta itemprop="ratingCount" content="<?php echo $memorial->vote; ?>">
                        <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="worstRating" content="0">
                    </div>
                </div>
                <strong><a href="/<?php echo $city_alias; ?>/memorial-<?php echo $memorial->alias; ?>-<?php echo $memorial->id; ?>"><?php echo $memorial->name; ?></a></strong>
                <span>Адрес: <span itemprop="address"><?php echo $memorial->address; ?></span></span><br>
                 <?php if (!empty($memorial->thumb)): ?>
                 <div class="entry-thumbs">
                    <div><img src="/<?php echo $memorial->thumb; ?>" loading="lazy" alt="<?php echo $memorial->descr; ?>"></div>
                    </div>
                 <?php endif; ?>
                    <span>Телефон: <span itemprop="telephone"><?php echo $memorial->phone ?? 'неизвестно'; ?></span></span><br>
                    <span class="sylka-adres">E-mail: <?php echo $memorial->email ?? 'неизвестно'; ?></span><br>
                     <span class="sylka-adres">Сайт: <span itemprop="url">
                                                <?php
                                                if (!empty($memorial->website) && $memorial->website != 'нет данных') {
                                                    $memorial->website = str_replace(array('http://', 'https://'), '', $memorial->website);
                                                    $memorial->website = preg_replace('~/$~', '', $memorial->website);
                                                    echo "<a href='/index.php?redirect=" . $memorial->website . "'>" . $memorial->website . "</a>";
                                                } else {
                                                    echo "нет данных";
                                                }
                                                ?>
                         </span></span>
                 </div>
            </div>
       <?php endforeach; ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>