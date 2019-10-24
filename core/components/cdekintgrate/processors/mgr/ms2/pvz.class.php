<?php

class pvzGetList extends modProcessor {
    /** @var cdekIntgrate $cdekClass */
    public $cdekClass;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var msOrder $msOrder */
    public $msOrder;
    public $data = [];

    public function initialize()
    {
        $this->cdekClass = $this->modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/', []);
        $this->ms2 = $this->modx->getService('miniShop2');

        return parent::initialize();
    }
    public function process()
    {
        if (!$order_id = $this->getProperty('order_id')) {
            return 'Не передан id заказа';
        }
        if (!$this->msOrder = $this->modx->getObject('msOrder', $order_id)) {
            return 'Не получен заказ';
        }
        $getPVZ = $this->cdekClass->getPVZ($this->msOrder);

        if (!$getPVZ['success']) {
            return $getPVZ['msg'];
        }


        return $this->success('Успешно', $getPVZ['obj']);
    }
    public function success($msg = '', $object = null)
    {
        return json_encode([
            'success' => true,
            'results' => $object,
        ]);
    }
}

return 'pvzGetList';