<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $fields = ['cdek_id', 'inner_cdek_id'];

            foreach ($fields as $item) {
                $table = $modx->getTableName('msOrderAddress');
                $sql = "ALTER TABLE $table  ADD $item int(10) NULL;";
                $modx->exec($sql);
                $modx->log(3, "Добавлено поле <b>$item</b> в $table");
            }
            /** @var miniShop2 $miniShop2 */
            if ($miniShop2 = $modx->getService('miniShop2')) {
                $miniShop2->addPlugin('CdekIntegratorFields', '{core_path}components/cdekintgrate/ms2/index.php');
                $modx->log(3, 'Плагин дополнительного поля <b>CdekIntegratorFields</b> установлен');
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            /** @var miniShop2 $miniShop2 */
//            if ($miniShop2 = $modx->getService('miniShop2')) {
//                $miniShop2->removePlugin('CdekIntegratorFields');
//                $modx->log(3, 'Плагин дополнительного поля удален')
//            }
            break;
    }
}

return true;