document.addEventListener("DOMContentLoaded", function () {
    var toggleMenu = document.querySelector(".toggleMenu");
    var menu = document.querySelector("#menu2");
    toggleMenu.addEventListener("click", function (event) {
        event.preventDefault();
        menu.classList.toggle("show");
    });
    document.addEventListener("click", function (event) {
        if (!menu.contains(event.target) && event.target !== toggleMenu) {
            menu.classList.remove("show");
        }
    });
    var submenuParents = document.querySelectorAll(".nav .parent");
    submenuParents.forEach(function (parent) {
        parent.addEventListener("click", function (event) {
            event.preventDefault();
            var submenu = this.nextElementSibling;
            submenu.classList.toggle("active");
            if (window.innerWidth < 769) {
                if (submenu.classList.contains("active")) {
                    submenu.style.left = "0";
                } else {
                    submenu.style.left = "-9999px";
                }
            }
        });
    });
    if (window.innerWidth >= 769) {
        var parentItems = document.querySelectorAll(".nav > li");

        parentItems.forEach(function (item) {
            item.addEventListener("mouseenter", function () {
                this.classList.add("hover");
            });

            item.addEventListener("mouseleave", function () {
                this.classList.remove("hover");
            });
        });
    }
});