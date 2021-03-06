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

        $account = $this->modx->getOption('cdek_auth_login', []);
        $password = $this->modx->getOption('cdek_auth_password', []);
        if ($this->modx->getOption('cdekintgrate_tests', [], true)) {
            $account = 'z9GRRu7FxmO53CQ9cFfI6qiy32wpfTkd'; //TODO: Сделать опцией на всякий случай
            $password = 'w24JTCv4MnAcuRTx0oHjHLDtyt3I6IBq'; //TODO: Сделать опцией на всякий случай
            $this->client = new \CdekSDK\CdekClient($account, $password, new \GuzzleHttp\Client([
                'base_uri' => 'https://integration.edu.cdek.ru', //TODO: Сделать опцией на всякий случай
            ]));
        } else {
            $this->client = new \CdekSDK\CdekClient($account, $password);
        }

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
                $configJson = json_encode($this->config);
                $this->modx->controller->addHtml("
                    <script>
                        let cdekIntegrateConfig = $configJson;
                    </script>
                ");
                $this->modx->controller->addLastJavascript($this->jsUrl . 'ms2/misc.js');
                $this->modx->controller->addLastJavascript($this->jsUrl . 'ms2/init.js');
                $this->modx->controller->addCss($this->cssUrl . 'ms2/style.css');
                break;
            case 'msOnCreateOrder':
                /** @var msOrder $msOrder */
                $msOrder = $scriptProperties['msOrder'];
                if ($this->modx->getOption('cdekintgrate_auto_create', [], true)) {
                    $this->createCdekOrder($msOrder);
                }
                break;
            case 'msOnChangeOrderStatus':
                if ($scriptProperties['status'] == 1) {
                    return;
                }
                if (!$this->modx->getOption('cdekintgrate_change_status', [], false)) {
                    return;
                }
                /** @var msOrder $msOrder */
                $msOrder = $scriptProperties['order'];
                $this->changeCdekOrder($msOrder);
                break;
        }

    }

    /**
     * Отправка/редактирование заказа в сдэк
     * @param msOrder $msOrder
     * @return array
     */
    public function createCdekOrder(msOrder $msOrder, $change = false)
    {
        /** @var integer $currentStatus */
        $currentStatus = (int)$msOrder->get('status');
        /** @var integer $paymentStatus */
        $paymentStatus = (int)$this->modx->getOption('cdekintgrate_payment_status', [], 2);
        /** @var modUser $user */
        $user = $msOrder->getOne('User');
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
            'Phone' => $address->get('phone'),
            'TariffTypeCode' => $tariffID,
        ]);

        $order->setAddress(Common\Address::create([
            'Street' => $address->get('street'),
            'House' => $address->get('building'),
            'Flat' => $address->get('room'),
            'PvzCode' => $address->get('pvz_id'),
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
            $productArray = [
                'WareKey' => $product->get('product_id'),
                'Cost' => $product->get('price'),
                'Payment' => 0, // Оплата за товар при получении (за единицу товара)
                'Weight' => $product->get('weight'),
                'Amount' => $product->get('count'),
                'Comment' => $product->get('name'),
            ];
            if ($this->modx->getOption('cdekintgrate_change_status', [], false)) {
                if ($currentStatus == $paymentStatus) {
                    $productArray['Payment'] = 0;
                } else {
                    $productArray['Payment'] = $product->get('price');
                }
            }
            $package->addItem(new Common\Item($productArray));
        }

        $order->addPackage($package);

        if (!$change) {
            $request = new Requests\DeliveryRequest([
                'Number' => 'Delivery-' . $msOrder->get('num'),
            ]);
        } else {
            $request = Requests\UpdateRequest::create([
                'Number' => $msOrder->get('num'),
            ]);
        }

        $request->addOrder($order);

        if (!$change) {
            $response = $this->client->sendDeliveryRequest($request);
        } else {
            $response = $this->client->sendUpdateRequest($request);
        }

        if ($response->hasErrors()) {
            $error = [];
            foreach ($response->getMessages() as $message) {
                $error[] = $message->getMessage();
            }

            return $this->out(implode(',', $error));
        }

        $orders = $response->getOrders();
        foreach ($orders as $item) {
            $order = $item;
        }
        $address->set('inner_cdek_id', $order->getDispatchNumber());
        if (!$address->save()) {
            return $this->out('Не сохранился адрес');
        }

        return $this->out('Заказ успешно отправлен в сдэк', true, ['number' => $order->getNumber(), 'inner_cdek' => $order->getDispatchNumber()]);
    }

    /**
     * Генерирует pdf от сдэка и сохраняет его
     * @param msOrder $msOrder
     * @return array
     */
    public function createCdekPdf(msOrder $msOrder)
    {
        /** @var msOrderAddress $address */
        $address = $msOrder->getOne('Address');

        if (!$dispatchNumber = $address->get('inner_cdek_id')) {
            return $this->out('Необходимо сначала отправить заказ в сдэк');
        }

        $request = new Requests\PrintReceiptsRequest([
            'CopyCount' => 4,
        ]);


        $request->addOrder(Common\Order::withDispatchNumber($dispatchNumber));

        $response = $this->client->sendPrintReceiptsRequest($request);

        if ($response->hasErrors()) {
            return $this->out($response->getMessages());
        }

        $baseUrl = $this->config['assetsUrl'] . 'pdf/';
        $basePath = MODX_BASE_PATH . $baseUrl;
        $name = str_replace('/', '_', $msOrder->get('num')) . '.pdf';
        file_put_contents($basePath . $name, (string)$response->getBody());

        return $this->out('Успешно', true, ['url' => $baseUrl . $name]);
    }

    public function changeCdekOrder(msOrder $msOrder)
    {
        $this->createCdekOrder($msOrder, true);
    }

    public function getPVZ(msOrder $msOrder)
    {
        /** @var msOrderAddress $address */
        $address = $msOrder->getOne('Address');

        /** @var MsCdek $tariffID */
        $tariffID = $this->modx->getObject('MsCdek', [
            'id_delivery' => $msOrder->get('delivery')
        ]);

        if (!$index = $address->get('index')) {
            return $this->out('Не задан индекс получателя');
        }

        if (!$tariffID) {
            return $this->out('Не найден ID тарифа отправления');
        } else {
            $tariffID = $tariffID->get('id_tarif');
        }

        $request = new Requests\PvzListRequest();
        $request->setCityPostCode($index);
        $request->setType('ALL');
        $request->setCashless(true);
        $request->setCodAllowed(true);
        $request->setDressingRoom(true);

        $response = $this->client->sendPvzListRequest($request);

        if ($response->hasErrors()) {
            return $this->out('Неизвестная ошибка, обратитесь в поддержку');
        }

        $outArray = [];
        foreach ($response as $item) {
            /** @var \CdekSDK\Common\Pvz $item */

            $outArray[] = [
                'id' => $item->Code,
                'name' => $item->Name
            ];
        };
        return $this->out('Успешно', true, $outArray);
    }

    public function out($msg, $success = false, $obj = null)
    {
        if ($this->modx->getOption('cdekintgrate_debug', [], true)) {
            if (!$success) {
                $this->modx->log(1, $msg);
            }
        }
        return [
            'success' => $success,
            'msg' => $msg,
            'obj' => $obj
        ];
    }


}