$(document).ready(function() {
    $.post("/templates/main/menu.php", function(data) {
        $(".cities-changer").html(data);
    });
});