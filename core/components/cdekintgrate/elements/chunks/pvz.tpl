{*
	<input type="hidden" name="point">
	input должен находится в форме заказа (id="msOrder")
	Полный список переменных смотрите распечатав массив {$pvz | print}
*}

<div class="cdek2_container">
    <div class="cdek2_title">
        <div class="cdek2_title-left">
            Выберете пункт самовывоза
        </div>
        <div class="cdek2_title-right">
            Для {$city} доступно {$count} пунктов самовывоза
        </div>
    </div>
    <div class="cdek2_map_container">
        <div id="cdek2_map" data-city="{$city}" data-start="{$pvz[0]['@attributes'].coordX},{$pvz[0]['@attributes'].coordY}" data-coords="{$coords}">

        </div>
        <div class="cdek2_map-points">
            {foreach $pvz as $item}
                {set $point = $item['@attributes']}
                <div class="cdek2_map-point" data-code="{$point.Code}" data-coord="{$point.coordX},{$point.coordY}" data-name="{$point.Name}, {$point.FullAddress}">
                    <div class="cdek2_map-point__name">
                        {$point.Name}
                    </div>
                    <div class="cdek2_map-point__worktime">
                        {$point.WorkTime}
                    </div>
                    <div class="cdek2_map-point__adress">
                        {$point.FullAddress}
                    </div>
                    <div class="cdek2_map-point__phones">
                        {$point.Phone}
                    </div>
                    <div class="cdek2_map-point__email">
                        <a href="mailto:{$point.Email}">{$point.Email}</a>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

