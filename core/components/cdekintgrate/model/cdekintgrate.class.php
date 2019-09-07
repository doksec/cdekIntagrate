<?php
include_once 'vendor/autoload.php';

use CdekSDK\Common;
use CdekSDK\Requests;

class cdekIntgrate
{
    /** @var modX $modx */
    public $modx;

    /** @var pdoFetch $pdoTools */
    public $pdoTools;

    /** @var array() $config */
    public $config = array();

    /** @var array $initialized */
    public $initialized = array();

    /** @var modError|null $error = */
    public $error = null;

    public $jsUrl;
    public $cssUrl;
    /**
     * CDEK API
     * @var \CdekSDK\CdekClient $client
     */
    public $client;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/cdekintgrate/';
        $assetsUrl = MODX_ASSETS_URL . 'components/cdekintgrate/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'customPath' => $corePath . 'custom/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->cssUrl = $this->config['cssUrl'];
        $this->jsUrl = $this->config['jsUrl'];

        $this->modx->addPackage('cdekintgrate', $this->config['modelPath']);
        $this->modx->lexicon->load('cdekintgrate:default');


        if ($this->pdoTools = $this->modx->getService('pdoFetch')) {
            $this->pdoTools->setConfig($this->config);
        }

        /**
         * TODO: Сделать порлучение данных а также переход в DEV режим
         */
        $account = 'z9GRRu7FxmO53CQ9cFfI6qiy32wpfTkd';
        $password = 'w24JTCv4MnAcuRTx0oHjHLDtyt3I6IBq';
        $this->client = new \CdekSDK\CdekClient($account, $password, new \GuzzleHttp\Client([
            'base_uri' => 'https://integration.edu.cdek.ru',
        ]));

    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties Properties for initialization.
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);

        $this->config['pageId'] = $this->modx->resource->id;

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {

                    $config = $this->makePlaceholders($this->config);
                    if ($css = $this->modx->getOption('cdekintgrate_frontend_css')) {
                        $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
                    }

                    $config_js = preg_replace(array('/^\n/', '/\t{5}/'), '', '
							cdekIntgrate = {};
							cdekIntgrateConfig = ' . $this->modx->toJSON($this->config) . ';
					');


                    $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n" . $config_js . "\n</script>", true);
                    if ($js = trim($this->modx->getOption('cdekintgrate_frontend_js'))) {

                        if (!empty($js) && preg_match('/\.js/i', $js)) {
                            $this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'), '', '
							<script type="text/javascript">
								if(typeof jQuery == "undefined") {
									document.write("<script src=\"' . $this->config['jsUrl'] . 'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
								}
							</script>
							'), true);
                            $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));

                        }
                    }

                }

                break;
        }
        return true;
    }


    /**
     * @return bool
     */
    public function loadServices()
    {
        $this->error = $this->modx->getService('error', 'error.modError', '', '');
        return true;
    }


    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        if (empty($action)) {
            return false;
        }
        #$this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/cdekintgrate/processors/';

        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));
    }


    /**
     * Method loads custom classes from specified directory
     *
     * @return void
     * @var string $dir Directory for load classes
     *
     */
    public function loadCustomClasses($dir)
    {
        $files = scandir($this->config['customPath'] . $dir);
        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                include_once($this->config['customPath'] . $dir . '/' . $file);
            }
        }
    }


    /**
     * Добавление ошибок
     * @param string $message
     * @param array $data
     */
    public function addError($message, $data = array())
    {
        $message = $this->modx->lexicon($message, $data);
        $this->error->addError($message);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->modx->error->getErrors();
    }

    /**
     * Вернут true если были ошибки
     * @return boolean
     */
    public function hasError()
    {
        return $this->modx->error->hasError();
    }


    /**
     * Обработчик для событий
     * @param modSystemEvent $event
     * @param array $scriptProperties
     */
    public function loadHandlerEvent(modSystemEvent $event, $scriptProperties = array())
    {
        switch ($event->name) {
            case 'msOnManagerCustomCssJs':
                if ($scriptProperties['page'] != 'orders') return;
                $this->modx->controller->addLastJavascript($this->jsUrl . 'ms2/init.js');
                $this->modx->controller->addCss($this->cssUrl . 'ms2/style.css');
                break;
        }

    }

    /**
     * Отправка заказа в сдэк
     * @param msOrder $msOrder
     * @return array
     */
    public function createCdekOrder(msOrder $msOrder)
    {
        /** @var modUser $user */
        $user = $msOrder->getOne('User');
        /** @var msDelivery $delivery */
        $delivery = $msOrder->getOne('Delivery');
        /** @var msOrderAddress $address */
        $address = $msOrder->getOne('Address');
        $products = $msOrder->getMany('Products');

        /** @var MsCdek $tariffID */
        $tariffID = $this->modx->getObject('MsCdek', [
            'id_delivery' => $msOrder->get('delivery')
        ]);

        if (!$tariffID) {
            return $this->out('Не найден ID тарифа отправления');
        } else {
            $tariffID = $tariffID->get('id_tarif');
        }

        $order = new Common\Order([
            'Number' => $msOrder->get('num'),
            'SendCityPostCode' => $this->modx->getOption('cdek_senderCityPostCode', [], 344000),
            'RecCityPostCode' => $address->get('index'),
            'RecCityCode' => $address->get('cdek_id'),
            'RecipientName' => $address->get('receiver'),
            'RecipientEmail' => $user->Profile->get('email'),
            'Phone' => $user->Profile->get('mobilephone'),
            'TariffTypeCode' => $tariffID,
        ]);

        $order->setAddress(Common\Address::create([
            'Street' => $address->get('street'),
            'House' => $address->get('building'),
            'Flat' => $address->get('room'),
        ]));

        $package = Common\Package::create([
            'Number' => $msOrder->get('num'),
            'BarCode' => $msOrder->get('num'),
            'Weight' => $msOrder->get('weight'),
            'SizeA' => 1,
            'SizeB' => 1,
            'SizeC' => 1,
        ]);

        /** @var msOrderProduct $product */
        foreach ($products as $product) {
            $package->addItem(new Common\Item([
                'WareKey' => $product->get('product_id'),
                'Cost' => $product->get('price'),
                'Payment' => 0, // Оплата за товар при получении (за единицу товара)
                'Weight' => $product->get('weight'),
                'Amount' => $product->get('count'),
                'Comment' => $product->get('name'),
            ]));
        }

        $order->addPackage($package);

        $request = new Requests\DeliveryRequest([
            'Number' => 'Delivery-' . $msOrder->get('num'),
        ]);
        $request->addOrder($order);

        $response = $this->client->sendDeliveryRequest($request);

        if ($response->hasErrors()) {
            $error = '';
            foreach ($response->getErrors() as $key => $order) {
                $error = $order->getMessage();
            }

            return $this->out($error);
        }

        $order = $response->getOrders()[0];
        $address->set('inner_cdek_id', $order->getDispatchNumber());
        $address->save();

        return $this->out('Заказ успешно отправлен в сдэк', true, ['number' => $order->getNumber(), 'inner_cdek' => $order->getDispatchNumber()]);
    }

    public function out($msg, $success = false, $obj = null)
    {
        return [
            'success' => $success,
            'msg' => $msg,
            'obj' => $obj
        ];
    }


}