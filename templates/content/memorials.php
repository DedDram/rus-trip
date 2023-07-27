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
                echo trim($matches[3]);
            }
            ?>
        </div>
        <?php foreach ($memorials as $memorial): ?>
        <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <strong><a href="https://rus-trip.ru/novosibirsk/dostoprimechatelnosti/<?php echo $memorial->alias; ?>-<?php echo $memorial->id; ?>"><?php echo $memorial->name; ?></a></strong>
                <span>Адрес: <span itemprop="address"><?php echo $memorial->address; ?></span></span><br>
                 <?php if (!empty($memorial->thumb)): ?>
                 <div class="entry-thumbs">
                    <div><img src="https://rus-trip.ru/<?php echo $memorial->thumb; ?>" loading="lazy" alt="<?php echo $memorial->descr; ?>"></div>
                    </div>
                    <span>Телефон: <span itemprop="telephone"><?php echo $memorial->phone ?? 'неизвестно'; ?></span></span><br>
                    <span class="sylka-adres">E-mail: <?php echo $memorial->email ?? 'неизвестно'; ?></span><br>
                     <span class="sylka-adres">Сайт: <span itemprop="url"><?php echo $memorial->website ?? 'неизвестно'; ?></span></span>
                 </div>
                 <?php endif; ?>
            </div>
       <?php endforeach; ?>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>