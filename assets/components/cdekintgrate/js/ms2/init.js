console.log('cdek init');


Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function () {
    let thisContainer = this.fields.items[0].items[3].items,
        self = this,
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
                                Ext.Ajax.request({ //делаем ajax запрос на наш контроллер
                                    url: '/assets/components/cdekintgrate/action.php',
                                    success: function (resp) {
                                        const response = JSON.parse(resp.responseText);
                                        if (response.success) {
                                            Ext.Msg.alert('Успешно', response.msg);
                                        } else {
                                            Ext.Msg.alert('Ошибка', response.msg + ' (подробнее в консоли)');
                                        }

                                        console.log(response);
                                    },
                                    failure: function (resp) {
                                        Ext.Msg.alert('Внимание', 'Ошибка ajax запроса');
                                    },
                                    params: {
                                        action: 'send',
                                        order_id: self.record.id,
                                    }
                                });
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
                        }
                    ]
                }
            ]
        };
    thisContainer.push(dataPush);
    console.log(this.record);

});