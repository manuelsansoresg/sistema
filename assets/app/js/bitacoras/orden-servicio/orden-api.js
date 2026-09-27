(function (window, $) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};
    var baseUrl = window.ORDEN_CONFIG.baseUrl;
    function request(options) { return $.ajax(options); }

    /** Comunicación exclusiva con los endpoints de órdenes y catálogos. */
    app.Api = {
        urls: {
            obtenerOrdenes: baseUrl + '/Bitacoras/GetOrdenes', obtenerOrden: baseUrl + '/Bitacoras/GetOrden',
            guardarOrden: baseUrl + '/Bitacoras/GuardarOrden', actualizarOrden: baseUrl + '/Bitacoras/ActualizarOrden',
            cargarMarcas: baseUrl + '/Bitacoras/GetAllMarca', cargarTipos: baseUrl + '/Bitacoras/GetAllTipo',
            cargarGruas: baseUrl + '/Bitacoras/GetAllGrua', cargarClientes: baseUrl + '/Bitacoras/GetCliente_Suc_RFC',
            cargarTarifas: baseUrl + '/Bitacoras/GetTarifaAll', cargarCargos: baseUrl + '/Bitacoras/GetTipoCargo'
        },
        obtenerOrdenes: function (parametros) { return request({url:this.urls.obtenerOrdenes, dataType:'json', data:parametros || {}}); },
        obtenerOrden: function (bis) { return request({url:this.urls.obtenerOrden + '/' + encodeURIComponent(bis), dataType:'json'}); },
        guardarOrden: function (orden) { return request({url:this.urls.guardarOrden, type:'POST', dataType:'json', contentType:'application/json; charset=utf-8', data:JSON.stringify(orden)}); },
        actualizarOrden: function (orden) { return request({url:this.urls.actualizarOrden, type:'POST', dataType:'json', contentType:'application/json; charset=utf-8', data:JSON.stringify(orden)}); },
        cargarMarcas: function (clasificacion, buscar) { return request({url:this.urls.cargarMarcas, type:'POST', dataType:'json', data:{qopcion:1, qclas:clasificacion, buscar:buscar || ''}}); },
        cargarTipos: function (marca, buscar) { return request({url:this.urls.cargarTipos, type:'POST', dataType:'json', data:{qopcion:1, qmarca:marca, buscar:buscar || ''}}); },
        cargarGruas: function (operador) { return request({url:this.urls.cargarGruas, type:'POST', dataType:'json', data:{qopcion:1, qope:operador, bis:window.ORDEN_CONFIG.bis || ''}}); },
        cargarClientes: function (todos) { return request({url:this.urls.cargarClientes, type:'POST', dataType:'json', data:{qtodos:!!todos}}); },
        cargarTarifas: function (cliente, localForaneo) { return request({url:this.urls.cargarTarifas, type:'POST', dataType:'json', data:{qcve:cliente, qlf:localForaneo}}); },
        cargarCargos: function (cliente) { return request({url:this.urls.cargarCargos, type:'POST', dataType:'json', data:{qcve:cliente}}); }
    };
}(window, jQuery));
