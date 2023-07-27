var map = (function() {
    var _private = {
        'geoMap': null,
        'moscow': [55.755381, 37.619044],
        'init': function(el) {
            _private.geoMap = new ymaps.Map(el, {
                    center: _private.moscow,
                    zoom: 9,
                    controls: [],
                },
                {
                    suppressMapOpenBlock: true,
                    suppressObsoleteBrowserNotifier: true,
                }
            );
            _private.geoMap.controls.add('zoomControl');
            _private.geoMap.controls.add(
                new ymaps.control.SearchControl({useMapBounds: true}), {top: 6,	left: 250});
        },
        'setCenter': function(res, zoom) {
            _private.geoMap.setCenter(res, zoom);
        },
        'setClusterer': function(res) {
            if(res.length){
                if(res[0].icon === undefined){
                    var clusterer = new ymaps.Clusterer();
                }else{
                    var clusterer = new ymaps.Clusterer({
                        /* Макет метки кластера pieChart.*/
                        clusterIconLayout: 'default#pieChart',
                        /* Радиус диаграммы в пикселях.*/
                        clusterIconPieChartRadius: 20,
                        /* Радиус центральной части макета.*/
                        clusterIconPieChartCoreRadius: 10,
                        /* Ширина линий-разделителей секторов и внешней обводки диаграммы.*/
                        clusterIconPieChartStrokeWidth: 1,
                        /* Определяет наличие поля balloon.*/
                        hasBalloon: false
                    });
                }
                var placemarks = [];
                var preset_icon = '';
                var preset_color = '';
                for(var i = 0; i < res.length; i++) {
                    if(res[i].icon === undefined){
                        preset_icon = 'islands#blueStretchyIcon';
                    }else{
                        preset_icon = res[i].icon;
                        preset_color = res[i].color;
                    }
                    var placemark = new ymaps.Placemark([res[i].geo_lat, res[i].geo_long], {
                        iconContent: '<a href="' + res[i].url + '" style="text-decoration: none;">' + res[i].text + '</a>'
                    }, {
                        preset: preset_icon,
                    });
                    placemarks.push(placemark);
                }

                clusterer.add(placemarks);
                _private.geoMap.geoObjects.add(clusterer);
                res=null;
                if(window.clustereradded){clustereradded=2}
            }else{
                if(window.clustereradded){clustereradded=3}
            }
        }
    };
    return {
        init: function(el) {
            _private.init(el);
        },
        setCenter: function(res, zoom) {
            _private.setCenter(res, zoom);
        },
        setClusterer: function(res) {
            _private.setClusterer(res);
        }
    };
}());

