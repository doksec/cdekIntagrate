console.log('cdek init');


Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function () {
    let thisContainer = this.fields.items[0].items[3].items,
        self = this,
        dataPush = {
            columnWidth: .98,
            layout: 'column',
            cls: 'cdek-column',
            border: false,
            items: [
                {
                    columnWidth: .48,
                    layout: 'column',
                    border: false,
                    items: [
                        {
                            xtype: 'button',
                            fieldLabel: '',
                            anchor: '100%',
                            text: '<i class="icon icon-paper-plane"></i> Отправить в сдэк',
                            handler: function () {
                                let mask = new Ext.LoadMask(self.bwrap.id, {msg: "Ожидаем ответа от СДЭК"});
                                Ext.Ajax.on('beforerequest', function () {
                                    mask.show();
                                }, this);
                                Ext.Ajax.on('requestcomplete', function () {
                                    mask.hide();
                                }, this);
                                Ext.Ajax.on('requestexception', function () {
                                    mask.hide();
                                }, this);
                                Ext.Ajax.request({
                                    url: '/assets/components/cdekintgrate/action.php',
                                    success: function (resp) {
                                        const response = JSON.parse(resp.responseText);
                                        if (response.success) {
                                            Ext.Msg.alert('Успешно', response.message);
                                        } else {
                                            Ext.Msg.alert('Ошибка', response.message);
                                        }
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
                            fieldLabel: '',
                            anchor: '100%',
                            text: '<i class="icon icon-file"></i> Накладная',
                            handler: function () {
                                let mask = new Ext.LoadMask(self.bwrap.id, {msg: "Генерируем PDF"});
                                Ext.Ajax.on('beforerequest', function () {
                                    mask.show();
                                }, this);
                                Ext.Ajax.on('requestcomplete', function () {
                                    mask.hide();
                                }, this);
                                Ext.Ajax.on('requestexception', function () {
                                    mask.hide();
                                }, this);
                                Ext.Ajax.request({
                                    url: '/assets/components/cdekintgrate/action.php',
                                    success: function (resp) {
                                        const response = JSON.parse(resp.responseText);
                                        if (response.success) {
                                            Ext.Msg.alert('Успешно', response.message);
                                            window.location.href = response.object.url;
                                        } else {
                                            Ext.Msg.alert('Ошибка', response.message);
                                        }

                                    },
                                    failure: function (resp) {
                                        Ext.Msg.alert('Внимание', 'Ошибка ajax запроса');
                                    },
                                    params: {
                                        action: 'pdf',
                                        order_id: self.record.id,
                                    }
                                });
                            }
                        }
                    ]
                },
                {
                    columnWidth: .48,
                    layout: 'form',
                    items: [
                        {
                            xtype: 'textfield',
                            name: 'addr_pvz_id',
                            fieldLabel: 'Код пункта самовывоза',
                            anchor: '100%'
                        },
                        {
                            xtype: 'displayfield',
                            name: 'addr_inner_cdek_id',
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