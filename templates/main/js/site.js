$(document).ready(function() {
    $.post("/templates/main/menu.php", function(data) {
        $(".cities-changer").html(data);
        var currentPath = window.location.pathname;
        var citySelect = $("#citySelect");
        var options = citySelect.find("option");
        for (var i = 0; i < options.length; i++) {
            var option = $(options[i]);
            if (option.val() === currentPath) {
                option.prop("selected", true);
                break;
            }
        }
    });
});
