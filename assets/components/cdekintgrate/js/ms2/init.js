console.log('cdek init');


Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function () {
    let thisContainer = this.fields.items[0].items[3].items,
        dataPush = {
            columnWidth: .98,
            layout: 'column',
            cls: 'cdek-column',
            style: 'padding:15pxd;text-align:center;',
            border: false,
            items: [
                {
                    columnWidth: .48,
                    layout: 'column',
                    border: false,
                    items: [
                        {
                            xtype: 'button',
                            name: 'no_rec',
                            fieldLabel: '',
                            anchor: '100%',
                            text: '<i class="icon icon-paper-plane"></i> Отправить в сдэк',
                            handler: function () {
                                console.log('test');
                            }
                        }, {
                            xtype: 'button',
                            name: 'no_rec1',
                            fieldLabel: '',
                            anchor: '100%',
                            text: '<i class="icon icon-file"></i> Накладная',
                            handler: function () {
                                console.log('test');
                            }
                        }
                    ]
                },
                {
                    columnWidth: .48,
                    layout: 'form',
                    items: [
                        {
                            xtype: 'displayfield',
                            name: 'createdon',
                            fieldLabel: 'ID в СДЭК',
                            anchor: '100%'
                        },
                        {
                            xtype: 'displayfield',
                            name: 'addr_cdek_id',
                            fieldLabel: 'ID города',
                            anchor: '100%'
                        },
                        {
                            xtype: 'displayfield',
                            name: 'createdon',
                            fieldLabel: 'ID тарифа',
                            anchor: '100%'
                        },
                    ]
                }
            ]
        };
    thisContainer.push(dataPush);
    console.log(this.record);

});