/* Nueva Orden: extracción del JavaScript de NewBitacora.php.
 * Scripts clásicos: conserva funciones y estado globales de la vista. */

        /* =========================================================
         * RESUMEN DE LA ORDEN
         * ========================================================= */
        function erpNumero(valor)
        {
            var numero = parseFloat(valor);
            return isNaN(numero) ? 0 : numero;
        }


        function erpValorModelo(item, campo)
        {
            if (!item) return 0;
            if (typeof item.get === "function")
            {
                return item.get(campo);
            }
            return item[campo];
        }


        function actualizarResumenOrden()
        {
            var subtotal = 0;
            var iva = 0;
            var total = 0, retencion = 0;

            if (!detalle || !detalle.ds)
            {
                return;
            }

            /* Totals include every captured row. */
            var data = detalle.ds.data();

            $.each(data, function(index, item)
            {
                subtotal += erpNumero(erpValorModelo(item, "subtotal"));
                iva += erpNumero(erpValorModelo(item, "iva"));
                total += erpNumero(erpValorModelo(item, "total"));
                retencion += erpNumero(erpValorModelo(item,"retencion"));
            });

            $("#lblsubtotal").text(kendo.toString(subtotal, "c2"));
            $("#lbliva").text(kendo.toString(iva, "c2"));
            $("#lbltotal").text(kendo.toString(total, "c2"));
            $("#lblretencion").text(kendo.toString(retencion,"c2"));
        }


        function enfocarError(selector) {
            var $campo=$(selector);
            if (!$campo.length) $campo=$('#ordenErrores');
            $campo[0].scrollIntoView({behavior:'smooth',block:'center',inline:'nearest'});
            if ($campo.is('select') && $campo.data('select2')) $campo.select2('open');
            else if ($campo.is('td')) {
                var grid=$('#grid').data('kendoGrid');
                if (grid) grid.editCell($campo);
            } else $campo.attr('tabindex',$campo.attr('tabindex') || '0').trigger('focus');
        }


        /* =========================================================
         * MODELO DE ORDEN / VALIDACION V3.2
         * ========================================================= */
        window.ERPOrden =
        {
            getCita:function()
            {
                if (!$('#chkcitas').is(':checked')) return null;

                return {
                    activa:true,
                    fecha:$('#txtcitafecha_guardada').val() || $('#txtcitafecha').val(),
                    hora:$('#txtcitahora_guardada').val() || $('#txtcitahora').val(),
                    duracion:erpNumero($('#txtcitaduracion_guardada').val() || $('#txtcitaduracion').val()),
                    observacion:$('#txtcitaobservacion_guardada').val() || $('#txtcitaobservacion').val()
                };
            },

            getUbicacion:function(tipo)
            {
                var contacto = tipo === 'contacto';
                return {
                    direccion: contacto ? $.trim($('#txtcontacto').val()) : $.trim($('#txttermino').val()),
                    lat: erpNumero(contacto ? $('#txtcontactolat').val() : $('#txtterminolat').val()),
                    lon: erpNumero(contacto ? $('#txtcontactolon').val() : $('#txtterminolon').val())
                };
            },

            getDetalle:function()
            {
                var detalleOrden=[];
                if (!detalle || !detalle.ds) return detalleOrden;

                $.each(detalle.ds.data(), function(i,item)
                {
                    var cliente=erpValorModelo(item,'cliente');
                    var servicio=erpValorModelo(item,'servicio');
                    var idcliente=erpValorModelo(item,'idcliente');
                    var idconcepto=erpValorModelo(item,'idconcepto');

                    /* Omit only pristine placeholders; any partially captured row must be validated. */
                    var capturada=idcliente || cliente || idconcepto || servicio ||
                        erpValorModelo(item,'asistencia') || erpValorModelo(item,'expendiente') ||
                        erpValorModelo(item,'idcargo') || erpValorModelo(item,'cargo') || erpValorModelo(item,'docto') ||
                        erpValorModelo(item,'km') || erpValorModelo(item,'kmn') || erpValorModelo(item,'ac') ||
                        Number(erpValorModelo(item,'cantidad')) !== 1 || Number(erpValorModelo(item,'precio')) !== 0;
                    if (!capturada) return;

                    detalleOrden.push({
                        fila:i,
                        idcliente:idcliente,
                        cliente:cliente,
                        idconcepto:idconcepto,
                        servicio:servicio,
                        asistencia:erpValorModelo(item,'asistencia'),
                        expendiente:erpValorModelo(item,'expendiente'),
                        kmn:erpNumero(erpValorModelo(item,'kmn')),
                        km:erpNumero(erpValorModelo(item,'km')),
                        cantidad:erpValorModelo(item,'cantidad'),
                        precio:erpValorModelo(item,'precio'),
                        precioBase:erpValorModelo(item,'precioBase'),
                        docto:erpValorModelo(item,'docto'),
                        iva:erpNumero(erpValorModelo(item,'iva')),
                        subtotal:erpNumero(erpValorModelo(item,'subtotal')),
                        total:erpNumero(erpValorModelo(item,'total')),
                        idcargo:erpValorModelo(item,'idcargo'),
                        cargo:erpValorModelo(item,'cargo'),
                        cargoRequerido:!!erpValorModelo(item,'cargoRequerido'),
                        ac:!!erpValorModelo(item,'ac')
                    });
                });

                return detalleOrden;
            },

            get:function()
            {
                actualizarResumenOrden();

                return {
                    usuario:$('#txtiduser').val(),
                    empresa:$('#txtidemp').val(),
                    sucursal:$('#txtidsuc').val(),
                    clienteSucursal:$('#txtidclisuc').val(),
                    vehiculo:{
                        tipo:$('#cbovehiculo').val(),
                        marca:$('#cbomarca').val(),
                        clase:$('#cbotipo').val(),
                        color:$('#txtcolor').val(),
                        placa:$('#txtplaca').val(),
                        modelo:$('#txtmodelo').val(),
                        serie:$('#txtserie').val(),
                        motor:$('#txtmotor').val() || ''
                    },
                    servicio:{
                        solicita:$('#txtsolicita').val(),
                        asegurado:$('#txtasegurado').val(),
                        contacto:$('#txtcontacto').val(),
                        etiqcont:$('#txtetqcontact').val(),
                        termino:$('#txttermino').val(),
                        etiqterm:$('#txteqtermi').val(), 
                        telefono:$('#txttel1').val(),
                        celular:$('#txtcel').val(),
                        clave:$('#cboclave').val(),
                        servicio:$('#txtserv').val(),
                        operador:$('#cbooperador').val(),
                        grua:$('#cbogrua').val(),
                        promesa:$('#txtpromesa').val(),
                        camino:$('input[name="rndcamino"]:checked').val(),
                        tipoServicio:$('#cbotiposervicio').val(),
                        fecha:$('#txtfecha').val()
                    },
                    opciones:{
                        todosClientes:$('#chktodos').is(':checked'),
                        citas:$('#chkcitas').is(':checked'),
                        local:$('#rndlocal').is(':checked'),
                        foraneo:$('#rndforaneo').is(':checked')
                    },
                    ubicaciones:{
                        contacto:this.getUbicacion('contacto'),
                        termino:this.getUbicacion('termino')
                    },
                    cita:this.getCita(),
                    comentarios:$('#txtcomentarios').val(),
                    observaciones:$('#txtobservaciones').val(),
                    ubicacion:$('#txtubicacion').val(),
                    detalle:this.getDetalle(),
                    totales:{
                        subtotal:erpNumero($('#lblsubtotal').text().replace(/[^0-9.-]/g,'')),
                        iva:erpNumero($('#lbliva').text().replace(/[^0-9.-]/g,'')),
                        total:erpNumero($('#lbltotal').text().replace(/[^0-9.-]/g,''))
                    }
                };
            },

            validar:function(cerrarEditor)
            {
                var grid=$('#grid').data('kendoGrid');
                if (cerrarEditor !== false && grid) {
                    // Kendo 2015 closeCell destroys the editor without committing a pending change.
                    grid.wrapper.find('.k-edit-cell input, .k-edit-cell select, .k-edit-cell textarea').trigger('change');
                    if (grid.editable && typeof grid.editable.end === 'function') grid.editable.end();
                    if (typeof grid.closeCell === 'function') grid.closeCell();
                }
                var errores=[], campos=[];
                var orden=this.get();
                $('.erp-invalid').removeClass('erp-invalid');
                $('[aria-invalid]').removeAttr('aria-invalid');
                function error(selector,mensaje) {
                    errores.push(mensaje); campos.push(selector);
                    var $campo=$(selector).attr('aria-invalid','true').addClass('erp-invalid');
                    $campo.next('.select2-container').addClass('erp-invalid');
                }
                function requerido(valor,selector,mensaje) {
                    if (!valor || !String(valor).trim()) error(selector,mensaje);
                }
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
                    var fila='#grid tbody tr:eq('+item.fila+')';
                    var prefijo='Fila '+(item.fila+1)+': ';
                    function celda(campo,mensaje) {
                        var indice=grid ? grid.columns.map(function(x){return x.field;}).indexOf(campo) : -1;
                        error(indice >= 0 ? fila+' td:eq('+indice+')' : '#grid',prefijo+mensaje);
                    }
                    if (!item.idcliente || Number(item.idcliente) <= 0) celda('cliente','seleccione cliente.');
                    if (!item.idconcepto || Number(item.idconcepto) <= 0) celda('servicio','seleccione concepto/tarifa.');
                    if (item.cantidad === null || item.cantidad === '' || !isFinite(Number(item.cantidad)) || Number(item.cantidad) <= 0) celda('cantidad','la cantidad debe ser mayor a cero.');
                    if (item.precio === null || item.precio === '' || !isFinite(Number(item.precio)) || Number(item.precio) < 0 || Number(item.precio) < Number(item.precioBase || 0)) celda('precio','ingrese un precio válido, al menos el precio de tarifa.');
                    // Configured cargos are checked authoritatively in PHP as well.
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

        };
window.OrdenServicio = {
    initCalculo:function() {

        
        // Override only this screen's calculation; shared ERPGrid remains untouched.
        detalle.calculateRow=function(model) {
            var subtotal=Math.round(Number(model.get('cantidad')) * Number(model.get('precio')) * 100) / 100;
            var iva=Math.round(subtotal * erpTasaIVA * 100) / 100;
            model.set('subtotal',subtotal); model.set('iva',iva);
            var retencion=Math.round(subtotal * (model.get('retencionAplica') ? erpTasaRetencion : 0) * 100)/100;
            model.set('retencion',retencion);
            model.set('total',Math.round((subtotal+iva-retencion)*100)/100);
            this.calculateTotals();
        };
    },
    init:function() {

        $(document).on('input.erpValidacion change.erpValidacion','[aria-required], #rndlocal, #rndforaneo, #chkcitas, #txtpromesa',function(){
            if ($('#ordenErrores').is(':visible')) ERPOrden.validar(false);
        });

        detalle.ds.bind('change',function(e){
            if (e.action === 'itemchange' && $('#ordenErrores').is(':visible')) {
                window.clearTimeout(window.erpValidacionTimer);
                window.erpValidacionTimer=window.setTimeout(function(){ERPOrden.validar(false);},0);
            }
        });

        // Validate, send JSON, and deactivate only after a confirmed successful response.
        $('#btngenerar').off('click.erpOrden').on('click.erpOrden', function(e)
        {
            e.preventDefault();
            if ($(this).data('guardando')) return;
            var resultado=ERPOrden.validar();

            if (!resultado.ok)
            {
                Swal.fire({
                    icon:'warning',
                    title:'Orden incompleta',
                    html:'<div style="text-align:left">' + resultado.errores.map(function(x){return '• '+x;}).join('<br>') + '</div>'
                }).then(function(){enfocarError(resultado.campos[0]);});
                return;
            }

            var $btn=$(this);
            var guardadoOk=false;
            $btn.data('guardando',true).prop('disabled',true).addClass('is-loading');

            OrdenApi.guardarOrden({url:OrdenApi.urls.GuardarOrden,
                type:'POST',
                dataType:'json',
                contentType:'application/json; charset=utf-8',
                data:JSON.stringify(resultado.orden),
                success:function(resp)
                {
                    if (!resp || resp.status !== true)
                    {
                        Swal.fire({icon:'error',title:'No fue posible generar la orden',text:(resp && resp.message) ? resp.message : 'Respuesta inválida del servidor.'});
                        return;
                    }

                    var d=resp.data || {};
                    guardadoOk=true;
                    Swal.fire({
                        icon:'success',
                        title:'Orden generada',
                        html:'<div style=\"font-size:13px\">' +
                             '<b>Folio:</b> ' + (d.folio || '') + '<br>' +
                             '<b>BIS:</b> ' + (d.pkvchid_bis || '') + '<br>' +
                             '<b>Servicio:</b> ' + (d.intno_serv || 1) +
                             '</div>',
                        confirmButtonText:'Aceptar'
                    }).then(function(){
                        frmServicios_DesactivarFormulario();
                    });
                },
                error:function(xhr)
                {
                    var mensaje='Error al comunicarse con el servidor.';
                    try { var r=JSON.parse(xhr.responseText); if(r.message) mensaje=r.message; } catch(ex){}
                    Swal.fire({icon:'error',title:'Error al guardar',text:mensaje});
                },
                complete:function()
                {
                    if (!guardadoOk) $btn.prop('disabled',false);
                    $btn.data('guardando',false).removeClass('is-loading');
                }
            });
        });
    }
};

$(function(){
    OrdenUI.init();
});
