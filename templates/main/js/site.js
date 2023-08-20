$(document).ready(function() {
    $.post("/templates/main/menu.php", function(data) {
        $(".cities-changer").html(data);
        var currentPath = window.location.pathname;
        var cityNameRegex = /^\/([^/]+)/;
        var cityNameMatch = currentPath.match(cityNameRegex);
        var cityName = cityNameMatch ? cityNameMatch[1] : null;
        var citySelect = $("#citySelect");
        var options = citySelect.find("option");
        for (var i = 0; i < options.length; i++) {
            var option = $(options[i]);
            var optionValue = option.val();
            if (optionValue.indexOf('/' + cityName) !== -1) {
                option.prop("selected", true);
                break;
            }
        }
    });

    $(".toggleMenu").click(function(e) {
        e.preventDefault();
        $("#menu2").toggleClass("menu-active");
    });
});
