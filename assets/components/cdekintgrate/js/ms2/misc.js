pvz = function (config) {
    config = config || {};
    console.log(config);
    Ext.applyIf(config, {
        name: 'user',
        fieldLabel: 'Пункт самовывоза',
        hiddenName: config.name,
        displayField: 'name',
        valueField: 'id',
        anchor: '99%',
        fields: ['id', 'name'],
        pageSize: 9999,
        typeAhead: false,
        editable: true,
        allowBlank: true,
        url: cdekIntegrateConfig.connectorUrl,
        baseParams: {
            action: 'mgr/ms2/pvz',
            order_id: config.record.id,
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{name}</b>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    pvz.superclass.constructor.call(this, config);
};
Ext.extend(pvz, MODx.combo.ComboBox);
Ext.reg('cdekIntagrate-combo-pvz', pvz);