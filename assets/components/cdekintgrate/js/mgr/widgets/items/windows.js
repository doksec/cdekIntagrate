cdekIntgrate.window.CreateItem = function (config) {
    config = config || {}
    config.url = cdekIntgrate.config.connector_url

    Ext.applyIf(config, {
        title: _('cdekintgrate_color_create'),
        width: 600,
        cls: 'cdekintgrate_windows',
        baseParams: {
            action: 'mgr/item/create',
            resource_id: config.resource_id
        }
    })
    cdekIntgrate.window.CreateItem.superclass.constructor.call(this, config)
}
Ext.extend(cdekIntgrate.window.CreateItem, cdekIntgrate.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('cdekintgrate_item_name'),
                name: 'name',
                id: config.id + '-name',
                anchor: '99%',
                allowBlank: false,
            }, {
                xtype: 'textarea',
                fieldLabel: _('cdekintgrate_item_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('cdekintgrate_item_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('cdekintgrate-item-window-create', cdekIntgrate.window.CreateItem)

cdekIntgrate.window.UpdateItem = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('cdekintgrate_item_update'),
        baseParams: {
            action: 'mgr/item/update',
            resource_id: config.resource_id
        },
    })
    cdekIntgrate.window.UpdateItem.superclass.constructor.call(this, config)

}
Ext.extend(cdekIntgrate.window.UpdateItem, cdekIntgrate.window.CreateItem)
Ext.reg('cdekintgrate-item-window-update', cdekIntgrate.window.UpdateItem)