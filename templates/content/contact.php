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
                            <span>Обратная связь</span>
                        </span>
                    </span>
                </span>
            </span>
        </div>
        <h1>Контакты</h1>
        <!--module.db:breadcrumbs-social-buttons-->
        <div style="min-height: 500px">
            <p>По всем вопросам пишите на info[СОБАКА]rus-trip.ru</p>
        </div>
    </article>
    <span class="clear"></span>
    </section>

    <?php include __DIR__ . '/../footer.php'; ?>
