<?php
/** @var modX $modx */
/** @var miniShop2 $miniShop2 */
if (!$miniShop2 = $modx->getService('miniShop2')) {
    return;
}


/** @var cdekIntgrate $cdekIntgrate */
$cdekIntgrate = $modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/', []);

$miniShop2->initialize($modx->context->key);
/** @var msOrderInterface $order */
$order = $miniShop2->order;

$ms_cdek2 = $modx->getService('ms_cdek2_class');
$jsPath = $modx->getOption('cdek_js_path');
$assetsUrl = $ms_cdek2->config['assetsUrl'];

$modx->regClientStartupScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');
$modx->regClientScript($cdekIntgrate->config['jsUrl'].'web/deliveryPointsIntegrate.js');
$modx->regClientCSS($assetsUrl.'css/web/deliveryPoints.css');

if ($order) {
    $order = $order->get();
}

$deliveryid = $order['delivery'];

if ($scriptProperties['deliveryids']) {
    $deliveryArr = explode(',', $scriptProperties['deliveryids']);
    if (!in_array($deliveryid, $deliveryArr)) {
        return false;
    }
}

/** @var pdoTools $pdo */
$pdo = $modx->getService('pdoTools');

if ($modx->getOption('cdek_calc_city')) {
    $cdekID = $order['cdek_id'];
    if ($scriptProperties['cityid']) {
        $cdekID = $scriptProperties['cityid'];
    }
}
$postCode = $order['index'];
if ($scriptProperties['citypostcode']) {
    $postCode = $scriptProperties['citypostcode'];
}

if (!empty($cdekID)) {
    $req = array(
        'cityid' => $cdekID
    );
} else {
    $req = array(
        'citypostcode' => $postCode
    );
}

if (empty($req['cityid']) && empty($req['citypostcode'])) {
    return ;
}

$url = 'https://integration.cdek.ru/pvzlist.php?'.http_build_query($req);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
$result = curl_exec($ch);
curl_close($ch);

$result = simplexml_load_string($result);
$result = json_decode( json_encode($result) , 1);

if ($result['Pvz']['@attributes']) {
    $result['Pvz'] = array($result['Pvz']);
}

$coords = '';
foreach ($result['Pvz'] as $item) {
    //$coords .= $item['@attributes']['coordX'].'|'.$item['@attributes']['coordY'].'|'.$item['@attributes']['name'].'|'.$item['@attributes']['FullAddress'].',';
    $coords .= $item['@attributes']['coordX'].'|'.$item['@attributes']['coordY'].',';
}

$countResult = count($result['Pvz']);
$out = array(
    'pvz' => $result['Pvz'],
    'city' => $result['Pvz'][0]['@attributes']['City'],
    'count' => $countResult,
    'coords' => $coords
);

if ($countResult == 0) {
    return ;
}

if ($scriptProperties['tpl']) {
    return $pdo->getChunk($scriptProperties['tpl'], $out);
}


return $pdo->getChunk('tpl.cdekIntgrate.pvz', $out);