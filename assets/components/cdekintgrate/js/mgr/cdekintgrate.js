var cdekIntgrate = function (config) {
    config = config || {};
    cdekIntgrate.superclass.constructor.call(this, config);
};
Ext.extend(cdekIntgrate, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('cdekintgrate', cdekIntgrate);

cdekIntgrate = new cdekIntgrate();