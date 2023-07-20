<?php include __DIR__ . '/../header.php'; ?>
    <div id="main">
        <div id="aCenter">
            <div style="min-height: 230px;">
                <!-- banner2 -->
            </div>
            <div>
                <!-- user19 -->
            </div>
            <div>
                <!-- user18 -->
            </div>
            <div>
                <!-- user12 -->
            </div>
            <?php if (!empty($error)): ?>
                <div style="background-color: #ffbdbd;padding: 5px;margin: 15px"><?= $error ?></div>
            <?php endif; ?>
            <?php if (!empty($successful)): ?>
                <div style="background-color: greenyellow;padding: 5px;margin: 15px"><?= $successful ?></div>
            <?php endif; ?>
            <div>
                <?php foreach ($contents as $content): ?>
                    <div style="width: 100%">
                        <p><?= mb_substr($content->getText(), 0, 550, 'UTF-8') . '...'; ?></p>
                        <a href="/<?=$catAlias;?>/<?= $content->getId() ?>-<?= $content->getAlias() ?>">читать подробнее...</a>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($content)): ?>
                <div class="text-center">
                    <p>Статей: <?=count($contents)*$page;?> из <?=$pagesCount;?></p>
                    <?php if($pagination->countPages > 1): ?>
                        <?=$pagination;?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div>
                <!-- aftercontent -->
            </div>
            <!-- code -->
        </div>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>