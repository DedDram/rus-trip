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
                            <span>Фотографии</span>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <h1>Красивые фотографии <?php echo $cityGenitive->genitive; ?></h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <div id="photos-gallery">
            <?php foreach ($photos as $photo): ?>
    <span itemscope itemtype="https://schema.org/ImageObject">
	<img src="/<?php echo $photo->thumb; ?>" data-mfp-src="/<?php echo $photo->photo; ?>"
         alt="<?php echo $photo->descr; ?>" loading="lazy" title="<?php echo $photo->descr; ?>" itemprop="thumbnail"/>
	<meta itemprop="description" content="<?php echo $photo->descr; ?>"/>
    </span>
            <?php endforeach; ?>
        </div>
        <?php
        if (preg_match('~\[photos\](.*)html(.*)"(<p>(.*)</p>)"~msU', $city->meta, $matches)) {
            echo trim($matches[3]);
        }
        ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>