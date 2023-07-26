<?php include __DIR__ . '/../header.php'; ?>
    <article class="box">
            <h1>Город <?php echo $city->name; ?></h1>
            <!--module.db:breadcrumbs-social-buttons-->
        <img src="/<?php echo $city->photo; ?>" alt="Город <?php echo $city->name; ?>" /><br>
        <div><?php echo $city->about; ?></div>
    </article>
    <span class="clear"></span>
</section>

<?php include __DIR__ . '/../footer.php'; ?>
