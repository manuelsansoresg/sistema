<html>
<head>
<meta charset="utf-8">
    <?= get_favicon(); ?>
    <meta charset="<?= SITE_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= SITE_DESC ?>">
    <meta name="author" content="Salvamentos Freecar SA de CV">
    <meta name="generator" content="<?= SITE_VERSION ?>">
    <title><?= SITE_NAME ?></title>
    
    <script type="text/javascript" src="<?= PLUGINS ?>/pace-master/pace.min.js"></script>        
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= BOOTSTRAP ?>/css/bootstrap.min.css">            
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= DIST ?>/css/font-awesome.min.css">   
    <link rel="stylesheet" href="<?= ASSETS ?>/app/css/ERPTheme.Kendo.css">
    <link rel="stylesheet" href="<?= ASSETS ?>/app/css/ERPLookup.css">
    <link rel="stylesheet" href="<?= ASSETS ?>/css/erp/erp.layout.css">
    <link rel="stylesheet" href="<?= ASSETS ?>/app/css/erp/orden-servicio.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= DIST ?>/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= PLUGINS ?>/select2/select2.css">
    <!-- Leaflet / OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.common.min.css" />
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.rtl.min.css" >
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.default.min.css" />
    <!-- Bootstrap and OneUI CSS framework -->
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.mobile.all.min.css">
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.dataviz.min.css">
    <link rel="stylesheet" href="<?= CSS ?>/kendo/css/kendo.dataviz.blueopal.min.css">

    <link rel="stylesheet" href="<?= DIST ?>/css/skins/all-skins.min.css">        
    <link rel="stylesheet" href="<?= LIBS ?>/sweetalert2/sweetalert2.min.css" >  

    
<?php
    
    $m_Session = new Session();
    $iduser = '';
    $idemp = '';
    $idsuc = '';
    $dciva = 0;
    $dcret = 0;
    
    if (array_key_exists('IdUsuario', $_SESSION)) 
    {   $iduser = unserialize($m_Session->getCurrentIdUsuario()); }
    if (array_key_exists('cveEmpresa', $_SESSION)) 
    {   $idemp = unserialize($m_Session->getCurrentCEmpresa()); }
    if (array_key_exists('cveSucursal', $_SESSION)) 
    {   $idsuc = unserialize($m_Session->getCurrentCSucursal()); } 
    if (array_key_exists('DCIVA', $_SESSION)) 
    {   $dciva = unserialize($m_Session->getCurrentIVA()); }
    if (array_key_exists('DCRET', $_SESSION)) 
    {   $dcret = unserialize($m_Session->getCurrentRETENCION()); }
    if (isset($ivaConfigurado)) $dciva = (float)$ivaConfigurado;
    if (isset($retencionConfigurada)) $dcret = (float)$retencionConfigurada;
?>

