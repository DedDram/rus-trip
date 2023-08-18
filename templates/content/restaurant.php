<?php include __DIR__ . '/../header.php';
$value = ($restaurant->vote > 0) ? round($restaurant->rate / $restaurant->vote, 2) : 0;
$width = round($value / 5 * 100, 2);
$word = \Services\stString::declension($restaurant->comments, array('отзыв', 'отзыва', 'отзывов'));
?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $restaurant->cityAlias; ?>" itemprop="url"><span><?php echo $restaurant->cityName; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <a href="/<?php echo $restaurant->cityAlias; ?>/memorials" itemprop="url"><span>Достопримечательности</span></a>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <div class="city_rating_wrapper">
            <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%" itemprop="aggregateRating"
                 itemscope="<?php echo $restaurant->name; ?>" itemtype="https://schema.org/AggregateRating">
                <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b>
                    <div class="rating_value" style="width:<?php echo $width; ?>%"></div>
                </div>
                <meta itemprop="itemReviewed" content="<?php echo $restaurant->name; ?>">
                (<b itemprop="ratingCount"><?php echo $word; ?></b>)
                <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                <meta itemprop="bestRating" content="5">
                <meta itemprop="worstRating" content="0">
            </div>
        </div>
        <h1 itemprop="name"><?php echo $restaurant->name; ?></h1>
        <script type="text/javascript">
            let items = <?php echo json_encode($addresses); ?>;
            ymaps.ready(function () {
                map.init('map');
                map.add(items);
                map.setCenter([items.geo_lat, items.geo_long], 10);
            });
        </script>
        <div id="map"></div>
        <div itemscope itemtype="https://schema.org/Restaurant">
            <p>
                <?php echo $restaurant->about; ?>
            </p>
            <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <span>Адрес: <span itemprop="address"><?php echo $restaurant->address; ?></span></span><br>
                <span>Телефон: <span itemprop="telephone"><?php echo $restaurant->phone ?? 'неизвестно'; ?></span></span><br>
                <span class="sylka-adres">E-mail: <?php echo $restaurant->email ?? 'неизвестно'; ?></span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url">
                    <?php
                    if (!empty($restaurant->website)) {
                        echo "<a href='" . $restaurant->website . "'>" . $restaurant->website . "</a>";
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