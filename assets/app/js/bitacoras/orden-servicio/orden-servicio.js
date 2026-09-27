(function (window, $) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};

    function mostrarExito(respuesta) {
        var datos=respuesta.data || {};
        return Swal.fire({icon:'success', title:window.ORDEN_CONFIG.modo === 'editar' ? 'Orden actualizada' : 'Orden generada',
            html:'<div style="font-size:13px"><b>Folio:</b> ' + (datos.folio || '') + '<br>' +
                '<b>BIS:</b> ' + (datos.pkvchid_bis || '') + '<br><b>Servicio:</b> ' + (datos.intno_serv || 1) + '</div>'});
    }

    function guardarFormulario() {
        var resultado=app.Validacion.validarFormulario();
        if (!resultado.ok) { app.Validacion.mostrarErrores(resultado); return; }
        var $boton=$('#btngenerar');
        if ($boton.data('guardando')) return;
        $boton.data('guardando',true).prop('disabled',true).addClass('is-loading');

        var peticion=window.ORDEN_CONFIG.modo === 'editar'
            ? app.Api.actualizarOrden(resultado.orden)
            : app.Api.guardarOrden(resultado.orden);
        peticion.done(function (respuesta) {
            if (!respuesta || respuesta.status !== true) {
                Swal.fire({icon:'error', title:'No fue posible guardar', text:respuesta && respuesta.message ? respuesta.message : 'Respuesta inválida del servidor.'});
                return;
            }
            mostrarExito(respuesta).then(function () {
                if (window.ORDEN_CONFIG.modo === 'nuevo') frmServicios_DesactivarFormulario();
            });
        }).fail(function (xhr) {
            app.UI.mostrarErrorAjax(xhr, 'No fue posible guardar la orden. Ningún cambio fue registrado.');
        }).always(function () {
            $boton.data('guardando',false).prop('disabled',false).removeClass('is-loading');
        });
    }

    function cargarOrdenExistente() {
        $('#btngenerar').prop('disabled',true);
        app.Api.obtenerOrden(window.ORDEN_CONFIG.bis).done(function (respuesta) {
            if (!respuesta || respuesta.status !== true) {
                Swal.fire({icon:'error', title:'No fue posible abrir la orden', text:respuesta && respuesta.message ? respuesta.message : 'Respuesta inválida del servidor.'});
                return;
            }
            app.Form.cargarOrden(respuesta.data).done(function () {
                frmServicios_ActivarFormulario();
                $('#btnnuevo').hide();
                $('#btngenerar').html('<i class="fa fa-save"></i> Actualizar');
            }).fail(function (xhr) {
                app.UI.mostrarErrorAjax(xhr, 'No fue posible cargar los catálogos dependientes de la orden.');
            });
        }).fail(function (xhr) {
            app.UI.mostrarErrorAjax(xhr, 'La orden no existe o no pertenece a esta sucursal.');
        });
    }

    app.Servicio = {
        initCalculo:function () {
            detalle.calculateRow=function(model) {
                var subtotal=Math.round(Number(model.get('cantidad')) * Number(model.get('precio')) * 100) / 100;
                var iva=Math.round(subtotal * erpTasaIVA * 100) / 100;
                var retencion=Math.round(subtotal * (model.get('retencionAplica') ? erpTasaRetencion : 0) * 100) / 100;
                model.set('subtotal',subtotal); model.set('iva',iva); model.set('retencion',retencion);
                model.set('total',Math.round((subtotal + iva - retencion) * 100) / 100);
                this.calculateTotals();
            };
        },
        init:function () {
            $(document).on('input.erpValidacion change.erpValidacion','[aria-required], #rndlocal, #rndforaneo, #chkcitas, #txtpromesa',function () {
                if ($('#ordenErrores').is(':visible')) app.Validacion.validarFormulario(false);
            });
            detalle.ds.bind('change',function (evento) {
                if (evento.action === 'itemchange' && $('#ordenErrores').is(':visible')) app.Validacion.validarFormulario(false);
            });
            $('#btngenerar').off('click.erpOrden').on('click.erpOrden',function (evento) {
                evento.preventDefault(); guardarFormulario();
            });
            if (window.ORDEN_CONFIG.modo === 'editar') cargarOrdenExistente();
        }
    };

    $(function () { app.UI.init(); });
}(window, jQuery));
