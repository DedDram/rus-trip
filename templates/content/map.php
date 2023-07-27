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
                            <span>Карта</span>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <h1>Карта <?php echo $cityGenitive->genitive; ?> с улицами и номерами домов</h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <?php
        if (preg_match('~\[map\](.*)html(.*)"(<p>(.*)</p>)"~msU', $city->meta, $matches)) {
            echo trim($matches[3]);
        }
        ?>
        <div><?php echo $map; ?></div>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>