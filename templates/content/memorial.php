<?php include __DIR__ . '/../header.php'; ?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $memorial->cityAlias; ?>" itemprop="url"><span><?php echo $memorial->cityName; ?></span></a>
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
        <div id="map"></div>
        <div itemscope itemtype="https://schema.org/TouristAttraction">
            <h1 itemprop="name"><?php echo $memorial->name; ?></h1>
            <p>
                <?php echo $memorial->about; ?>
            </p>
            <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <span>Адрес: <span itemprop="address"><?php echo $memorial->address; ?></span></span><br>
                <span>Телефон: <span itemprop="telephone"><?php echo $memorial->phone ?? 'неизвестно'; ?></span></span><br>
                <span class="sylka-adres">E-mail: <?php echo $memorial->email ?? 'неизвестно'; ?></span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url"><?php echo $memorial->website ?? 'неизвестно'; ?></span></span>
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
        </div>
    </article>
    <span class="clear"></span>
    </section>

<?php include __DIR__ . '/../footer.php'; ?>