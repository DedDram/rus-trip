var map = (function () {
    var _private = {
        geoMap: null,
        init: function (el) {
            _private.geoMap = new ymaps.Map(el, {
                center: [55.76, 37.64],
                zoom: 10
            });
        },
        add: function (res) {
            if (res && _private.geoMap) {
                var placemark = new ymaps.Placemark([res.geo_lat, res.geo_long], {
                    iconContent: '<a href="' + res.url + '" style="text-decoration: none;">' + res.text + '</a>'
                }, {
                    preset: res.icon || 'islands#blueStretchyIcon',
                });
                _private.geoMap.geoObjects.add(placemark);
                _private.setCenter([res.geo_lat, res.geo_long], 10);
            }
        },

        setCenter: function (coords, zoom) {
            if (_private.geoMap) {
                _private.geoMap.setCenter(coords, zoom);
            }
        }
    };
    return {
        init: function (el) {
            _private.init(el);
        },
        setCenter: function (coords, zoom) {
            _private.setCenter(coords, zoom);
        },
        setClusterer: function (res) {
            _private.setClusterer(res);
        },
        add: function (res) {
            _private.add(res);
        }
    };
})();