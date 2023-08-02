<?php include __DIR__ . '/../header.php';
$value = ($memorial->vote > 0) ? round($memorial->rate / $memorial->vote, 2) : 0;
$width = round($value / 5 * 100, 2);
$word = \Services\stString::declension($memorial->comments, array('отзыв', 'отзыва', 'отзывов'));
?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $memorial->cityAlias; ?>"
                       itemprop="url"><span><?php echo $memorial->cityName; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <a href="/<?php echo $memorial->cityAlias; ?>/memorials" itemprop="url"><span>Достопримечательности</span></a>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <script type="text/javascript">
            let items = <?php echo json_encode($addresses); ?>;
            ymaps.ready(function () {
                map.init('map');
                map.add(items);
                map.setCenter([items.geo_lat, items.geo_long], 10);
            });
        </script>
        <div class="city_rating_wrapper">
            <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%" itemprop="aggregateRating"
                 itemscope="<?php echo $memorial->name; ?>" itemtype="https://schema.org/AggregateRating">
                <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                    <div class="rating_value" style="width:<?php echo $width; ?>%"></div>
                </div>
                <meta itemprop="itemReviewed" content="<?php echo $memorial->name; ?>">
                (<b itemprop="ratingCount"><?php echo $word; ?></b>)
                <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                <meta itemprop="bestRating" content="5">
                <meta itemprop="worstRating" content="0">
            </div>
        </div>
        <h1 itemprop="name"><?php echo $memorial->name; ?></h1>
        <div id="map"></div>
        <div itemscope itemtype="https://schema.org/TouristAttraction">
            <p>
                <?php echo $memorial->about; ?>
            </p>
            <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <span>Адрес: <span itemprop="address"><?php echo $memorial->address; ?></span></span><br>
                <span>Телефон: <span
                            itemprop="telephone"><?php echo $memorial->phone ?? 'неизвестно'; ?></span></span><br>
                <span class="sylka-adres">E-mail: <?php echo $memorial->email ?? 'неизвестно'; ?></span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url">
                    <?php
                    if (!empty($memorial->website)) {
                        echo "<a href='" . $memorial->website . "'>" . $memorial->website . "</a>";
                    } else {
                        echo "нет данных";
                    }
                    ?>
                    </span>
                            </span>
            </div>
            <div>
                <a href="/posterror?id=<?php echo $memorial->id; ?>&object_group=memorial"
                   class="simplemodal"
                   data-width="450" data-height="380"
                   style="vertical-align: middle;float: right"
                   rel="nofollow">Нашли ошибку?</a><br>
            </div>
            <div class="separator"></div>
            <div id="photos-gallery">
                <?php if (!empty($photos)): ?>
                    <?php foreach ($photos as $photo): ?>
                        <span itemscope itemtype="https://schema.org/ImageObject">
	                        <img src="/<?php echo $photo->thumb; ?>" data-mfp-src="/<?php echo $photo->photo; ?>"
                                 title="<?php echo $photo->descr; ?>" itemprop="thumbnail"/>
	                        <meta itemprop="description" content="<?php echo $photo->descr; ?>"/>
                        </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php include __DIR__ . '/../comments/comments.php'; ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>