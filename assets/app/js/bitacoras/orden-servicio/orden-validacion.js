(function (window) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};
    window.enfocarError = function (selector) {
        var $campo=$(selector);
        if (!$campo.length) $campo=$('#ordenErrores');
        $campo[0].scrollIntoView({behavior:'smooth', block:'center'});
        if ($campo.is('select') && $campo.data('select2')) $campo.select2('open');
        else if ($campo.is('td')) { var grid=$('#grid').data('kendoGrid'); if (grid) grid.editCell($campo); }
        else $campo.attr('tabindex', $campo.attr('tabindex') || '0').trigger('focus');
    };

    function validarFormulario(cerrarEditor) {
        var grid=$('#grid').data('kendoGrid');
        if (cerrarEditor !== false && grid) {
            grid.wrapper.find('.k-edit-cell input, .k-edit-cell select, .k-edit-cell textarea').trigger('change');
            if (grid.editable && typeof grid.editable.end === 'function') grid.editable.end();
            if (typeof grid.closeCell === 'function') grid.closeCell();
        }
        var errores=[], campos=[], orden=app.Form.obtenerOrden();
        app.Validacion.limpiarErrores();
        function error(selector,mensaje) {
            errores.push(mensaje); campos.push(selector);
            var $campo=$(selector).attr('aria-invalid','true').addClass('erp-invalid');
            $campo.next('.select2-container').addClass('erp-invalid');
        }
        function requerido(valor,selector,mensaje) { if (!valor || !String(valor).trim()) error(selector,mensaje); }
        requerido(orden.vehiculo.tipo,'#cbovehiculo','Seleccione el tipo de vehículo.');
        requerido(orden.vehiculo.marca,'#cbomarca','Seleccione la marca.');
        requerido(orden.vehiculo.clase,'#cbotipo','Seleccione el tipo del catálogo.');
        requerido(orden.servicio.clave,'#cboclave','Seleccione la clave del servicio.');
        requerido(orden.servicio.contacto,'#txtcontacto','Complete el lugar de contacto.');
        requerido(orden.servicio.termino,'#txttermino','Complete el lugar de entrega.');
        if (orden.opciones.local === orden.opciones.foraneo) error('#rndlocal','Seleccione Local o Foráneo.');
        requerido(orden.servicio.operador,'#cbooperador','Seleccione el operador.');
        requerido(orden.servicio.grua,'#cbogrua','Seleccione la grúa.');
        if (orden.servicio.promesa && (!/^\d+$/.test(orden.servicio.promesa) || Number(orden.servicio.promesa) <= 0)) error('#txtpromesa','Ingrese el tiempo promesa en minutos enteros mayores a cero.');
        if (!orden.detalle.length) error('#grid','Agregue al menos un concepto.');
        orden.detalle.forEach(function(item) {
            var fila='#grid tbody tr:eq('+item.fila+')', prefijo='Fila '+(item.fila+1)+': ';
            function celda(campo,mensaje) {
                var indice=grid ? grid.columns.map(function(columna){return columna.field;}).indexOf(campo) : -1;
                error(indice >= 0 ? fila+' td:eq('+indice+')' : '#grid', prefijo+mensaje);
            }
            if (!item.idcliente || Number(item.idcliente) <= 0) celda('cliente','seleccione cliente.');
            if (!item.idconcepto || Number(item.idconcepto) <= 0) celda('servicio','seleccione concepto/tarifa.');
            if (!isFinite(Number(item.cantidad)) || Number(item.cantidad) <= 0) celda('cantidad','la cantidad debe ser mayor a cero.');
            if (!isFinite(Number(item.precio)) || Number(item.precio) < Number(item.precioBase || 0)) celda('precio','ingrese un precio válido, al menos el precio de tarifa.');
            if (!item.idcargo || Number(item.idcargo) <= 0) celda('cargo','seleccione tipo de cargo.');
        });
        if (orden.opciones.citas) {
            var cita=orden.cita;
            if (!cita || !/^\d{4}-\d{2}-\d{2}$/.test(cita.fecha || '') || !/^([01]\d|2[0-3]):[0-5]\d$/.test(cita.hora || '') || !isFinite(cita.duracion) || cita.duracion <= 0)
                error('#chkcitas','Complete una fecha, hora y duración válidas para la cita.');
        }
        $('#ordenErrores').prop('hidden',errores.length===0).text(errores.join(' '));
        return {ok:errores.length===0, errores:errores, campos:campos, orden:orden};
    }
    app.Validacion = {
        validarFormulario:validarFormulario,
        limpiarErrores:function () {
            $('#ordenErrores').prop('hidden', true).empty();
            $('.erp-invalid').removeClass('erp-invalid');
            $('[aria-invalid]').removeAttr('aria-invalid');
        },
        mostrarErrores:function (resultado) {
            Swal.fire({icon:'warning', title:'Orden incompleta',
                html:'<div style="text-align:left">' + resultado.errores.map(function (error) {
                    return '• ' + $('<div>').text(error).html();
                }).join('<br>') + '</div>'}).then(function () { enfocarError(resultado.campos[0]); });
        }
    };
}(window));
