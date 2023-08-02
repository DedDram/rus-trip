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
        <div class="separator"></div>
            <div class="list-entry regular-list-entry">
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
                                                if (!empty($memorial->website)) {
                                                    echo "<a href='" . $memorial->website . "'>" . $memorial->website . "</a>";
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