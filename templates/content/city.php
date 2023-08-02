<?php include __DIR__ . '/../header.php';
$value = ($city->vote > 0) ? round($city->rate / $city->vote, 2) : 0;
$width = round($value / 5 * 100, 2);
$word = \Services\stString::declension($city->comments, array('отзыв', 'отзыва', 'отзывов'));
?>
    <article class="box">
        <div class="city_rating_wrapper">
            <div class="rating_wrapper" data-rating-width="<?php echo $width; ?>%" itemprop="aggregateRating"
                 itemscope="<?php echo $city->name; ?>" itemtype="https://schema.org/AggregateRating">
                <div class="rating_stars"><b>1</b><b>2</b><b>3</b><b>4</b><b>5</b><div class="rating_value" style="width:<?php echo $width; ?>%"></div></div>
                <span itemprop="itemReviewed"><?php echo $city->name; ?></span>
                (<b itemprop="ratingCount"><?php echo $word; ?></b>)
                <meta itemprop="ratingValue" content="<?php echo $value; ?>">
                <meta itemprop="bestRating" content="5">
                <meta itemprop="worstRating" content="0">
            </div>
        </div>
            <h1>Город <?php echo $city->name; ?></h1>
            <!--module.db:breadcrumbs-social-buttons-->
        <img src="/<?php echo $city->photo; ?>" alt="Город <?php echo $city->name; ?>" /><br>
        <div><?php echo $city->about; ?></div>
        <div class="separator">
            <a href="/posterror?id=<?php echo $city->id; ?>&object_group=city"
               class="simplemodal"
               data-width="450" data-height="380"
               style="vertical-align: middle;float: right"
               rel="nofollow">Нашли ошибку?</a>
        </div>
        <?php include __DIR__ . '/../comments/comments.php'; ?>
    </article>
    <span class="clear"></span>
</section>

<?php include __DIR__ . '/../footer.php'; ?>
