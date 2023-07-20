$(document).ready(function(){
    $(document).on('click', '.simplemodal', function(e) {
        e.preventDefault();
        let url = $(this).attr("href");
        let width = $(this).data("width") + "px";
        let height = $(this).data("height") + "px";
        let win_width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
        if(win_width < 769) {
            width = 95 + "%";
            height = 95 + "%";
        }
        let modal = "<div class='modal'><iframe src='" + url + "'></iframe></div>";
        $("body").append(modal);
        $(".modal").css({"width": width, "height": height, "position": "fixed", "z-index": "9999", "top": "50%", "left": "50%", "transform": "translate(-50%,-50%)", "background-color": "white", "border": "1px solid black", "box-shadow": "0 8px 16px 0 rgba(0,0,0,0.2)"});
        $("iframe").css({"width": "100%", "height": "100%"});
        let closeButton = "<div class='close-button'></div>";
        $(".modal").append(closeButton);
        $(".close-button").css({"background": "url(/images/x.png) no-repeat", "width":"25px", "height":"29px", "display":"inline", "z-index":"3200", "position":"absolute", "top":"-15px", "right":"-16px", "cursor":"pointer"});
        let overlay = "<div class='modal-overlay'></div>";
        $("body").append(overlay);
        $(".modal-overlay").css({"width": "100%", "height": "100%", "position": "fixed", "top": "0", "left": "0", "background-color": "rgba(0,0,0,0.5)", "z-index": "9998"});
    });
    $(document).on('click', '.modal', function(){
        $(this).remove();
        $(".modal-overlay").remove();
    });
    $(document).on('click', '.close-button', function(){
        $(".modal").remove();
        $(".modal-overlay").remove();
    });
    $(document).on('click', '.modal-overlay', function(){
        $(".modal").remove();
        $(this).remove();
    });
});
