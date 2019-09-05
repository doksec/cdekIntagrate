<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var cdekIntgrate $cdekIntgrate */
$cdekIntgrate = $modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/');
$modx->lexicon->load('cdekintgrate:default');

// handle request
$corePath = $modx->getOption('cdekintgrate_core_path', null, $modx->getOption('core_path') . 'components/cdekintgrate/');
$path = $modx->getOption('processorsPath', $cdekIntgrate->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);