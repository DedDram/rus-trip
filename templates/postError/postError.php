<?php defined('_DEF') or exit(); ?>
<head>
    <meta name="robots" content="noindex">
    <script src="/templates/main/js/jquery-3.6.3.min.js"></script>
</head>
<style>
    .postform {
        border: 2px solid #000;
        margin: 3px 0;
        padding: 3px;
    }
    .postmsg {
        margin-bottom: 7px;
        color: blue;
        font-weight: bold;
    }
</style>
<script>
    $(document).on("submit", "#form", function (e) {
        e.preventDefault();
        let submitBtn = $(this).find('input[type="submit"]');
        submitBtn.hide();
        $.ajax({
            type: "POST",
            url: '/post/error',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data)
            {
                $("#msg").html(data.msg);
                if (data.msg !== 'Ваше сообщение успешно отправлено') {
                    submitBtn.show();
                }
            }
        });
    });
</script>

<div id="msg" class="postmsg"></div>
<h3>Укажите ошибку</h3>
<form id="form">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="task" value="postError">
    <input type="hidden" name="object_group" value="<?php echo $object_group; ?>">
    <input type="text" name="mailfrom" value="" placeholder="Ваш E-mail адрес" class="postform">
    <br>
    <textarea name="description" style="width: 95%;height: 50%" placeholder="Опишите где и что у нас неправильно" class="postform"></textarea>
    <br>
    <input type="submit" name="submit" value="Отправить">
</form>