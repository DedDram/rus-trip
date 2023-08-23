$(document).ready(function() {
    var redirectParam = getUrlParameter('redirect');
    if (redirectParam) {
        showOverlay();
    }
    console.log(redirectParam);

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    function showOverlay() {
        var overlay = $('<div id="overlay"></div>').appendTo('body');

        var text = $('<div id="text">На этой странице представлена подробная информация про нашу столицу. ' +
            'Можете остановить таймер и полюбопытствовать или же нажать "перейти" и попадете на сайт '+ redirectParam +'</div>').appendTo(overlay);

        var timer = $('<div id="timer">20</div>').appendTo(overlay);

        var buttonsContainer = $('<div id="buttons-container"></div>').css({
            marginTop: '10px'
        }).appendTo(overlay);

        var stopButton = $('<button id="stop">Остановить</button>').appendTo(buttonsContainer);

        var goButton = $('<button id="go">Перейти</button>').css({
            marginLeft: '15px'
        }).appendTo(buttonsContainer);


        var interval;

        function startTimer() {
            var time = 20;
            interval = setInterval(function() {
                time--;
                timer.text(time);

                if (time === 0) {
                    clearInterval(interval);
                     window.location.href = 'http://' + redirectParam;
                }
            }, 1000);
        }

        startTimer();

        stopButton.on('click', function() {
            clearInterval(interval);
            overlay.css('height', '10%');
            goButton.show();
            text.remove();
        });

        goButton.on('click', function() {
            window.location.href = 'http://' + redirectParam;
        });
    }
});