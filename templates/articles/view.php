<?php include __DIR__ . '/../header.php'; ?>
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
                <h1><?= $article->getName() ?></h1>
                <?php if (!empty($user)): ?>
                    <?php if ($user->isAdmin()): ?>
                        <a href="/articles/<?= $article->getId() ?>/edit">редактировать</a>
                    <?php endif; ?>
                <?php endif; ?>
                <p><?= $article->getText() ?></p>
                <p>Автор:
                    <?php
                    if (!empty($article->getAuthor())) {
                        echo $article->getAuthor()->getUserName();
                    }
                    ?>
                </p>
            </div>
            <div>
                aftercontent
            </div>
            <!-- code -->
        </div>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>