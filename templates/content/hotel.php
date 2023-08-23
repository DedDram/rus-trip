<?php include __DIR__ . '/../header.php';
$value = ($hotel->vote > 0) ? round($hotel->rate / $hotel->vote, 2) : 0;
$width = round($value / 5 * 100, 2);
$word = \Services\stString::declension($hotel->comments, array('отзыв', 'отзыва', 'отзывов'));
?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $hotel->cityAlias; ?>" itemprop="url"><span><?php echo $hotel->cityName; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <a href="/<?php echo $hotel->cityAlias; ?>/memorials" itemprop="url"><span>Гостиницы, отели</span></a>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <div class="city_rating_wrapper">
            <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%" itemprop="aggregateRating"
                 itemscope="<?php echo $hotel->name; ?>" itemtype="https://schema.org/AggregateRating">
                <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                    <div class="rating_value" style="width:<?php echo $width; ?>%"></div>
                </div>
                <meta itemprop="itemReviewed" content="<?php echo $hotel->name; ?>">
                (<b><?php echo $word; ?></b>)
                <meta itemprop="ratingCount" content="<?php echo $hotel->comments; ?>">
                <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                <meta itemprop="bestRating" content="5">
                <meta itemprop="worstRating" content="1">
            </div>
        </div>
        <script type="text/javascript">
            let items = <?php echo json_encode($addresses); ?>;
            ymaps.ready(function () {
                map.init('map');
                map.add(items);
                map.setCenter([items.geo_lat, items.geo_long], 10);
            });
        </script>
        <div id="map"></div>
        <div>
            <a href="/posterror?id=<?php echo $hotel->id; ?>&object_group=hotel"
               class="simplemodal"
               data-width="450" data-height="380"
               style="vertical-align: middle;float: right"
               rel="nofollow">Нашли ошибку?</a><br>
        </div>
        <div itemscope itemtype="https://schema.org/Hotel">
            <h1 itemprop="name"><?php echo $hotel->name; ?></h1>
            <p>
                <?php echo $hotel->about; ?>
            </p>
            <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <span>Адрес: <span itemprop="address"><?php echo $hotel->address; ?></span></span><br>
                <span>Телефон: <span itemprop="telephone"><?php echo $hotel->phone ?? 'неизвестно'; ?></span></span><br>
                <span class="sylka-adres">E-mail: <?php echo $hotel->email ?? 'неизвестно'; ?></span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url">
                    <?php
                    if (!empty($hotel->website)) {
                        echo "<a href='" . $hotel->website . "'>" . $hotel->website . "</a>";
                    } else {
                        echo "нет данных";
                    }
                    ?>
                    </span>
                            </span>
            </div>
            <div class="separator"></div>
            <div id="photos-gallery">
                <?php if(!empty($photos)): ?>
                    <?php foreach ($photos as $photo): ?>
                        <span itemscope itemtype="https://schema.org/ImageObject">
	                        <img src="/<?php echo $photo->thumb; ?>" data-mfp-src="/<?php echo $photo->photo; ?>" title="<?php echo $photo->descr; ?>" itemprop="thumbnail" />
	                        <meta itemprop="description" content="<?php echo $photo->descr; ?>" />
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