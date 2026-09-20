/* Transporte AJAX de Nueva Orden. Conserva opciones, parámetros y callbacks. */
window.OrdenApi = {
    urls: {
        GetAllMarca:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetAllMarca',
        GetAllTipo:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetAllTipo',
        GetAllGrua:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetAllGrua',
        GetCliente_Suc_RFC:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetCliente_Suc_RFC',
        GetTarifaAll:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetTarifaAll',
        GetTipoCargo:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GetTipoCargo',
        GuardarOrden:window.ORDEN_CONFIG.baseUrl + '/Bitacoras/GuardarOrden',
        BuscarUbicaciones:'https://nominatim.openstreetmap.org/search'
    },
    getMarcas:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetAllMarca}));
    },
    getTipos:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetAllTipo}));
    },
    getGruas:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetAllGrua}));
    },
    getClientes:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetCliente_Suc_RFC}));
    },
    getTarifas:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetTarifaAll}));
    },
    getTipoCargo:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GetTipoCargo}));
    },
    guardarOrden:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.GuardarOrden}));
    },
    buscarUbicaciones:function(options) {
        return $.ajax($.extend({}, options, {url:OrdenApi.urls.BuscarUbicaciones}));
    }
};
