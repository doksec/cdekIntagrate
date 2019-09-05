<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/cdekIntgrate/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/cdekintgrate')) {
            $cache->deleteTree(
                $dev . 'assets/components/cdekintgrate/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/cdekintgrate/', $dev . 'assets/components/cdekintgrate');
        }
        if (!is_link($dev . 'core/components/cdekintgrate')) {
            $cache->deleteTree(
                $dev . 'core/components/cdekintgrate/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/cdekintgrate/', $dev . 'core/components/cdekintgrate');
        }
    }
}

return true;