</head>
<body>    
    <input type="hidden" id="txtiduser" name="txtiduser" value="<?= $iduser?>">
    <input type="hidden" id="txtidemp" name="txtidemp" value="<?= $idemp?>">
    <input type="hidden" id="txtidsuc" name="txtidsuc" value="<?= htmlspecialchars((string)$idsuc, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" id="txtidclisuc" name="txtidclisuc" value="">
    <input type="hidden" id="txtcitafecha_guardada" name="txtcitafecha_guardada" value="">
    <input type="hidden" id="txtcitahora_guardada" name="txtcitahora_guardada" value="">
    <input type="hidden" id="txtcitaduracion_guardada" name="txtcitaduracion_guardada" value="">
    <input type="hidden" id="txtcitaobservacion_guardada" name="txtcitaobservacion_guardada" value="">
    <input type="hidden" id="txtcontactolat" name="txtcontactolat" value="">
    <input type="hidden" id="txtcontactolon" name="txtcontactolon" value="">
    <input type="hidden" id="txtterminolat" name="txtterminolat" value="">
    <input type="hidden" id="txtterminolon" name="txtterminolon" value="">
    <input type="hidden" id="txtdciva" name="txtdciva" value="<?= $dciva?>">
    <input type="hidden" id="txtdcret" name="txtdcret" value="<?= $dcret?>">
    <div class="erp-layout">
        <!-- Toolbar -->
        <div class="erp-toolbar">
            <div class="erp-toolbar-left">
                <span class="erp-toolbar-title">
                    <i class="fa fa-file-text-o"></i>Orden de Servicio
                </span>
            </div>
            <div class="erp-toolbar-right">
                <button type="button" id="btnnuevo" class="erp-btn success" onClick="frmServicios_ActivarFormulario();"><i class="fa fa-file"></i> Nuevo </button>
                <button type="button" id="btngenerar" class="erp-btn primary"><i class="fa fa-save"></i> Generar</button>                        
                <button type="button" id="btncancelar" class="erp-btn danger" onclick="frmServicios_DesactivarFormulario();"><i class="fa fa-remove"></i>Cancelar</button>
                <button type="button" class="erp-btn warning"> <i class="fa fa-print"></i> Imprimir</button>
            </div>          
        </div>
        <p class="erp-required-help"><span aria-hidden="true">*</span> Campos obligatorios</p>
        <div id="ordenErrores" class="erp-validation-summary" role="alert" tabindex="-1" hidden></div>
        <!-- Cuerpo -->
        <div class="erp-layout-body">
            <div class="erp-main">
                <div class="erp-top">
                    <!-- Panel izquierdo -->
                    <div class="erp-left">
                        <div class="erp-card">
                            <div class="erp-card-header"> <i class="fa fa-car"></i>           
                                Automovil
                            </div>
                            <div class="erp-card-body">                                
                                <div class="form-group">                                    
                                    <label for="cbovehiculo">TIPO DE VEHICULO <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                    <select class="select2 input-default" style="width:100%;" name="cbovehiculo" id="cbovehiculo" aria-required="true"></select>
                                </div>                               
                                <div class="form-group">                                    
                                    <label for="cbomarca">MARCA <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                    <select class="cbomarca form-control" style="width:100%" name="cbomarca" id="cbomarca" aria-required="true"></select>
                                </div>
                                <div class="form-group">
                                    <label for="cbotipo">TIPO <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                    <select class="cbotipo form-control" style="width:100%" name="cbotipo" id="cbotipo" aria-required="true"></select>
                                </div>                                
                                <div class="erp-form-row">
                                    <div class="erp-form-label"><i class="fa fa-tint"></i>COLOR</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtcolor" name="txtcolor" placeholder="COLOR">
                                    </div>
                                </div> 
                                <div class="erp-form-row">
                                    <div class="erp-form-label"><i class="fa fa-calendar"></i>PLACAS</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtplaca" name="txtplaca" placeholder="PLACAS">
                                    </div>
                                </div>                               
                                <div class="erp-form-row">
                                    <div class="erp-form-label"><i class="fa fa-calendar"></i>MODELO</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtmodelo" name="txtmodelo" placeholder="MODELO">
                                    </div>
                                </div>                               
                                <div class="erp-form-row">
                                    <div class="erp-form-label"><i class="fa fa-barcode"></i>SERIE</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtserie" name="txtserie" placeholder="SERIE">
                                    </div>

                                </div>                                
                                <div class="erp-form-row">
                                    <label class="erp-form-label" for="txtmotor"><i class="fa fa-cog"></i>MOTOR</label>
                                    <div class="erp-form-control"><input type="text" class="form-control erp-input-sm text-uppercase" id="txtmotor" name="txtmotor" placeholder="MOTOR (opcional)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Panel central -->
                    <div class="erp-center">
                        <div class="erp-card">
                            <div class="erp-card-header">            
                                SERVICIOS
                            </div>
                            <div class="erp-card-body">
                                <div class="erp-form-row">                                
                                    <div class="erp-form-label"><i class="fa fa-user-md"></i>SOLICITA</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtsolicita" name="txtsolicita" PlaceHolder="SOLICITA" disabled>
                                    </div>
                                </div>                            
                                <div class="erp-form-row">                                
                                    <div class="erp-form-label"><i class="fa fa-user-md"></i>ASEGURADO</div>
                                    <div class="erp-form-control">
                                        <input type="text" class="form-control erp-input-sm text-uppercase" id="txtasegurado" name="txtasegurado" PlaceHolder="ASEGURADO" disabled>
                                    </div>
                                </div>
                                <div class="erp-form-row">                                
                                    <label class="erp-form-label" for="txtcontacto"><i class="fa fa-map-pin"></i>CONTACTO <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                    <div class="erp-form-control">
                                        <div class="erp-input-group">
                                            <input type="text" class="form-control erp-input-sm text-uppercase" id="txtcontacto" aria-required="true" name="txtcontacto" placeholder="CONTACTO" disabled>
                                            <input type="text" class="form-control erp-input-sm text-uppercase" id="txtetqcontact" name="txtetqcontact" placeholder="ZONA CONTACTO" disabled>                                            
                                            <button class="erp-input-btn" type="button" id="btncontacto" name="btncontacto" data-toggle="modal" data-target="#modal-mapacontacto" disabled>
                                            <i class="fa fa-search push-5-r"></i></button>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="erp-form-row">                                
                                    <label class="erp-form-label" for="txttermino"><i class="fa fa-map-pin"></i>TERMINO <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                    <div class="erp-form-control">
                                        <div class="erp-input-group">
                                            <input type="text" class="form-control erp-input-sm text-uppercase" id="txttermino" aria-required="true" name="txttermino" PlaceHolder="TERMINO" disabled>
                                            <input type="text" class="form-control erp-input-sm text-uppercase" id="txteqtermi" name="txteqtermi" PlaceHolder="ZONA TERMINO" disabled>                                            
                                            <button class="erp-input-btn" type="button" id="btntermino" name="btntermino" disabled><i class="fa fa-search push-5-r"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="erp-form-row">                                
                                    <div class="erp-form-label"><i class="fa fa-phone"></i>TELEFONO</div>
                                    <div class="erp-form-control">
                                        <input name="txttel1" id="txttel1" class="form-control erp-input-sm" Placeholder="TELÉFONO 1" />
                                    </div>
                                    <div class="erp-form-label"><i class="fa fa-mobile-phone"></i>CELULAR</div>
                                    <div class="erp-form-control">
                                        <input name="txtcel" id="txtcel" class="form-control erp-input-sm" Placeholder="CELULAR" />
                                    </div>                                            
                                </div>
                                <br>
                                <!-- DATOS INTERNOS -->
                                <div class="erp-card erp-card-internal">
                                    <div class="erp-card-header">
                                        <div class="erp-card-header-title">
                                            <i class="fa fa-cogs"></i>
                                            <div>
                                                <strong>Datos internos</strong>
                                                <small>Información operativa del servicio</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="erp-card-body">
                                        <div class="erp-internal-grid">
                                            <!-- BLOQUE CLAVE  -->
                                            <div class="erp-internal-section">
                                                <div class="erp-internal-title">
                                                    <i class="fa fa-key"></i>
                                                    <span>Configuración</span>
                                                </div>
                                                <!-- CLAVE -->
                                                <div class="erp-field">
                                                    <label for="cboclave">Clave <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                                    <select class="cbotipo form-control" name="cboclave" id="cboclave" aria-required="true">
                                                    </select>
                                                </div>
                                                <div class="erp-internal-row">
                                                    <!-- SERV -->
                                                    <div class="erp-field">
                                                        <label>Serv.</label>
                                                        <input class="form-control" type="number" id="txtserv" name="txtserv" readonly aria-label="Primer servicio de la nueva orden" value="1" placeholder="1">
                                                    </div>
                                                    <!-- TIPO SERVICIO -->
                                                    <div class="erp-field">
                                                        <label>Local / Foráneo <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                                        <div class="erp-radio-group">
                                                            <label class="erp-radio">
                                                                <input type="radio" name="rndlocal" id="rndlocal" checked value="L">
                                                                <span class="erp-radio-mark"></span>
                                                                <span>Local</span>
                                                            </label>
                                                            <label class="erp-radio">
                                                                <input type="radio" name="rndlocal" id="rndforaneo" value="F">
                                                                <span class="erp-radio-mark"></span>
                                                                <span>Foráneo</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- BLOQUE OPERATIVO -->
                                            <div class="erp-internal-section">
                                                <div class="erp-internal-title">
                                                    <i class="fa fa-user"></i>
                                                    <span>Operación</span>
                                                </div>
                                                <!-- OPERADOR -->
                                                <div class="erp-field">
                                                    <label for="cbooperador">Operador <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                                    <select class="select2 input-default" style="width:100%;" name="cbooperador" id="cbooperador" aria-required="true">
                                                    </select>
                                                </div>
                                                <div class="erp-internal-row">
                                                    <!-- GRUA -->
                                                    <div class="erp-field">
                                                        <label for="cbogrua">Grúa <span class="erp-required" aria-label="Obligatorio">*</span></label>
                                                        <select class="select2 input-default" style="width:100%;" name="cbogrua" id="cbogrua" aria-required="true">
                                                        </select>
                                                    </div>
                                                    <!-- TIEMPO PROMESA -->
                                                    <div class="erp-field">
                                                        <label>Tiempo promesa</label>
                                                        <input type="number" min="1" step="1" name="txtpromesa" id="txtpromesa" class="form-control" placeholder="Minutos (opcional)">
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
            <!-- Panel derecho -->
            <div class="erp-right">
                <div class="erp-card">
                    <div class="erp-card-header">            
                            Datos del Servicio
                        </div>
                    <div class="erp-card-body">
                        <div class="erp-radio-group erp-road-default" role="group" aria-label="Condición del servicio">
                            <label class="erp-radio"><input type="radio" name="rndcamino" id="rndservsobre" value="S" checked><span class="erp-radio-mark"></span><span>Sobre Camino</span></label>
                            <label class="erp-radio"><input type="radio" name="rndcamino" id="rndservfuera" value="F"><span class="erp-radio-mark"></span><span>Fuera del camino</span></label>
                        </div>
                        <div class="form-group">
                            <label for="cbotiposervicio">Medio de solicitud (opcional)</label>
                            <select class="select2 input-default" id="cbotiposervicio" name="cbotiposervicio"></select>
                        </div>
                                                       
                    </div>
                </div>
                <div class="erp-card">
                    <div class="erp-card-header">
                        <i class="fa fa-sliders"></i>Opciones
                    </div> 
                    <label class="erp-checkbox success">
                        <input type="checkbox" id="chktodos" name="chktodos">
                        <span class="checkmark"></span><i class="fa fa-users"></i> Todos los clientes
                    </label>
                    <label class="erp-checkbox warning">
                        <input type="checkbox" id="chkcitas" name="chkcitas">
                        <span class="checkmark"></span><i class="fa fa-calendar"></i> Activar Citas
                    </label>
                    <div class="erp-cita-confirmada" id="citaConfirmada" style="display:none;">
                        <i class="fa fa-clock-o"></i>
                        <span>Cita: <strong id="citaConfirmadaTexto"></strong></span>
                    </div>                    
                </div>
                <div class="erp-card">
                    <div class="erp-card-header">
                        <i class="fa fa-money"></i>
                        Resumen
                    </div>
                    <div class="erp-card-body">
                        <table class="table table-condensed erp-total-table">
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-right erp-summary-value" id="lblsubtotal">$0.00</td>
                            </tr>
                            <tr>
                                <td>IVA</td>
                                <td class="text-right erp-summary-value" id="lbliva">$0.00</td>
                            </tr>                                
                            <tr><td>Retención</td><td class="text-right erp-summary-value" id="lblretencion">$0.00</td></tr>
                        </table>
                        <div class="erp-total-final">
                            <span>TOTAL</span>
                            <span id="lbltotal">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
                </div>
                <div class="erp-card erp-card-notes">
                    <div class="erp-card-header">
                        <i class="fa fa-comments-o"></i> 
                        <span>Comentarios y observaciones</span>
                    </div>
                    <div class="erp-card-body">
                        <div class="erp-notes-grid">
                            <!-- COMENTARIOS -->
                            <div class="erp-note-field">
                                <label>
                                    <i class="fa fa-comment-o"></i>
                                    Comentarios
                                </label>
                                <textarea id="txtcomentarios" name="txtcomentarios" class="form-control" placeholder="Ingrese comentarios..."></textarea>
                            </div>
                            <!-- OBSERVACIONES -->
                            <div class="erp-note-field">
                                <label>
                                    <i class="fa fa-sticky-note-o"></i>
                                    Observaciones
                                </label>
                                <textarea id="txtobservaciones" name="txtobservaciones" class="form-control" placeholder="Ingrese observaciones..."></textarea>
                            </div>
                            <!-- UBICACIÓN -->
                            <div class="erp-note-field">
                                <label>
                                    <i class="fa fa-map-marker"></i>
                                    Ubicación
                                </label>
                                <textarea id="txtubicacion" name="txtubicacion" class="form-control" placeholder="Ingrese ubicación..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>                             
            </div>
        </div>        
        <!-- Parte inferior -->
        <!-- DETALLE DEL SERVICIO -->
        <div class="erp-layout-footer">
            <div class="erp-card erp-card-grid">
                <div class="erp-card-header erp-grid-header">
                    <div class="erp-grid-title">             
                        <div class="erp-grid-icon">
                            <i class="fa fa-list-alt"></i>
                        </div>
                        <div>
                            <strong>Detalle del servicio</strong>
                        </div>
                    </div>
                    <div class="erp-grid-info">
                        <i class="fa fa-keyboard-o"></i>
                        ENTER / TAB para navegar
                    </div>
                </div>
                <div class="erp-grid-toolbar">
                    <button type="button" id="btnagregar_row" class="erp-btn primary" disabled><i class="fa fa-plus"></i> Agregar concepto</button>
                    <button type="button" id="btneliminar_row" class="erp-btn danger" disabled><i class="fa fa-trash"></i> Quitar fila</button>
                </div>
                <div class="erp-grid-body">
                    <div class="erp-grid-scroll">
                        <div id="grid"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
  
    <!-- =========================================================
         MODAL: PROGRAMAR CITA
         ========================================================= -->
    <div class="modal fade erp-modal" id="modal-cita" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-calendar"></i> Programar cita</h4>
                </div>
                <div class="modal-body">
                    <div class="erp-modal-help">Seleccione el día y la hora en que se realizará el servicio.</div>
                    <div class="row">
                        <div class="col-xs-7">
                            <div class="form-group">
                                <label for="txtcitafecha">Fecha de cita</label>
                                <input type="date" id="txtcitafecha" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="txtcitahora">Hora</label>
                                <input type="time" id="txtcitahora" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtcitaduracion">Duración estimada</label>
                        <select id="txtcitaduracion" class="form-control">
                            <option value="30">30 minutos</option>
                            <option value="60" selected>1 hora</option>
                            <option value="90">1 hora 30 minutos</option>
                            <option value="120">2 horas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="txtcitaobservacion">Observación de la cita</label>
                        <textarea id="txtcitaobservacion" class="form-control" rows="3" placeholder="Observación opcional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelarCita">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarCita"><i class="fa fa-check"></i> Confirmar cita</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         MODAL: BUSQUEDA DE UBICACION
         ========================================================= -->
    <div class="modal fade erp-modal erp-modal-location" id="modal-ubicacion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-map-marker"></i> <span id="modalUbicacionTitulo">Buscar ubicación</span></h4>
                </div>
                <div class="modal-body">
                    <div class="erp-location-search">
                        <div class="erp-location-search-row">
                            <div class="erp-location-search-input">
                                <label for="txtbusquedaubicacion">Buscar lugar</label>
                                <input type="text" id="txtbusquedaubicacion" class="form-control" placeholder="Ciudad, colonia, C.P., dirección o latitud,longitud">
                            </div>
                            <div class="erp-location-search-action">
                                <button type="button" class="btn btn-primary" id="btnBuscarUbicacion"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="erp-location-examples">
                            Ejemplos: <strong>Monterrey</strong>, <strong>64000</strong>, <strong>Centro, Monterrey</strong> o <strong>25.6866,-100.3161</strong>
                        </div>
                    </div>
                    <div class="erp-location-layout">
                        <div class="erp-location-results" id="ubicacionResultados">
                            <div class="erp-location-empty"><i class="fa fa-search"></i><span>Realice una búsqueda para mostrar resultados.</span></div>
                        </div>
                        <div class="erp-location-map-wrap">
                            <div id="mapaUbicacion"></div>
                        </div>
                    </div>
                    <div class="erp-location-selected" id="ubicacionSeleccionada" style="display:none;">
                        <div><i class="fa fa-map-marker"></i></div>
                        <div>
                            <strong id="ubicacionSeleccionadaTexto"></strong>
                            <small id="ubicacionSeleccionadaCoordenadas"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnUsarUbicacion" disabled><i class="fa fa-check"></i> Usar ubicación</button>
                </div>
            </div>
        </div>
    </div>

