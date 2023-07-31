<?php include __DIR__ . '/../header.php'; ?>
    <article class="box">
        <div class="breadcrumbs">
            <span itemscope="" itemtype="https://schema.org/WebPage">
                <span itemprop="breadcrumb">
                    <a href="/<?php echo $hotel->cityAlias; ?>" itemprop="url"><span><?php echo $hotel->cityName; ?></span></a>
                </span>
                <span> »
                    <span itemscope="" itemtype="https://schema.org/WebPage">
                        <span itemprop="breadcrumb">
                            <a href="/<?php echo $hotel->cityAlias; ?>/memorials" itemprop="url"><span>Достопримечательности</span></a>
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
            <h1 itemprop="name"><?php echo $hotel->name; ?></h1>
            <p>
                <?php echo $hotel->about; ?>
            </p>
            <div class="separator"></div>
            <div class="list-entry regular-list-entry">
                <span>Адрес: <span itemprop="address"><?php echo $hotel->address; ?></span></span><br>
                <span>Телефон: <span itemprop="telephone"><?php echo $hotel->phone ?? 'неизвестно'; ?></span></span><br>
                <span class="sylka-adres">E-mail: <?php echo $hotel->email ?? 'неизвестно'; ?></span><br>
                <span class="sylka-adres">Сайт: <span itemprop="url"><?php echo $hotel->website ?? 'неизвестно'; ?></span></span>
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