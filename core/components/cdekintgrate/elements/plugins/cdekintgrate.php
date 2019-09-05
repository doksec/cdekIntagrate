<?php
/** @var modX $modx */
/** @var string $page */
/** @var cdekIntgrate $cdekIntgrate */
$cdekIntgrate = $modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/', []);
$cdekIntgrate->loadHandlerEvent($modx->event, $scriptProperties);