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
    <link rel="stylesheet" href="<?= ASSETS ?>/app/css/erp/orden-servicio-grid-fix.css">
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
    $idsuc = 25;
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
    

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= JS ?>/funciones.js"></script>
    <script>
        window.ORDEN_CONFIG = {
            baseUrl: <?= json_encode(base_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            vehiculos: <?= json_encode(json_decode($tipclasif), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            empleados: <?= json_encode(json_decode($Empleado), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            claves: <?= json_encode(json_decode($Claves), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            iva: <?= json_encode($dciva) ?>,
            retencion: <?= json_encode($dcret) ?>
        };
    </script>
    <script src="<?= ASSETS ?>/app/js/bitacoras/orden-servicio/orden-api.js"></script>
    <script src="<?= ASSETS ?>/app/js/bitacoras/orden-servicio/orden-ui.js"></script>
    <script src="<?= ASSETS ?>/app/js/bitacoras/orden-servicio/orden-servicio.js"></script>
</html>
