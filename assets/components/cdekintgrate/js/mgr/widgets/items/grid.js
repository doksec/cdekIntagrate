cdekIntgrate.grid.Items = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'cdekintgrate-grid-items';
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/item/getlist',
        },
        stateful: true,
        stateId: config.id,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return !rec.data.active
                  ? 'cdekintgrate-grid-row-disabled'
                  : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    cdekIntgrate.grid.Items.superclass.constructor.call(this, config);
};
Ext.extend(cdekIntgrate.grid.Items, cdekIntgrate.grid.Default, {

    getFields: function () {
        return [
            'id', 'name', 'description', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('cdekintgrate_item_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('cdekintgrate_item_name'), dataIndex: 'name', sortable: true, width: 200},
            {header: _('cdekintgrate_item_description'), dataIndex: 'description', sortable: false, width: 250},
            {header: _('cdekintgrate_item_active'), dataIndex: 'active', width: 75, renderer: cdekIntgrate.utils.renderBoolean},
            {
                header: _('cdekintgrate_grid_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: cdekIntgrate.utils.renderActions
            }
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('cdekintgrate_item_create'),
            handler: this.createItem,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateItem(grid, e, row);
            },
        };
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'cdekintgrate-item-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },

    updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/item/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'cdekintgrate-item-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
              ? _('cdekintgrate_items_remove')
              : _('cdekintgrate_item_remove'),
            text: ids.length > 1
              ? _('cdekintgrate_items_remove_confirm')
              : _('cdekintgrate_item_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/item/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    disableItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/item/disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    enableItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/item/enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },
});
Ext.reg('cdekintgrate-grid-items', cdekIntgrate.grid.Items);