let comments = (function () {
            let _private = {
                    'list': function () {
                        /*Показ фото отзыва*/
                        $(".scomments-item-images-toogle").click(function (e) {
                            e.preventDefault();
                            let id = $(this).attr("data-id");
                            let el = $(this).parent().find(".scomments-item-images");
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
                                    id: id
                                },
                                success: function (rows) {
                                    el.html('');
                                    $.each(rows, function (n, row) {
                                        el.append('<a href="/images/comments/' + row.original + '" class="simplemodal" data-width="800" data-height="500"><img src="/images/comments/' + row.thumb + '"></a>');
                                    });
                                }
                            });
                            el.toggle("fast");
                        });
                        /*голосование за отзыв*/
                        $(".scomments-vote a").click(function (e) {
                            e.preventDefault();
                            let el = $(this).parent();
                            let id = $(this).attr("data-id");
                            let value = $(this).attr("data-value");

                            $.ajax({
                                type: 'POST',
                                url: '/post/comment',
                                dataType: 'json',
                                timeout: 5000,
                                data: {
                                    option: 'com_comments',
                                    view: 'item',
                                    format: 'json',
                                    task: 'vote',
                                    value: value,
                                    id: id
                                },
                                success: function (data) {
                                    el.html(data.msg);
                                }
                            });
                        });
                        /* Ответить на отзыв*/
                        $(".scomments-reply").click(function (e) {
                            e.preventDefault();
                            let text = $(this).closest('.comments-content').find('.scomments-text').html().replace(/<blockquote>(.*)<\/blockquote>/gm, '');
                            let text2 = ' ' + text.substring(0,150) + '...';
                            let link = $(this).attr('href').replace(/\?num=/g, '#');
                            text2 = text2.replace(/(\r\n|\n|\r|<br>|<br \/>|)/gm, "");
                            $('#description').val('<blockquote>' + link + text2 + '</blockquote>');
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $("#description").offset().top
                            }, 1000);
                        });

                        /*Группировка отзывов по виду - плохой, хорош. нейтрал.*/
                        $(".checked_comm_div input").click(function (e) {
                                let votes = $("input[name='radio']:checked").val();
                                let objectid = $("input[name='object_id']").val();
                                let objectgroup = $("input[name='object_group']").val();
                                let all = $(".scomments-all");
                                let count_good = document.getElementById('count_good');
                                let count_neutrally = document.getElementById('count_neutrally');
                                let count_bad = document.getElementById('count_bad');
                                let link_comment = '';
                                if (votes === 'good') {
                                    count_good.style.fontWeight = 'bold';
                                    count_good.style.color = '#8af78f';
                                    count_good.style.textShadow = 'black 1px 1px 1px, green 0px 0px 0em';
                                    count_bad.style.fontWeight = '';
                                    count_bad.style.color = '';
                                    count_neutrally.style.fontWeight = '';
                                    link_comment = '😀';
                                } else if (votes === 'neutrally') {
                                    count_neutrally.style.fontWeight = 'bold';
                                    count_good.style.fontWeight = '';
                                    count_good.style.textShadow = '';
                                    count_good.style.color = '';
                                    count_bad.style.fontWeight = '';
                                    count_bad.style.color = '';
                                    link_comment = '😐';
                                } else if (votes === 'bad') {
                                    count_bad.style.fontWeight = 'bold';
                                    count_bad.style.color = '#f44336';
                                    count_good.style.fontWeight = '';
                                    count_good.style.textShadow = '';
                                    count_good.style.color = '';
                                    count_neutrally.style.fontWeight = '';
                                    link_comment = '😡';
                                } else {
                                    count_good.style.fontWeight = '';
                                    count_good.style.color = '';
                                    count_good.style.textShadow = '';
                                    count_neutrally.style.fontWeight = '';
                                    count_bad.style.fontWeight = '';
                                    count_bad.style.color = '';
                                    link_comment = '#';
                                }

                                $.ajax({
                                    type: 'POST',
                                    url: '/post/comment',
                                    dataType: 'json',
                                    timeout: 5000,
                                    data: {
                                        option: 'com_comments',
                                        view: 'item',
                                        format: 'json',
                                        task: 'votes',
                                        votes: votes,
                                        objectid: objectid,
                                        objectgroup: objectgroup
                                    },
                                    success: function (data) {
                                        let str = '';
                                        let styleComments;
                                        let text_title;
                                        let status;

                                        for (let i = 0; i < data.length; i++) {
                                            if (Number(data[i].rate) >= 4) {
                                                styleComments = 'good_comm';
                                                text_title = 'Хороший отзыв';
                                            } else if (Number(data[i].rate) === 3 || Number(data[i].rate) === 0) {
                                                styleComments = 'neutrally_comm';
                                                text_title = 'Нейтральный отзыв';
                                            } else {
                                                styleComments = 'bad_comm';
                                                text_title = 'Плохой отзыв';
                                            }
                                            if (Number(data[i].status) === 0) {
                                                status = 'style="background-color: #ffebeb;"';
                                            } else {
                                                status = '';
                                            }
                                            str += '<div class="scomments-item ' + styleComments + '"' + status + '>';
                                            if (data[i].registered) {
                                                str += '<div class="comments-avatar-registered" ' + text_title + '  зарегистрированного пользователя"></div>';
                                            } else {
                                                str += '<div class="comments-avatar-guest"  title="' + text_title + '"></div>';
                                            }
                                            str += '<div class="comments-content">' +
                                                '<div class="scomments-title">' +
                                                '<span class="scomments-vote">' +
                                                '<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good" data-id="' + data[i].id + '" data-value="up">Это правда' + (data[i].isgood ? '<span>' + data[i].isgood + '</span>' : '') + '</a>' +
                                                '<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor" data-id="' + data[i].id + '" data-value="down">Это ложь' + (data[i].ispoor ? '<span>' + data[i].ispoor + '</span>' : '') + '</a>' +
                                                '</span>' +
                                                '<div>' +
                                                '<a href="#scomment-' + data[i].id + '" name="scomment-' + data[i].id + '" id="scomment-' + data[i].id + '"> ' + link_comment + '</a>';
                                            if (data[i].user_name) {
                                                str += '<span class="scomments-user-name" itemprop="author">' + data[i].user_name + '</span>';
                                            } else {
                                                str += '<span class="scomments-guest-name" itemprop="author">' + data[i].guest_name + '</span>';
                                            }
                                            str += '</div></div><div>' +
                                                '<span class="scomments-date" itemprop="datePublished" content="' + data[i].created + '">' + data[i].created + '</span>';
                                            if (data[i].country && data[i].country !== 'unknown') {
                                                str += '<span class="scomments-marker"></span><span class="scomments-country">' + data[i].country + '</span>';
                                            }
                                            str += '</div>' +
                                                '<div class="scomments-text" itemprop="reviewBody">' + data[i].description + '</div>';
                                            if (Number(data[i].mages) > 0) {
                                                str += '<a href="#" data-id="' + data[i].id + '" class="scomments-item-images-toogle">Показать прикрепленное фото</a>' +
                                                    '<div class="scomments-item-images"></div>';
                                            }
                                            str += '</div></div>';
                                        }
                                        $("div.pagination").empty();
                                        all.html(str);

                                        /*голосование за после выбора плохих или хороших отзывов*/
                                        $('.scomments-vote a').bind('click', function (e) {
                                            e.preventDefault();
                                            let el = $(this).parent();
                                            let id = $(this).attr("data-id");
                                            let value = $(this).attr("data-value");
                                            $.ajax({
                                                type: 'POST',
                                                url: '/post/comment',
                                                dataType: 'json',
                                                timeout: 5000,
                                                data: {
                                                    option: 'com_comments',
                                                    view: 'item',
                                                    format: 'json',
                                                    task: 'vote',
                                                    value: value,
                                                    id: id
                                                },
                                                success: function (data) {
                                                    el.html(data.msg);
                                                }
                                            });
                                        });
                                    }
                                });
                            }
                        );
                    },
                    'form':

                        function () {
                            $(document).on("blur", "#email", function (e) {
                                $("label[for='email']").hide();
                            });
                            $(document).on("focus", "#email", function (e) {
                                $("label[for='email']").show();
                            });
                            /*клик по кнопке выбора файла (дополнительная загрузка 2х скриптов)*/
                            $(document).on("click", "#file", function (e) {
                                $.getScript("/templates/comments/js/slick.js");
                                $.getScript("/templates/comments/js/jquery.form.js").done(function(script, textStatus) {
                                    $('#slider').slick({
                                        slidesToShow: 3,
                                        slidesToScroll: 3,
                                        variableWidth: true,
                                        infinite: false,
                                    });
                                });
                            });
                            $(document).on("click", "#submit", function (e) {
                                e.preventDefault();
                                $('#myform').submit();
                            });
                            $(document).on("change", "#upload [type=file]", function (e) {
                                e.preventDefault();
                                $('#upload').submit();
                            });

                            /*отправка нового отзыва*/
                            $(document).on("submit", "#myform", function (e) {
                                e.preventDefault();
                                let data = $(this).serializeArray();
                                $.ajax({
                                    type: 'POST',
                                    url: '/post/comment',
                                    dataType: 'json',
                                    timeout: 5000,
                                    data: data,
                                    beforeSend: function () {
                                        $('#msg').hide();
                                        $('#loader').html('<img src="/images/loader.gif">').show();
                                    },
                                    success: function (data) {
                                        if (data.status === 1) {
                                            $('#msg').attr('class', 'msg-success').html(data.msg).show();
                                            $('#myform').trigger('reset');
                                            $('#wrapper').hide();
                                            $('#loader').hide();
                                        }
                                        if (data.status === 2) {
                                            $('#msg').attr('class', 'msg-error').html(data.msg).show();
                                            $('#loader').hide();
                                        }
                                        _private.scroll();
                                    }
                                });
                            });

                            /*загрузка фото к отзыву*/
                            $(document).on("submit", "#upload", function (e) {
                                e.preventDefault();
                                $('#upload').ajaxSubmit({
                                    beforeSend: function () {
                                        $('#msg').hide();
                                        $('#percent').html('0%').show();
                                    },
                                    uploadProgress: function (event, position, total, percentComplete) {
                                        $('#percent').html(percentComplete + '%');
                                    },
                                    success: function (data) {
                                        if (data.status === 1) {
                                            $('#slider').slick('slickAdd', '<div class="row-slide"><a href="#" data-id="' + data.id + '" data-attach="' + data.attach + '" class="remove-slide"></a><img src="/images/comments/' + data.thumb + '"></div>');
                                        }
                                        if (data.status === 2) {
                                            $('#msg').attr('class', 'msg-error').html(data.msg).show();
                                        }
                                        $('#upload').clearForm();
                                        $('#percent').html('100%').hide();
                                    }
                                });
                            });
                            /*удаление фото из отзыва*/
                            $(document).on("click", ".remove-slide", function (e) {
                                e.preventDefault();
                                $('#msg').hide();
                                let slideIndex = $(this).parent().attr("data-slick-index");
                                let parentDiv = $(this).parent('div');
                                let id_img = $(this).attr("data-id");
                                let attach = $(this).attr("data-attach");
                                $.ajax({
                                    type: 'POST',
                                    url: '/post/comment',
                                    dataType: 'json',
                                    data: {
                                        option: 'com_comments',
                                        format: 'json',
                                        task: 'removeImage',
                                        id_img: id_img,
                                        attach: attach
                                    },
                                    beforeSend: function () {
                                        $('#percent').html('').show();
                                    },
                                    success: function (data) {
                                        if (data.status === 1) {
                                            parentDiv.remove();
                                        }
                                        if (data.status === 2) {
                                            $('#msg').attr('class', 'msg-error').html(data.msg).show();
                                        }
                                        $('#percent').html('').hide();
                                    }
                                });
                            });
                        }
                    ,
                    'scroll':

                        function (callback) {
                            $('body,html').animate({
                                scrollTop: $(".scomments-anchor").offset().top
                            }, 300, function () {
                                if (callback) {
                                    callback();
                                }
                            });
                        }
                }
            ;
            return {
                init: function () {
                    _private.list();
                    _private.form();
                }
            }
        }
        ()
    );

$(document).ready(function () {
    comments.init();
});