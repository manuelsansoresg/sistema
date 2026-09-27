(function (window, $) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};

    function seleccionar($select, id, texto) {
        if (!$select.find('option[value="' + id + '"]').length) {
            $select.append(new Option(texto || id, id, true, true));
        }
        $select.val(String(id)).trigger('change.select2');
    }

    function cargarVehiculo(vehiculo) {
        seleccionar($('#cbovehiculo'), vehiculo.tipo, vehiculo.tipoTexto);
        return app.Api.cargarMarcas(vehiculo.tipo).then(function (marcas) {
            $('#cbomarca').empty();
            (marcas || []).forEach(function (marca) { $('#cbomarca').append(new Option(marca.text, marca.id)); });
            seleccionar($('#cbomarca'), vehiculo.marca, vehiculo.marcaTexto);
            return app.Api.cargarTipos(vehiculo.marca);
        }).then(function (tipos) {
            $('#cbotipo').empty();
            (tipos || []).forEach(function (tipo) { $('#cbotipo').append(new Option(tipo.text, tipo.id)); });
            seleccionar($('#cbotipo'), vehiculo.clase, vehiculo.claseTexto);
        });
    }

    function cargarOperadorYGrua(servicio) {
        seleccionar($('#cbooperador'), servicio.operador, servicio.operadorTexto);
        return app.Api.cargarGruas(servicio.operador).then(function (gruas) {
            $('#cbogrua').empty();
            (gruas || []).forEach(function (grua) { $('#cbogrua').append(new Option(grua.text, grua.id)); });
            seleccionar($('#cbogrua'), servicio.grua, servicio.gruaTexto);
        });
    }

    function cargarCita(cita) {
        var activa = !!cita;
        $('#chkcitas').prop('checked', activa);
        if (!activa) { $('#citaConfirmada').hide(); return; }
        $('#txtcitafecha, #txtcitafecha_guardada').val(cita.fecha || '');
        $('#txtcitahora, #txtcitahora_guardada').val(cita.hora || '');
        $('#txtcitaduracion, #txtcitaduracion_guardada').val(cita.duracion || 30);
        $('#txtcitaobservacion, #txtcitaobservacion_guardada').val(cita.observacion || '');
        $('#citaConfirmadaTexto').text((cita.fecha || '') + ' ' + (cita.hora || ''));
        $('#citaConfirmada').show();
    }

    function obtenerUbicacion(tipo) {
        var contacto = tipo === 'contacto';
        return {
            direccion:$.trim($(contacto ? '#txtcontacto' : '#txttermino').val()),
            lat:erpNumero($(contacto ? '#txtcontactolat' : '#txtterminolat').val()),
            lon:erpNumero($(contacto ? '#txtcontactolon' : '#txtterminolon').val())
        };
    }

    function obtenerDetalle() {
        var filas = [];
        if (!window.detalle || !detalle.ds) return filas;
        detalle.ds.data().forEach(function (item, indice) {
            var idcliente=erpValorModelo(item,'idcliente'), cliente=erpValorModelo(item,'cliente');
            var idconcepto=erpValorModelo(item,'idconcepto'), servicio=erpValorModelo(item,'servicio');
            var capturada=idcliente || cliente || idconcepto || servicio || erpValorModelo(item,'asistencia') ||
                erpValorModelo(item,'expendiente') || erpValorModelo(item,'idcargo') || erpValorModelo(item,'cargo') ||
                Number(erpValorModelo(item,'cantidad')) !== 1 || Number(erpValorModelo(item,'precio')) !== 0;
            if (!capturada) return;
            filas.push({fila:indice, idcliente:idcliente, cliente:cliente, idconcepto:idconcepto, servicio:servicio,
                asistencia:erpValorModelo(item,'asistencia'), expendiente:erpValorModelo(item,'expendiente'),
                kmn:erpNumero(erpValorModelo(item,'kmn')), km:erpNumero(erpValorModelo(item,'km')),
                cantidad:erpValorModelo(item,'cantidad'), precio:erpValorModelo(item,'precio'),
                precioBase:erpValorModelo(item,'precioBase'), docto:erpValorModelo(item,'docto'),
                iva:erpNumero(erpValorModelo(item,'iva')), subtotal:erpNumero(erpValorModelo(item,'subtotal')),
                total:erpNumero(erpValorModelo(item,'total')), idcargo:erpValorModelo(item,'idcargo'),
                cargo:erpValorModelo(item,'cargo'), cargoRequerido:!!erpValorModelo(item,'cargoRequerido'),
                ac:!!erpValorModelo(item,'ac')});
        });
        return filas;
    }

    function obtenerCita() {
        if (!$('#chkcitas').is(':checked')) return null;
        return {activa:true, fecha:$('#txtcitafecha_guardada').val() || $('#txtcitafecha').val(),
            hora:$('#txtcitahora_guardada').val() || $('#txtcitahora').val(),
            duracion:erpNumero($('#txtcitaduracion_guardada').val() || $('#txtcitaduracion').val()),
            observacion:$('#txtcitaobservacion_guardada').val() || $('#txtcitaobservacion').val()};
    }

    function obtenerOrden() {
        actualizarResumenOrden();
        var orden = {
            usuario:$('#txtiduser').val(), empresa:$('#txtidemp').val(), sucursal:$('#txtidsuc').val(),
            vehiculo:{tipo:$('#cbovehiculo').val(), marca:$('#cbomarca').val(), clase:$('#cbotipo').val(),
                color:$('#txtcolor').val(), placa:$('#txtplaca').val(), modelo:$('#txtmodelo').val(),
                serie:$('#txtserie').val(), motor:$('#txtmotor').val() || ''},
            servicio:{solicita:$('#txtsolicita').val(), asegurado:$('#txtasegurado').val(),
                contacto:$('#txtcontacto').val(), etiqcont:$('#txtetqcontact').val(), termino:$('#txttermino').val(),
                etiqterm:$('#txteqtermi').val(), telefono:$('#txttel1').val(), celular:$('#txtcel').val(),
                clave:$('#cboclave').val(), servicio:$('#txtserv').val(), operador:$('#cbooperador').val(),
                grua:$('#cbogrua').val(), promesa:$('#txtpromesa').val(),
                camino:$('input[name="rndcamino"]:checked').val(), tipoServicio:$('#cbotiposervicio').val(), fecha:$('#txtfecha').val()},
            opciones:{todosClientes:$('#chktodos').is(':checked'), citas:$('#chkcitas').is(':checked'),
                local:$('#rndlocal').is(':checked'), foraneo:$('#rndforaneo').is(':checked')},
            ubicaciones:{contacto:obtenerUbicacion('contacto'), termino:obtenerUbicacion('termino')},
            cita:obtenerCita(), comentarios:$('#txtcomentarios').val(), observaciones:$('#txtobservaciones').val(),
            ubicacion:$('#txtubicacion').val(), detalle:obtenerDetalle()
        };
        if (window.ORDEN_CONFIG.modo === 'editar') orden.pkvchid_bis = window.ORDEN_CONFIG.bis;
        return orden;
    }

    /**
     * Hidrata una orden respetando vehículo→marca→tipo y operador→grúa.
     * No usa temporizadores: cada catálogo espera la petición anterior.
     */
    function cargarOrden(orden) {
        var v = orden.vehiculo || {}, s = orden.servicio || {}, opciones = orden.opciones || {};
        $('#txtcolor').val(v.color); $('#txtplaca').val(v.placa); $('#txtmodelo').val(v.modelo);
        $('#txtserie').val(v.serie); $('#txtmotor').val(v.motor); $('#txtserv').val(orden.intno_serv);
        $('#txtsolicita').val(s.solicita); $('#txtasegurado').val(s.asegurado);
        $('#txtcontacto').val(s.contacto); $('#txtetqcontact').val(s.etiqcont);
        $('#txttermino').val(s.termino); $('#txteqtermi').val(s.etiqterm);
        $('#txttel1').val(s.telefono); $('#txtcel').val(s.celular); $('#txtpromesa').val(s.promesa || '');
        seleccionar($('#cboclave'), s.clave, s.claveTexto); $('input[name="rndcamino"][value="' + (s.camino || 'S') + '"]').prop('checked', true);
        $('#rndlocal').prop('checked', !!opciones.local); $('#rndforaneo').prop('checked', !!opciones.foraneo);
        $('#txtcomentarios').val(orden.comentarios); $('#txtobservaciones').val(orden.observaciones); $('#txtubicacion').val(orden.ubicacion);
        app.Mapa.cargarUbicacion('contacto', orden.ubicaciones && orden.ubicaciones.contacto);
        app.Mapa.cargarUbicacion('termino', orden.ubicaciones && orden.ubicaciones.termino);
        cargarCita(orden.cita);

        return $.when(cargarVehiculo(v), cargarOperadorYGrua(s)).then(function () {
            app.Grid.cargarDetalle(orden.detalle || []);
        });
    }

    app.Form = {
        cargarOrden:cargarOrden, cargarVehiculo:cargarVehiculo, cargarCita:cargarCita,
        obtenerOrden:obtenerOrden, obtenerDetalle:obtenerDetalle, obtenerUbicacion:obtenerUbicacion
    };
}(window, jQuery));
