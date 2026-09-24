/* Nueva Orden: extracción del JavaScript de NewBitacora.php.
 * Scripts clásicos: conserva funciones y estado globales de la vista. */
let idClase;
let idMarca;
let idoperador;
let vehiculo;
let empleado;
let claves;
let tiposerv;
let datos;
let datosemp;
let datoscve;
let datosserv;
var automovil;
var claveGrua;
let m_gruas;
var m_iva;
var erpTasaIVA;
var erpTasaRetencion;
var detalle;
var editorCliente;
var editorconceptos;
var editorcargo;
var erpResumenDataSourceBound;
var erpRecalcularResumen;
var citaProgramada;
var erpUbicacionTipo;
var erpUbicacionSeleccionada;
var erpMapaUbicacion;
var erpMarcadorUbicacion;

        
        // Ignorar respuestas de una clasificación o marca que ya cambió.
        function transporteVehiculo(combo, params, success, failure) {
            const vigente = function() {
                return combo === '#cbomarca'
                    ? String(params.data.qclas) === String($('#cbovehiculo').val() || 0)
                    : String(params.data.qmarca) === String($('#cbomarca').val() || 0);
            };
            if (!vigente() || (combo === '#cbomarca' ? !$('#cbovehiculo').val() : !$('#cbomarca').val())) {
                success([]);
                return { abort: function() {} };
            }
            const request = (combo === '#cbomarca' ? OrdenApi.getMarcas(params) : OrdenApi.getTipos(params));
            request.done(function(response) {
                if (vigente()) success(response);
            });
            request.fail(function(xhr, status, error) {
                if (status === 'abort' || !vigente()) return;
                $(combo).empty().trigger('change');
                failure(xhr, status, error);
            });
            return request;
        }

        function Loadgrua(idoperador)
        {
           if (!idoperador) { $('#cbogrua').empty().trigger('change'); return; }
           OrdenApi.getGruas({url:OrdenApi.urls.GetAllGrua,
                type:'POST',
                dataType:'json',           
                data:{  qopcion:1, qope:idoperador },                     
                success:function(resp)
                {
                    if (String(idoperador) !== String($('#cbooperador').val())) return;
                    m_gruas = resp || [];
                    filtrargrua();
                }
            });
        }

        function filtrargrua()
        {
            $('#cbogrua').empty().select2({
                width:'100%',
                placeholder:'Seleccione grúa',
                data: m_gruas.map(function(item){

                    return{
                        id:item.id,
                        text:item.text
                    };

                })
            }).trigger('change');
        }

        /*Mostrar la fecha actual*/
               
        function frmServicios_DesactivarFormulario()
        {
            if ($('#btngenerar').data('guardando')) return;
            $('#grid').attr('inert','').attr('aria-disabled','true').addClass('erp-grid-disabled');
            $('#rndservsobre').prop('disabled', true); //adding property
            $('#rndservfuera').prop('disabled', true); //adding property
            $('#cbovehiculo').prop('disabled', true); //adding property
            $('#cbomarca').prop('disabled', true); //adding property
            $('#cbotipo').prop('disabled', true); //adding property        
            $('#txtcolor').prop('disabled', true); //adding property
            $('#txtplaca').prop('disabled', true); //adding property
            $('#txtmodelo').prop('disabled', true); //adding property
            $('#txtmotor').prop('disabled', true);
            $('#txtserie').prop('disabled', true); //adding property              
            $('#txtsolicita').prop('disabled', true); //adding property
            $('#txtasegurado').prop('disabled', true); //adding property
            $('#txtcontacto').prop('disabled', true); //adding property
            $('#txtetqcontact').prop('disabled', true); //adding property
            $('#btncontacto').prop('disabled', true); //adding property
            $('#txttermino').prop('disabled', true); //adding property
            $('#txteqtermi').prop('disabled', true); //adding property
            $('#btntermino').prop('disabled', true); //adding property
            $('#cboclave').prop('disabled', true); //adding property
            $('#txthora').prop('disabled', true); //adding property
            $('#txtmin').prop('disabled', true); //adding property
            $('#txtserv').prop('disabled', true); //adding property
            $('#rndlocal').prop('disabled', true); //adding property
            $('#rndforaneo').prop('disabled', true); //adding property
            $('#cbooperador').prop('disabled', true); //adding property
            $('#cbogrua').prop('disabled', true); //adding property
            $('#txttel1').prop('disabled', true); //adding property
            $('#txtcel').prop('disabled', true); //adding property
            $('#chktodos').prop('disabled', true); //adding property
            $('#chkmovimiento').prop('disabled', true); //adding property
            $('#chkcitas').prop('disabled', true); //adding property
            $('#txtcomentarios').prop('disabled', true); //adding property
            $('#txtobservaciones').prop('disabled', true); //adding property
            $('#txtubicacion').prop('disabled', true);
            $('#txtpromesa, #cbotiposervicio').prop('disabled',true); //adding property
            $('#btnagregar_row, #btneliminar_row').prop('disabled', true); //adding property
            $('#txttotal').prop('disabled', true); //adding property
            $('#btngenerar').prop('disabled', true); //adding property
            $('#btncancelar').prop('disabled', true); //adding property
            $('#btnnuevo').prop('disabled', false); //adding property
        }

        function frmServicios_ActivarFormulario()
        {
            if ($('#btngenerar').data('guardando')) return;
            $('#grid').removeAttr('inert').attr('aria-disabled','false').removeClass('erp-grid-disabled');
            $('#ordenErrores').prop('hidden',true).empty();
            $('.erp-invalid').removeClass('erp-invalid');
            $('[aria-invalid]').removeAttr('aria-invalid');
            $('#rndservsobre').prop('disabled', false); //removing property
            $('#rndservfuera').prop('disabled', false); //removing property
            $('#cbovehiculo').prop('disabled', false); //removing property
            $('#cbomarca').prop('disabled', false); //removing property
            $('#cbotipo').prop('disabled', false); //removing property
            $('#txtcolor').prop('disabled', false); //removing property
            $('#txtplaca').prop('disabled', false); //removing property
            $('#txtmodelo').prop('disabled', false); //removing property
            $('#txtmotor').prop('disabled', false);
            $('#txtserie').prop('disabled', false); //removing property             
            $('#txtsolicita').prop('disabled', false); //removing property
            $('#txtasegurado').prop('disabled', false); //removing property
            $('#txtcontacto').prop('disabled', false); //removing property
            $('#txtetqcontact').prop('disabled', false); //removing property            
            $('#btncontacto').prop('disabled', false); //removing property
            $('#txttermino').prop('disabled', false); //removing property
            $('#txteqtermi').prop('disabled', false); //removing property
            $('#btntermino').prop('disabled', false); //removing property
            $('#cboclave').prop('disabled', false); //removing property
            $('#txthora').prop('disabled', false); //removing property
            $('#txtmin').prop('disabled', false); //removing property
            $('#txtserv').prop('disabled', false); //removing property
            $('#rndlocal').prop('disabled', false); //removing property
            $('#rndforaneo').prop('disabled', false); //removing property
            $('#cbooperador').prop('disabled', false); //removing property
            $('#cbogrua').prop('disabled', false); //removing property
            $('#txttel1').prop('disabled', false); //removing property
            $('#txtcel').prop('disabled', false); //removing property
            $('#chktodos').prop('disabled', false); //removing property
            $('#chkmovimiento').prop('disabled', false); //removing property
            $('#chkcitas').prop('disabled', false); //removing property
            $('#txtcomentarios').prop('disabled', false); //removing property
            $('#txtobservaciones').prop('disabled', false); //removing property
            $('#txtubicacion').prop('disabled', false);
            $('#txtpromesa, #cbotiposervicio').prop('disabled',false); //removing property
            $('#btnagregar_row, #btneliminar_row').prop('disabled', false); //removing property
            $('#txttotal').prop('disabled', false); //removing property
            $('#btngenerar').prop('disabled', false); //removing property
            $('#btncancelar').prop('disabled', false); //removing property
            $('#btnnuevo').prop('disabled', true); //removing property
        }


        /*Limpieza del table*/   
        function limpiarBody()
        {
            removerChildNodes(document.getElementById("lstConceptosGenerar").querySelector("tbody"));                                   
                  
        }


        // Typed text without a catalog selection must invalidate the previous lookup ID.
        function validarEditorCatalogo(editor, campo, campoId, invalidar) {
            return function(container,options) {
                editor(container,options);
                var combo=container.find('input[name="'+campo+'"]').data('kendoComboBox');
                if (!combo) return;
                combo.bind('change',function(){
                    if (!this.dataItem()) {
                        options.model.set(campoId,null);
                        options.model.set(campo,this.text());
                        if (invalidar) invalidar(options.model);
                    }
                });
            };
        }


        function editorCheck(container,options)
        {
            $('<input type="checkbox"/>').appendTo(container).prop("checked",options.model.ac).change(function()
            {
                options.model.set("ac",this.checked);
            });
        }

        function editorPrecio(container, options)
{
    var precioBase =
        parseFloat(options.model.get("precioBase")) || 0;

    var precioActual =
        parseFloat(options.model.get("precio")) || precioBase;


    var wrapper = $('<div class="erp-price-editor"></div>')
        .appendTo(container);


    var input = $('<input/>')
        .appendTo(wrapper)
        .kendoNumericTextBox({
            format: "c2",
            decimals: 2,
            min: precioBase,
            spinners: false
        });


    var numeric = input.data("kendoNumericTextBox");

    numeric.value(precioActual);


    $('<div class="erp-price-min"></div>')
        .html(
            '<i class="fa fa-lock"></i> ' +
            'Mínimo: ' +
            kendo.toString(precioBase, "c2")
        )
        .appendTo(wrapper);


    input.on("change", function()
    {
        var nuevoPrecio =
            parseFloat(numeric.value());


        if (isNaN(nuevoPrecio))
        {
            nuevoPrecio = precioBase;
        }


        if (nuevoPrecio < precioBase)
        {
            Swal.fire({
                icon:"warning",
                title:"Precio no permitido",
                text:
                    "El precio mínimo autorizado es " +
                    kendo.toString(precioBase, "c2")
            });

            numeric.value(precioBase);

            nuevoPrecio = precioBase;
        }


        options.model.set(
            "precio",
            nuevoPrecio
        );


        detalle.calculateRow(
            options.model
        );
        enlazarResumenERPGrid();
        window.setTimeout(actualizarResumenOrden, 0);
    });
}


        function enlazarResumenERPGrid()
        {
            if (!detalle || !detalle.ds) return;

            if (!erpResumenDataSourceBound)
            {
                detalle.ds.bind("change", function()
                {
                    window.setTimeout(actualizarResumenOrden, 0);
                });
                erpResumenDataSourceBound = true;
            }

            /* También observamos cada Model de Kendo para cubrir set() realizado
             * por los editores personalizados de ERPGrid. */
            var data = detalle.ds.data();
            $.each(data, function(index, item)
            {
                if (item && typeof item.bind === "function" && !item._erpResumenBound)
                {
                    item.bind("change", function()
                    {
                        window.setTimeout(actualizarResumenOrden, 0);
                    });
                    item._erpResumenBound = true;
                }
            });
        }


        function abrirModalCita()
        {
            var ahora = new Date();
            var yyyy = ahora.getFullYear();
            var mm = String(ahora.getMonth() + 1).padStart(2, "0");
            var dd = String(ahora.getDate()).padStart(2, "0");

            if (!$('#txtcitafecha').val())
            {
                $('#txtcitafecha').val(yyyy + "-" + mm + "-" + dd);
            }

            if (!$('#txtcitahora').val())
            {
                var hh = String(ahora.getHours()).padStart(2, "0");
                var mi = ahora.getMinutes() < 30 ? "30" : "00";
                if (mi === "00") hh = String((ahora.getHours() + 1) % 24).padStart(2, "0");
                $('#txtcitahora').val(hh + ":" + mi);
            } 

            $('#modal-cita').modal({backdrop:'static', keyboard:false});
        }


        function iniciarMapaUbicacion()
        {
            if (erpMapaUbicacion) return;

            erpMapaUbicacion = L.map('mapaUbicacion', {zoomControl:true}).setView([23.6345, -102.5528], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(erpMapaUbicacion);
        }


        function abrirModalUbicacion(tipo)
        {
            erpUbicacionTipo = tipo || 'contacto';
            $('#modalUbicacionTitulo').text(erpUbicacionTipo === 'contacto' ? 'Buscar ubicación de contacto' : 'Buscar ubicación de término');
            $('#txtbusquedaubicacion').val('');
            $('#ubicacionResultados').html('<div class="erp-location-empty"><i class="fa fa-search"></i><span>Realice una búsqueda para mostrar resultados.</span></div>');
            $('#ubicacionSeleccionada').hide();
            $('#btnUsarUbicacion').prop('disabled', true);
            erpUbicacionSeleccionada = null;
            $('#modal-ubicacion').modal('show');
        }


        function erpMostrarMarcador(lat, lon)
        {
            iniciarMapaUbicacion();
            if (erpMarcadorUbicacion)
            {
                erpMapaUbicacion.removeLayer(erpMarcadorUbicacion);
            }
            erpMarcadorUbicacion = L.marker([lat, lon], {draggable:true}).addTo(erpMapaUbicacion);
            erpMarcadorUbicacion.on('dragend', function(e)
            {
                var p=e.target.getLatLng();
                if (!erpUbicacionSeleccionada) erpUbicacionSeleccionada={display_name:'Ubicación seleccionada'};
                erpUbicacionSeleccionada.lat=p.lat;
                erpUbicacionSeleccionada.lon=p.lng;
                $('#ubicacionSeleccionadaCoordenadas').text('Lat: '+p.lat.toFixed(6)+' | Lon: '+p.lng.toFixed(6));
            });
            erpMapaUbicacion.setView([lat, lon], 17);
        }


        function erpSeleccionarUbicacion(item)
        {
            var lat = parseFloat(item.lat);
            var lon = parseFloat(item.lon);
            if (isNaN(lat) || isNaN(lon)) return;

            erpUbicacionSeleccionada =
            {
                display_name: item.display_name || '',
                lat: lat,
                lon: lon
            };

            $('#ubicacionSeleccionadaTexto').text(erpUbicacionSeleccionada.display_name);
            $('#ubicacionSeleccionadaCoordenadas').text('Lat: ' + lat.toFixed(6) + ' | Lon: ' + lon.toFixed(6));
            $('#ubicacionSeleccionada').show();
            $('#btnUsarUbicacion').prop('disabled', false);
            erpMostrarMarcador(lat, lon);
        }


        function erpBuscarUbicacion()
        {
            var texto = $.trim($('#txtbusquedaubicacion').val());
            if (!texto)
            {
                Swal.fire({icon:'info', title:'Buscar ubicación', text:'Ingrese ciudad, colonia, C.P., dirección o coordenadas.'});
                return;
            }

            var latLon = texto.match(/^\s*(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)\s*$/);
            if (latLon)
            {
                var lat = parseFloat(latLon[1]);
                var lon = parseFloat(latLon[2]);
                if (lat >= -90 && lat <= 90 && lon >= -180 && lon <= 180)
                {
                    erpSeleccionarUbicacion({lat:lat, lon:lon, display_name:'Coordenadas ' + lat + ', ' + lon});
                    return;
                }
            }

            $('#ubicacionResultados').html('<div class="erp-location-loading"><i class="fa fa-spinner fa-spin"></i> Buscando...</div>');

            OrdenApi.buscarUbicaciones({url:OrdenApi.urls.BuscarUbicaciones,
                method:'GET',
                dataType:'json',
                data:
                {
                    q:texto,
                    format:'jsonv2',
                    addressdetails:1,
                    limit:8,
                    countrycodes:'mx'
                },
                headers:{'Accept-Language':'es-MX,es;q=0.9'},
                success:function(resultados)
                {
                    if (!resultados || !resultados.length)
                    {
                        $('#ubicacionResultados').html('<div class="erp-location-empty"><i class="fa fa-map-marker"></i><span>No se encontraron ubicaciones.</span></div>');
                        return;
                    }

                    var html = '';
                    $.each(resultados, function(i, item)
                    {
                        html += '<button type="button" class="erp-location-result" data-index="' + i + '">' +
                                    '<i class="fa fa-map-marker"></i>' +
                                    '<span>' + $('<div>').text(item.display_name).html() + '</span>' +
                                '</button>';
                    });
                    $('#ubicacionResultados').html(html);

                    $('#ubicacionResultados .erp-location-result').each(function(i)
                    {
                        $(this).on('click', function(){ erpSeleccionarUbicacion(resultados[i]); });
                    });
                },
                error:function()
                {
                    $('#ubicacionResultados').html('<div class="erp-location-empty"><i class="fa fa-warning"></i><span>No fue posible consultar el servicio de mapas.</span></div>');
                }
            });
        }
window.OrdenUI = {
    init:function() {
         
                        
        idClase = 0;

        idMarca = 0;

        idoperador = 0;


        vehiculo = window.ORDEN_CONFIG.vehiculos;

        empleado = window.ORDEN_CONFIG.empleados;

        claves = window.ORDEN_CONFIG.claves;
 
        tiposerv = [{"id":0,"text":"NINGUNO"},{"id":1,"text":"LLAMADA"},{"id":2,"text":"WHATSAPP"},{"id":3,"text":"PORTAL"},{"id":4,"text":"VISITA"},{"id":99,"text":"OTROS"}];
  
        
        datos = vehiculo.map(function(item){
            return {
                id: item.pkintid_clasvehiculo,
                text: item.vchconcepto
            };
        });


        datosemp = empleado.map(function(item){
            return {
                id: item.id,
                text: item.text
            };
        });

        
        datoscve = claves.map(function(item){
            return {
                id: item.id,
                text: item.text
            };
        });

        datosserv = tiposerv.map(function(item){
            return {
                id: item.id,
                text: item.text
            };
        });

        
        $('#cbovehiculo').select2({
            width: '100%',
            placeholder: 'Seleccione',
            data: datos
        });

        
        $('#cbooperador').select2({
            width: '100%',
            placeholder: 'Seleccione Operador',
            data: datosemp
        });

        $('#cbooperador').val(null).trigger('change');

        $('#cboclave').select2({
            width: '100%',
            placeholder: 'Seleccione Clave',
            data: datoscve
        });

        $('#cbotiposervicio').select2({
            width: '100%',
            placeholder: 'Seleccione Tipo Servicio',
            data: datosserv
        });

        
        
        // Seleccionar el primero
        automovil = datos.filter(function(x){return /autom[oó]vil/i.test(x.text);})[0] || datos[0];

        if (automovil) { idClase=automovil.id; $('#cbovehiculo').val(idClase).trigger('change'); }

        claveGrua = datoscve.filter(function(x){return /servicio.*gr[uú]a/i.test(x.text);})[0];

        if (claveGrua) $('#cboclave').val(claveGrua.id).trigger('change');

        
        $('#cbovehiculo').on('change.erpDependencia', function(e)
        {
            idClase = $(this).val() || 0;
            idMarca = 0;

            $('#cbomarca').select2('close').empty().trigger('change');
            $('#cbotipo').select2('close').empty().trigger('change');
        });


        /* Cargar las marcas */    
        $('#cbomarca').select2(
        {
            width:'100%',
            placeholder:'Seleccione marca',
            ajax:
            {
                url:OrdenApi.urls.GetAllMarca,
                error:function(xhr, status, error) {
                    if (status === 'abort') return;
                    console.error('Error catálogo vehículo:', status, error, xhr.responseText);
                },
                transport:function(params, success, failure) {
                    return transporteVehiculo('#cbomarca', params, success, failure);
                },
                type:'POST',
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        buscar: params.term || '',
                        qopcion :1,
                        qclas:$('#cbovehiculo').val()
                    };
                },
                processResults: function (response) 
                {
                    console.log('GetAllMarca respuesta', response);
                    return { results:response };
                },
                cache: false
            }
        });

        /* Tip de vehiculo */
        $('#cbomarca').on('change.erpDependencia', function(e)
        {
            idMarca = $(this).val() || 0;
            $('#cbotipo').select2('close').empty().trigger('change');
        });


        $('#cbotipo').select2({
            width:'100%',
            placeholder:'Seleccione tipo',
            ajax:{
                url:OrdenApi.urls.GetAllTipo,
                error:function(xhr, status, error) {
                    if (status === 'abort') return;
                    console.error('Error catálogo vehículo:', status, error, xhr.responseText);
                },
                transport:function(params, success, failure) {
                    return transporteVehiculo('#cbotipo', params, success, failure);
                },
                type:'POST',
                dataType:'json',
                delay:250,
                data:function(params){
                    console.log('Marca seleccionada:', $('#cbomarca').val());
                    return{
                        buscar: params.term || '',
                        qopcion:1,                    
                        qmarca:$('#cbomarca').val() || 0
                    };
                },
                processResults:function(response){
                    console.log('Tipos recibidos:', response);
                    return{
                        results: response
                    };
                },
                cache:false
            }
        });

        $('#cbooperador').on('change.erpDependencia', function(e)
        {
            idoperador = $(this).val() || 0;
            $('#cbogrua').val(null).trigger('change'); 
            Loadgrua(idoperador);
                   
        });

        m_gruas = [];

        
               
        
    

        /* CITAS: al activar se abre el selector de fecha/hora.
         * Si el usuario cancela, la opción vuelve a quedar desactivada.
         */
        $('#chkcitas').on('change', function()
        {
            if (this.checked)
            {
                abrirModalCita();
            }
            else
            {
                $('#citaConfirmada').hide();
                $('#txtcitafecha').val('');
                $('#txtcitahora').val('');
            }
        });

    

        
        frmServicios_DesactivarFormulario();

              
    

        /*Creo el datasource por default*/
        m_iva = Number($('#txtdciva').val());

        erpTasaIVA = m_iva <= 1 ? m_iva : m_iva / 100;

        erpTasaRetencion = Number(window.ORDEN_CONFIG.retencion);

        detalle = new ERPGrid(
        {

            grid:"#grid",
            confirmDelete:true,
            keyboard:
            {
                enter:"next",
                tab:"next",
                shiftTab:"previous",
                insert:"add",
                delete:"remove",
                f4:"lookup",
                ctrlD:"duplicate",
                ctrlEnter:"save",
                esc:"cancel"
            },
            footer:
            {
                subtotal:"#lblsubtotal",
                iva:"#lbliva",
                total:"#lbltotal"
            }
        });
OrdenServicio.initCalculo();

        editorCliente = detalle.createComboEditor(
        {
            url:OrdenApi.urls.GetCliente_Suc_RFC,
            textField:"text",
            valueField:"id",
            valueModel:"idcliente",
            textModel:"cliente",
            parameters:
            {
                qsuc:"#txtidsuc",

                qtodos:function(){
                    return ERPGrid.Helpers.checked("#chktodos");
                }
            },
            afterSelect:function(item,options)
            {
                options.model.set("idconcepto",null);
                options.model.set("servicio","");                
                options.model.set("precio",0);
                options.model.set("retencionAplica",false);
                options.model.set("precioBase",0);
                options.model.set("idcargo",item.idcargo);
                options.model.set("cargo",item.cargo);
                var clienteId=item.id;
                options.model.set('cargoRequerido',!!item.idcargo);
                OrdenApi.getTipoCargo({url:OrdenApi.urls.GetTipoCargo,type:'POST',dataType:'json',data:{qcve:clienteId},success:function(cargos){
                    if (String(options.model.get('idcliente')) === String(clienteId)) {
                        options.model.set('cargoRequerido',!!(cargos && cargos.length));
                    }
                }});
            }
        });

        
        editorconceptos = detalle.createComboEditor(
        {   
            url:OrdenApi.urls.GetTarifaAll,
            textField:"text",
            valueField:"id",
            valueModel:"idconcepto",
            textModel:"servicio",
            error:function(xhr,status,error){
                console.error('Error al cargar servicios:',status,error,xhr.responseText);
            },
            parameters:
            {
                qcve:"@idcliente",
                qlf:function(){
                    return ERPGrid.Helpers.radio("input[name='rndlocal']")
                }
            },
            mapping:
            {
                precio:"precio",
                ac:"ac"        
            },
            afterSelect:function(item,options)
            {                    
                var precioCatalogo = parseFloat(item.precio) || 0;

                /* Precio establecido por catálogo.Este será el precio mínimo permitido.*/
                options.model.set("precioBase", precioCatalogo);
                /* Precio actual inicia con el precio del catálogo.*/
                options.model.set("precio", precioCatalogo);
                options.model.set("ac", item.ac == 1);
                options.model.set("retencionAplica", item.ret == 1);
                detalle.calculateRow(options.model);
                enlazarResumenERPGrid();
                window.setTimeout(actualizarResumenOrden, 0);
                
            }
        });

        editorcargo = detalle.createComboEditor(
        {   
            url:OrdenApi.urls.GetTipoCargo,
            textField:"text",
            valueField:"id",
            valueModel:"idcargo",
            textModel:"cargo",
            parameters:
            {
                qcve:"@idcliente"
            },            
            afterSelect:function(item,options)
            {
                detalle.calculateRow(options.model);
                enlazarResumenERPGrid();
                window.setTimeout(actualizarResumenOrden, 0);
            }
        });

        editorCliente=validarEditorCatalogo(editorCliente,'cliente','idcliente',function(model){
            model.set('idconcepto',null); model.set('servicio',''); model.set('idcargo',null); model.set('cargo','');
            model.set('precio',0); model.set('precioBase',0); model.set('retencionAplica',false);
        });

        editorconceptos=validarEditorCatalogo(editorconceptos,'servicio','idconcepto');

        editorcargo=validarEditorCatalogo(editorcargo,'cargo','idcargo');

        detalle.settings.columns = 
        [
            /* CLIENTE */
            {
                field: "cliente", title: "Cliente", editor: editorCliente,width: 145,editable :true,
                headerAttributes: { class: "erp-grid-center" },attributes: {class: "erp-grid-text"}
            }, 
            /* SERVICIO */
            {
                field: "servicio", title: "Servicio", editor: editorconceptos,width: 180,
                headerAttributes: { class: "erp-grid-center" },attributes: { class: "erp-grid-text" }
            },
            /* ASISTENCIA */
            {
                field: "asistencia", title: "Asistencia", width: 80,
                headerAttributes: { class: "erp-grid-center" }, attributes: { class: "erp-grid-center" }
            },
            /* EXPEDIENTE */
            {
                field: "expendiente", title: "Exp.", width: 65, headerAttributes: { class: "erp-grid-center" },
                attributes: { class: "erp-grid-center" }
            },
            /* KMN */
            {
                field: "kmn",title: "KMN", width: 48, headerAttributes: { class: "erp-grid-center" },
                attributes: { class: "erp-grid-center" }
            },
            /* KM */
            {
                field: "km", title: "KM", width: 48, headerAttributes: { class: "erp-grid-center" },
                attributes: { class: "erp-grid-center" }
            },
            /* CANTIDAD */
            {
                field: "cantidad", title: "Cant.", width: 55, headerAttributes: { class: "erp-grid-center" },
                attributes: { class: "erp-grid-center" }
            },
            /* PRECIO */
            {
                field: "precio", title: "Precio", width: 78, format: "{0:c}",  editor:editorPrecio, attributes: { class: "erp-grid-money" },
                headerAttributes: { class: "erp-grid-center" }
            },
            /* IVA */
            {
                field: "iva", title: "IVA", width: 75, format: "{0:c}", editable: false, 
                attributes: { class: "erp-grid-money erp-grid-readonly" }, headerAttributes: { class: "erp-grid-center" }
            },
            /* SUBTOTAL */
            {
                field: "subtotal", title: "Subtotal", width: 82, format: "{0:c}", editable: false,
                attributes: { class: "erp-grid-money erp-grid-readonly" }, headerAttributes: { class: "erp-grid-center" }
            },
            /* TOTAL */
            {
                field: "total", title: "Total", width: 82, format: "{0:c}", editable: false, 
                attributes: { class: "erp-grid-money erp-grid-total erp-grid-readonly" },headerAttributes: { class: "erp-grid-center" }
            },
            /* TIPO CARGO */
            {
                field: "cargo", title: "Cargo", editor: editorcargo, width: 72, 
                headerAttributes: { class: "erp-grid-center" }, attributes: { class: "erp-grid-center" }
            },
            /* AC */
            {
                field: "ac", title: "AC", width: 38, minScreenWidth: 35, editor: editorCheck,
                template:
                    "# if(data.ac){ #" +
                        "<span class='erp-check ok'></span>" +
                    "# } else { #" +
                        "<span class='erp-check no'></span>" +
                    "# } #",
                editable: false, headerAttributes: { class: "erp-grid-center" },
                attributes: { class: "erp-grid-center" }
            }
        ];

       
        detalle.init();

        $('#btnagregar_row').on('click',function(){
            if ($('#btngenerar').data('guardando')) return;
            detalle.grid.closeCell(); detalle.addRow();
        });

        $('#btneliminar_row').on('click',function(){
            if ($('#btngenerar').data('guardando')) return;
            var row=detalle.grid.select().closest('tr');
            var item=detalle.grid.dataItem(row);
            if (!item) { Swal.fire({icon:'info',title:'Seleccione una fila del detalle'}); return; }
            detalle.grid.closeCell(); detalle.ds.remove(item); actualizarResumenOrden();
        });

        $('#grid').attr('inert','').attr('aria-disabled','true').addClass('erp-grid-disabled');


        /* Cada cambio de fila vuelve a calcular el resumen. */
        erpResumenDataSourceBound = false;


        /* Reforzamos los puntos donde cambian los importes. */
        erpRecalcularResumen = function(model)
        {
            if (model && detalle && typeof detalle.calculateRow === "function")
            {
                detalle.calculateRow(model);
            }
            enlazarResumenERPGrid();
            window.setTimeout(actualizarResumenOrden, 0);
        };


        /* Enlazar después de inicializar el grid. */
        enlazarResumenERPGrid();

        detalle.addRow();

        enlazarResumenERPGrid();

        actualizarResumenOrden();


        /* =========================================================
         * CITAS
         * ========================================================= */
        citaProgramada = null;


        $('#btnCancelarCita').on('click', function()
        {
            $('#chkcitas').prop('checked', false);
            citaProgramada = null;
            $('#citaConfirmada').hide();
            $('#modal-cita').modal('hide');
        });


        $('#btnConfirmarCita').on('click', function()
        {
            var fecha = $('#txtcitafecha').val();
            var hora = $('#txtcitahora').val();

            if (!fecha || !hora)
            {
                Swal.fire({icon:'warning', title:'Cita incompleta', text:'Seleccione la fecha y la hora de la cita.'});
                return;
            }

            citaProgramada =
            {
                fecha: fecha,
                hora: hora,
                duracion: $('#txtcitaduracion').val(),
                observacion: $('#txtcitaobservacion').val()
            };

            /* Conservamos la fecha de servicio del formulario. */
            var partes = fecha.split('-');
            if (partes.length === 3)
            {
                $('#txtfecha').val(partes[2] + '/' + partes[1] + '/' + partes[0]);
            }

            $('#citaConfirmadaTexto').text(partes[2] + '/' + partes[1] + '/' + partes[0] + ' ' + hora);
            $('#txtcitafecha_guardada').val(fecha);
            $('#txtcitahora_guardada').val(hora);
            $('#txtcitaduracion_guardada').val($('#txtcitaduracion').val());
            $('#txtcitaobservacion_guardada').val($('#txtcitaobservacion').val());
            $('#citaConfirmada').show();
            $('#modal-cita').modal('hide');
        });
OrdenServicio.init();


        /* =========================================================
         * UBICACIONES / LEAFLET + OPENSTREETMAP
         * ========================================================= */
        erpUbicacionTipo = 'contacto';

        erpUbicacionSeleccionada = null;

        erpMapaUbicacion = null;

        erpMarcadorUbicacion = null;


        $('#modal-ubicacion').on('shown.bs.modal', function()
        {
            iniciarMapaUbicacion();
            window.setTimeout(function(){ erpMapaUbicacion.invalidateSize(); }, 100);
            if (!erpMapaUbicacion._erpClickBound)
            {
                erpMapaUbicacion.on('click', function(e)
                {
                    var nombre=(erpUbicacionSeleccionada && erpUbicacionSeleccionada.display_name) || 'Ubicación marcada manualmente';
                    erpSeleccionarUbicacion({lat:e.latlng.lat, lon:e.latlng.lng, display_name:nombre});
                });
                erpMapaUbicacion._erpClickBound=true;
            }
        });


        $('#btnBuscarUbicacion').on('click', erpBuscarUbicacion);

        $('#txtbusquedaubicacion').on('keydown', function(e)
        {
            if (e.keyCode === 13)
            {
                e.preventDefault();
                erpBuscarUbicacion();
            }
        });


        $('#btnUsarUbicacion').on('click', function()
        {
            if (!erpUbicacionSeleccionada) return;

            var texto = erpUbicacionSeleccionada.display_name;
            var coordenadas = erpUbicacionSeleccionada.lat.toFixed(6) + ', ' + erpUbicacionSeleccionada.lon.toFixed(6);
            var valor = texto + ' [' + coordenadas + ']';

            if (erpUbicacionTipo === 'contacto')
            {
                $('#txtcontacto').val(valor);
                $('#txtcontactolat').val(erpUbicacionSeleccionada.lat);
                $('#txtcontactolon').val(erpUbicacionSeleccionada.lon);
            }
            else
            {
                $('#txttermino').val(valor);
                $('#txtterminolat').val(erpUbicacionSeleccionada.lat);
                $('#txtterminolon').val(erpUbicacionSeleccionada.lon);
            }

            $('#modal-ubicacion').modal('hide');
        });


        $('#btncontacto').on('click', function(){ abrirModalUbicacion('contacto'); });

        $('#btntermino').on('click', function(){ abrirModalUbicacion('termino'); });


        /* Cuando se elige una tarifa, recalcular y actualizar el resumen. */
        if (typeof editorconceptos !== 'undefined') { /* marcador de compatibilidad */ }
    }
};
