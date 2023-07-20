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
            <?php if (!empty($user) && $user->isAdmin()): ?>
                <?php if (!empty($_GET['task'])): ?>
                    <div id="system-message">
                        <div class="alert alert-message">
                            <div>
                                <?php if ($_GET['task'] == 'unpublish'): ?>
                                    <div class="alert-message">Комментарий снят с публикации</div>
                                <?php elseif ($_GET['task'] == 'publish'): ?>
                                    <div class="alert-message">Комментарий опубликован</div>
                                <?php elseif ($_GET['task'] == 'remove'): ?>
                                    <div class="alert-message">Комментарий удален</div>
                                <?php elseif ($_GET['task'] == 'blacklist'): ?>
                                    <div class="alert-message">Пользователь заблокирован</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div>
                <?php if (!empty($user)): ?>
                    <?php if ($user->isAdmin()): ?>
                        <a href="/news/<?= $content->id ?>-<?= $content->alias ?>/edit">редактировать</a>
                    <?php endif; ?>
                <?php endif; ?>
                <p><?= $content->text ?></p>
            </div>
            <div>
                <!-- aftercontent -->
            </div>
            <!-- code -->
            <?php include __DIR__ . '/../maps/maps.php'; ?>
            <!-- code -->
        </div>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>