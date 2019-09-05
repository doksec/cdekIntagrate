<?php

/**
 * The home manager controller for cdekIntgrate.
 *
 */
class cdekIntgrateHomeManagerController extends modExtraManagerController
{
    /** @var cdekIntgrate $cdekIntgrate */
    public $cdekIntgrate;


    /**
     *
     */
    public function initialize()
    {
        $this->cdekIntgrate = $this->modx->getService('cdekIntgrate', 'cdekIntgrate', MODX_CORE_PATH . 'components/cdekintgrate/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['cdekintgrate:manager','cdekintgrate:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('cdekintgrate');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->cdekIntgrate->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/cdekintgrate.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/widgets/items/grid.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/widgets/items/windows.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->cdekIntgrate->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        cdekIntgrate.config = ' . json_encode($this->cdekIntgrate->config) . ';
        cdekIntgrate.config.connector_url = "' . $this->cdekIntgrate->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "cdekintgrate-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="cdekintgrate-panel-home-div"></div>';

        return '';
    }
}