<?php
/**
 * @var Content $content
 */

use Models\Content\Content;

include __DIR__ . '/../header.php';
?>
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
        <div>
            <!-- message -->
        </div>
        <div>
            <h1>Редактирование статьи</h1>
            <?php if(!empty($error)): ?>
                <div style="color: red;"><?= $error ?></div>
            <?php endif; ?>
            <form action="/news/<?= $content->getId() ?>-<?= $content->getAlias() ?>/edit" method="post">
                <label for="title">Название статьи</label><br>
                <input type="text" name="title" id="title" value="<?= $_POST['title'] ?? $content->getTitle() ?>" size="50"><br>
                <br>
                <label for="text">Текст статьи</label><br>
                <textarea name="text" id="text" rows="10" cols="80"><?= $_POST['text'] ?? $content->getText() ?></textarea><br>
                <br>
                <input type="submit" value="Обновить">
            </form>
        </div>
        <div>
            <!-- aftercontent -->
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
