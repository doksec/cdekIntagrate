cdekIntgrate.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'cdekintgrate-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('cdekintgrate') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('cdekintgrate_items'),
                layout: 'anchor',
                items: [{
                    html: _('cdekintgrate_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'cdekintgrate-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    cdekIntgrate.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(cdekIntgrate.panel.Home, MODx.Panel);
Ext.reg('cdekintgrate-panel-home', cdekIntgrate.panel.Home);
