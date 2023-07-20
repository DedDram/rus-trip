<?php
/**
 * @var Article $article
 */

use Models\Articles\Article;

include __DIR__ . '/../header.php';
?>
<div id="main">
    <div id="aCenter">
        <div style="min-height: 230px;">
            место модуля banner2
        </div>
        <div>
            user19
        </div>
        <div>
            user18
        </div>
        <div>
            user12
        </div>
        <div>
            message
        </div>
        <div>
            <h1>Редактирование статьи</h1>
            <?php if(!empty($error)): ?>
                <div style="color: red;"><?= $error ?></div>
            <?php endif; ?>
            <form action="/articles/<?= $article->getId() ?>/edit" method="post">
                <label for="name">Название статьи</label><br>
                <input type="text" name="name" id="name" value="<?= $_POST['name'] ?? $article->getName() ?>" size="50"><br>
                <br>
                <label for="text">Текст статьи</label><br>
                <textarea name="text" id="text" rows="10" cols="80"><?= $_POST['text'] ?? $article->getText() ?></textarea><br>
                <br>
                <input type="submit" value="Обновить">
            </form>
        </div>
        <div>
            aftercontent
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
