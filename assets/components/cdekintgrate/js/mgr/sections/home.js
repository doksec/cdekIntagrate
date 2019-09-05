cdekIntgrate.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'cdekintgrate-panel-home',
            renderTo: 'cdekintgrate-panel-home-div'
        }]
    });
    cdekIntgrate.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(cdekIntgrate.page.Home, MODx.Component);
Ext.reg('cdekintgrate-page-home', cdekIntgrate.page.Home);