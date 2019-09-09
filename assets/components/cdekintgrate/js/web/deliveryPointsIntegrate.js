let map,
    mapID = 'cdek2_map',
    placemark,
    pointClass = '.cdek2_map-point',
    ajaxId = '#cdek2_map_ajax';

ymaps.ready(init_map);

function init_map() {
    let pointstart = $(document).find('#' + mapID).data('start'),
        allPoints = $(document).find('#' + mapID).data('coords').split(','),
        tmp = '',
        pointsArr = [];

    if (!pointstart) {
        return false;
    }

    allPoints.forEach(function (item) {
        tmp = item.split('|');
        pointsArr.push(tmp.reverse());
    });

    getMap(pointstart, pointsArr);

    function getMap(center, arr) {
        center = center.split(',').reverse();
        map = new ymaps.Map(mapID, {
            center: center,
            zoom: 11
        });

        arr.forEach(function (item) {
            placemark = new ymaps.Placemark(item);
            placemark.events.add('click', function (event) {
                let obj = event.get('target'),
                    tmp = obj.geometry._coordinates,
                    str = tmp[1] + ',' + tmp[0],
                    $item = $('.cdek2_map_container').find('[data-coord="' + str + '"]');

                $item.click();
            });
            map.geoObjects.add(placemark);
        });
    }

}

$(document).on('click', pointClass, function (event) {
    let coord = $(this).data('coord').split(',').reverse(),
        pvz_id = $(this).data('code'),
        name = $(this).data('name');
    miniShop2.Order.add('point', name);
    setTimeout(function () {
        miniShop2.Order.add('pvz_id', pvz_id);
    }, 700);
    miniShop2.Message.success('Заказ будет доставлен на ' + name + ' (' + pvz_id + ')');

    map.setCenter(coord, 14);
    $(pointClass).removeClass('is-active');
    $(this).addClass('is-active');
});

function map_reload() {
    if (map) {
        map.destroy();
    }

    setTimeout(function () {
        $.get(window.location.href, function (data) {
            var mapContainer = $(data).find(ajaxId).html();
            $(ajaxId).html(mapContainer).fadeIn();
            init_map();
            if ($(document).find(ajaxId).find(pointClass).length) {
                miniShop2.Message.success('Пункты самовывоза обновлены');
            }


        }, 'html');
    }, 500);
}