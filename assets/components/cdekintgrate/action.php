<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
//if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
//    $modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
//}

//exit(print_r($modx->user->toArray(), 1));


if (empty($_REQUEST['action'])) {
    die('Access denied');
}

/** @var cdekIntgrate $cdekIntgrate */
$cdekIntgrate = $modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/', []);

if (!$modx->user->hasSessionContext('mgr')) {
    die(json_encode(['success' => false, 'msg' => 'Вы не авторизованы']));
}

/** @var modProcessorResponse $response */
$response = $cdekIntgrate->runProcessor('mgr/ms2/' . $_REQUEST['action'], $_REQUEST);


exit(json_encode($response->getResponse()));