</body>

    <script src="<?= PLUGINS ?>/jQuery/jquery-2.2.3.min.js"></script>     
    <!-- Bootstrap 3.3.6 -->
    <script src="<?= BOOTSTRAP ?>/js/bootstrap.min.js"></script>    
    <!-- FastClick -->
    <script src="<?= PLUGINS ?>/fastclick/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= DIST ?>/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= DIST ?>/js/demo.js"></script>
    <script src="<?= CSS ?>/kendo/js/kendo.all.min.js"></script>           
    <script src="<?= PLUGINS ?>/select2/select2.full.min.js"></script>          
    <script src="<?= LIBS ?>/sweetalert2/sweetalert2.min.js"></script>         
    <script src="<?= ASSETS ?>/app/js/ErpGrid.js"></script>
    <script src="<?= ASSETS ?>/app/js/ErpGrid.Keyboard.js"></script> 
    <script src="<?= ASSETS ?>/app/js/ErpGrid.Editors.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPGrid.Helpers.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPGrid.Calculation.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPLookup.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPDropDown.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPGrid.Navigation.js"></script>
    <script src="<?= ASSETS ?>/app/js/ERPDatePicker.js"></script>
    

    <!-- llenado de lista -->
    <script type="text/javascript">         
                        
        let idClase = 0;
        let idMarca = 0;
        let idoperador = 0;

        let vehiculo = <?= $tipclasif ?>;
        let empleado = <?= $Empleado ?>;
        let claves = <?= $Claves ?>; 
        let tiposerv = [{"id":0,"text":"NINGUNO"},{"id":1,"text":"LLAMADA"},{"id":2,"text":"WHATSAPP"},{"id":3,"text":"PORTAL"},{"id":4,"text":"VISITA"},{"id":99,"text":"OTROS"}];  
        
        let datos = vehiculo.map(function(item){
            return {
                id: item.pkintid_clasvehiculo,
                text: item.vchconcepto
            };
        });

        let datosemp = empleado.map(function(item){
            return {
                id: item.id,
                text: item.text
            };
        });
        
        let datoscve = claves.map(function(item){
            return {
                id: item.id,
                text: item.text
            };
        });
        let datosserv = tiposerv.map(function(item){
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
        var automovil=datos.filter(function(x){return /autom[oó]vil/i.test(x.text);})[0] || datos[0];
        if (automovil) { idClase=automovil.id; $('#cbovehiculo').val(idClase).trigger('change'); }
        var claveGrua=datoscve.filter(function(x){return /servicio.*gr[uú]a/i.test(x.text);})[0];
        if (claveGrua) $('#cboclave').val(claveGrua.id).trigger('change');
        
        $('#cbovehiculo').on('change.erpDependencia', function(e)
        {
            idClase = $(this).val() || 0;
            idMarca = 0;

            $('#cbomarca').select2('close').empty().trigger('change');
            $('#cbotipo').select2('close').empty().trigger('change');
        });
        
        // Ignorar respuestas de una clasificación o marca que ya cambió.
        function transporteVehiculo(combo, params, success, failure) {
            const vigente = function() {
                return String(params.data.qclas) === String(idClase) &&
                    (combo === '#cbomarca' || String(params.data.qmarca) === String(idMarca));
            };
            if (!vigente() || !idClase || (combo === '#cbotipo' && !idMarca)) {
                success([]);
                return { abort: function() {} };
            }
            const request = $.ajax(params);
            request.done(function(response) {
                if (vigente()) success(response);
            });
            request.fail(function(xhr, status, error) {
                if (status === 'abort' || !vigente()) return;
                console.error('Error al cargar ' + (combo === '#cbomarca' ? 'Marca' : 'Tipo'), {
                    status: xhr.status, error: error, response: xhr.responseText
                });
                $(combo).empty().trigger('change');
                failure(xhr, status, error);
            });
            return request;
        }

        /* Cargar las marcas */    
        $('#cbomarca').select2(
        {
            width:'100%',
            placeholder:'Seleccione marca',
            ajax:
            {
                url:'<?=base_url?>/Bitacoras/GetAllMarca',
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
                        qclas:idClase                   
                    };
                },
                processResults: function (response) 
                {
                    return { results:response };
                },
                cache: true
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
                url:'<?=base_url?>/Bitacoras/GetAllTipo',
                transport:function(params, success, failure) {
                    return transporteVehiculo('#cbotipo', params, success, failure);
                },
                type:'POST',
                dataType:'json',
                delay:250,
                data:function(params){

                    return{
                        buscar: params.term || '',
                        qopcion:1,                    
                        qmarca:idMarca, qclas:idClase
                    };
                },
                processResults:function(response){

                    return{
                        results: response
                    };
                },
                cache:true
            }
        });
        $('#cbooperador').on('change.erpDependencia', function(e)
        {
            idoperador = $(this).val() || 0;
            $('#cbogrua').val(null).trigger('change'); 
            Loadgrua(idoperador);
                   
        });
        let m_gruas = [];
        function Loadgrua(idoperador)
        {
           if (!idoperador) { $('#cbogrua').empty().trigger('change'); return; }
           $.ajax
                ({ 
            
                url:'<?=base_url?>/Bitacoras/GetAllGrua',
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
        
               
        
    </script>

    <script type="text/javascript">
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
    </script> 
    <!-- Eventos -->
    <script type="text/javascript">
        
        frmServicios_DesactivarFormulario();
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
              
    </script>
    <!-- Llenado de KendoGrid -->
    <script type="text/javascript">
        /*Creo el datasource por default*/
        var m_iva = Number($('#txtdciva').val());
        var erpTasaIVA = m_iva <= 1 ? m_iva : m_iva / 100;
        var erpTasaRetencion = Number(<?= json_encode($dcret) ?>);
        var detalle=new ERPGrid(
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
        var editorCliente=detalle.createComboEditor(
        {
            url:"<?=base_url ?>/Bitacoras/GetCliente_Suc_RFC",
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
                $.ajax({url:'<?=base_url?>/Bitacoras/GetTipoCargo',type:'POST',dataType:'json',data:{qcve:clienteId},success:function(cargos){
                    if (String(options.model.get('idcliente')) === String(clienteId)) {
                        options.model.set('cargoRequerido',!!(cargos && cargos.length));
                    }
                }});
            }
        });
        
        var editorconceptos=detalle.createComboEditor(
        {   
            url:"<?=base_url ?>/Bitacoras/GetTarifaAll",
            textField:"text",
            valueField:"id",
            valueModel:"idconcepto",
            textModel:"servicio",
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
        var editorcargo=detalle.createComboEditor(
        {   
            url:"<?=base_url ?>/Bitacoras/GetTipoCargo",
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
        editorCliente=validarEditorCatalogo(editorCliente,'cliente','idcliente',function(model){
            model.set('idconcepto',null); model.set('servicio',''); model.set('idcargo',null); model.set('cargo','');
            model.set('precio',0); model.set('precioBase',0); model.set('retencionAplica',false);
        });
        editorconceptos=validarEditorCatalogo(editorconceptos,'servicio','idconcepto');
        editorcargo=validarEditorCatalogo(editorcargo,'cargo','idcargo');

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

        /* Cada cambio de fila vuelve a calcular el resumen. */
        var erpResumenDataSourceBound = false;

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

        /* Reforzamos los puntos donde cambian los importes. */
        var erpRecalcularResumen = function(model)
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
        var citaProgramada = null;

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

            $.ajax({
                url:'<?=base_url?>/Bitacoras/GuardarOrden',
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

        /* =========================================================
         * UBICACIONES / LEAFLET + OPENSTREETMAP
         * ========================================================= */
        var erpUbicacionTipo = 'contacto';
        var erpUbicacionSeleccionada = null;
        var erpMapaUbicacion = null;
        var erpMarcadorUbicacion = null;

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

            $.ajax(
            {
                url:'https://nominatim.openstreetmap.org/search',
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
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= JS ?>/funciones.js"></script>
</html>
