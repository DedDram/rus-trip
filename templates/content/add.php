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
        <div>
            <!-- message -->
        </div>
        <div>
            <h1>Создание новой статьи</h1>
            <?php if(!empty($error)): ?>
                <div style="color: red;"><?= $error ?></div>
            <?php endif; ?>
            <form action="/news/add" method="post">
                <label for="title">Название статьи</label><br>
                <input type="text" name="title" id="title" value="<?= $_POST['title'] ?? '' ?>" size="50"><br>
                <br>
                <input type="text" name="alias" id="alias" placeholder="alias"  value="<?= $_POST['alias'] ?? '' ?>" size="50"><br>
                <br>
                <input type="text" name="metaDesc" id="metaDesc" placeholder="metaDesc" value="<?= $_POST['metaDesc'] ?? '' ?>" size="50"><br>
                <br>
                <input type="text" name="metaKey" id="metaKey" placeholder="metaKey"  value="<?= $_POST['metaKey'] ?? '' ?>" size="50"><br>
                <br>
                <input type="text" name="catid" id="catid" placeholder="catid"  value="<?= $_POST['catid'] ?? '' ?>" size="50"><br>
                <br>
                <label for="text">Текст статьи</label><br>
                <textarea name="text" id="text" rows="10" cols="80"><?= $_POST['text'] ?? '' ?></textarea><br>
                <br>
                <input type="submit" value="Создать">
            </form>
        </div>
        <div>
            <!-- aftercontent -->
        </div>
        <!-- code -->
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>

