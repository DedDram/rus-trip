$(document).ready(function() {
    $(document).on("click", ".scomments-control-delete, .scomments-control-publish, .scomments-control-unpublish, .scomments-control-blacklist", function(e) {
        e.preventDefault();
        let el = $(this).parent().find('.scomments-control-msg');
        let task = $(this).attr("data-task");
        let object_group = $(this).attr("data-object-group");
        let object_id = $(this).attr("data-object-id");
        let item_id = $(this).attr("data-item-id");
        el.html('<img src="/templates/comments/image/loader.gif">').show();
        $.ajax({
            type: 'POST',
            url: '/post/comment',
            dataType: 'json',
            timeout: 5000,
            data: {
                option: 'com_comments',
                view: 'moderation',
                format: 'json',
                task: task,
                object_group: object_group,
                object_id: object_id,
                item_id: item_id,
            },
            success: function (data) {
                el.html(data.msg);
            }
        });
    });

    $(document).on("click", ".scomments-control-edit", function(e) {
        e.preventDefault();
        let text = $(this).closest('.comments-content').find('.scomments-text').html();
        let msg = $(this).parent().find('.scomments-control-msg');
        let form = $('.scomments-form');
        let task1 = $('#task1');
        let task2 = $('#task2');
        let item_id = $(this).attr("data-item-id");
        $('#description').val(text);
        form.find('header').text( "Редактировать отзыв" );
        task1.val('edit');
        task2.val('add');
        form.find('input[name=item_id]').val(item_id);
        let el = $('#slider');
        $.ajax({
            type: 'POST',
            url: '/post/comment',
            dataType: 'json',
            timeout: 5000,
            data: {
                option: 'com_comments',
                view: 'images',
                format: 'json',
                task: 'cut',
                id: item_id
            },
            success: function (rows) {
                msg.html(rows.msg);
                $.each(rows, function (n, row) {
                    el.append('<div class="row-slide"><a href="#" data-id="' + row.id + '" data-attach="' + row.attach + '" class="remove-slide"></a><img src="/images/comments/' + row.thumb + '"></div>');
                });
            }
        });
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#description").offset().top
        }, 1000);
    });
});