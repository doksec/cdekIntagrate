<?php
/** @var modX $modx */
/** @var string $page */
/** @var cdekIntgrate $cdekIntgrate */
$cdekIntgrate = $modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/', []);
switch ($modx->event->name) {
    case 'msOnManagerCustomCssJs':
        if ($page != 'orders') return;
        $modx->controller->addLastJavascript($cdekIntgrate->jsUrl.'ms2/init.js');
        $modx->controller->addCss($cdekIntgrate->cssUrl.'ms2/style.css');
        break;
}