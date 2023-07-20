var widgetId;
var onloadCallback = function() {
    widgetId = grecaptcha.render('code', {
        'sitekey' : '6LdzJ80SAAAAAFi1ZK1clo1csaldifibi0pqtGnk',
        'callback' : function(code) {
            $.ajax({
                'type': 'POST',
                'url': '/plugins/blacklist',
                'dataType': 'json',
                'timeout': 5000,
                'data': {'code': code},
                'success': function (res) {
                    if(res.status > 0) {
                        $.post('/plugins/blacklist', {code:'1'});
                        location.reload();
                    }
                },
                'error': function(jqXHR, textStatus) {
                    console.log('Error: blacklist');
                }
            });
        }
    });
};

$(document).ready(function() {
    $.getScript('https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', function(){
        return;
    });
});
