(function (window) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};

    window.erpNumero = function (valor) {
        var numero = parseFloat(valor);
        return isNaN(numero) ? 0 : numero;
    };
    window.erpValorModelo = function (item, campo) {
        return item && typeof item.get === 'function' ? item.get(campo) : (item ? item[campo] : null);
    };
    window.actualizarResumenOrden = function () {
        var totales = {subtotal:0, iva:0, retencion:0, total:0};
        if (!window.detalle || !detalle.ds) return;
        detalle.ds.data().forEach(function (fila) {
            var subtotal=erpNumero(erpValorModelo(fila, 'subtotal'));
            var iva=erpNumero(erpValorModelo(fila, 'iva'));
            var retencion=erpNumero(erpValorModelo(fila, 'retencion'));

            totales.subtotal += subtotal;
            totales.iva += iva;
            totales.retencion += retencion;
            /* El total general es la suma de los importes calculados de cada concepto. */
            totales.total += subtotal + iva - retencion;
        });
        $('#lblsubtotal').text(kendo.toString(totales.subtotal, 'c2'));
        $('#lbliva').text(kendo.toString(totales.iva, 'c2'));
        $('#lblretencion').text(kendo.toString(totales.retencion, 'c2'));
        $('#lbltotal').text(kendo.toString(totales.total, 'c2'));
    };

    /** Fachada única sobre el ERPGrid compartido por alta y edición. */
    app.Grid = {
        crearColumnas:function (editores) {
            var centro={class:'erp-grid-center'}, texto={class:'erp-grid-text'}, dinero={class:'erp-grid-money erp-grid-readonly'};
            return [
                {field:'cliente',title:'Cliente',editor:editores.cliente,width:145,headerAttributes:centro,attributes:texto},
                {field:'servicio',title:'Servicio',editor:editores.concepto,width:180,headerAttributes:centro,attributes:texto},
                {field:'asistencia',title:'Asistencia',width:80,headerAttributes:centro,attributes:centro},
                {field:'expendiente',title:'Exp.',width:65,headerAttributes:centro,attributes:centro},
                {field:'kmn',title:'KMN',width:48,headerAttributes:centro,attributes:centro},
                {field:'km',title:'KM',width:48,headerAttributes:centro,attributes:centro},
                {field:'cantidad',title:'Cant.',width:55,headerAttributes:centro,attributes:centro},
                {field:'precio',title:'Precio',width:78,format:'{0:c}',editor:editores.precio,headerAttributes:centro,attributes:{class:'erp-grid-money'}},
                {field:'iva',title:'IVA',width:75,format:'{0:c}',editable:false,headerAttributes:centro,attributes:dinero},
                {field:'subtotal',title:'Subtotal',width:82,format:'{0:c}',editable:false,headerAttributes:centro,attributes:dinero},
                {field:'total',title:'Total',width:82,format:'{0:c}',editable:false,headerAttributes:centro,attributes:{class:'erp-grid-money erp-grid-total erp-grid-readonly'}},
                {field:'cargo',title:'Cargo',editor:editores.cargo,width:72,headerAttributes:centro,attributes:centro},
                {field:'ac',title:'AC',width:38,editor:editores.check,editable:false,headerAttributes:centro,attributes:centro,
                    template:"# if(data.ac){ #<span class='erp-check ok'></span># } else { #<span class='erp-check no'></span># } #"},
                {title:'Acciones',width:92,editable:false,sortable:false,
                    headerAttributes:{class:'erp-grid-center erp-grid-actions-header'},attributes:{class:'erp-grid-center erp-grid-actions-cell'},
                    template:"<div class='erp-row-actions'><button type='button' class='erp-row-action erp-row-add' title='Agregar fila' aria-label='Agregar fila'><i class='fa fa-plus' aria-hidden='true'></i></button><button type='button' class='erp-row-action erp-row-remove' title='Quitar fila' aria-label='Quitar fila'><i class='fa fa-trash' aria-hidden='true'></i></button></div>"}
            ];
        },
        cargarDetalle: function (filas) {
            if (!window.detalle || !detalle.ds) return;
            while (detalle.ds.data().length) detalle.ds.remove(detalle.ds.data()[0]);
            (filas || []).forEach(function (fila, indice) {
                detalle.ds.add($.extend({idlinea:Date.now() + indice}, fila, {
                    cantidad:Number(fila.cantidad || 0), precio:Number(fila.precio || 0),
                    precioBase:Number(fila.precioBase || 0), subtotal:Number(fila.subtotal || 0),
                    iva:Number(fila.iva || 0), retencion:Number(fila.retencion || 0),
                    total:Number(fila.total || 0), ac:Number(fila.ac) === 1,
                    retencionAplica:Number(fila.retencionAplica) === 1
                }));
            });
            detalle.grid.refresh();
            if (typeof enlazarResumenERPGrid === 'function') enlazarResumenERPGrid();
            if (typeof actualizarResumenOrden === 'function') actualizarResumenOrden();
        },
        obtenerDetalle: function () {
            return app.Form ? app.Form.obtenerDetalle() : [];
        },
        recalcular: function () {
            if (!window.detalle || !detalle.ds) return;
            detalle.ds.data().forEach(function (fila) { detalle.calculateRow(fila); });
            if (typeof actualizarResumenOrden === 'function') actualizarResumenOrden();
        },
        limpiar: function () { this.cargarDetalle([]); }
    };
}(window));
