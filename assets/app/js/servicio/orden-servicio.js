    (function(window, $)
    {
        "use strict";
        window.ERPOrdernServicio =
        {
            guardar: function()
            {
                var self = this;
                /* VALIDACIÓN */

                if (typeof ERPOrden === "undefined")
                {
                    console.error("ERPOrden no está disponible.");
                    return;
                }
                /* OBTENER INFORMACIÓN DEL FORMULARIO */ 
                var orden = ERPOrden.get();
                if (!orden)
                {
                    Swal.fire({
                        icon: "warning",
                        title: "Orden vacía",
                        text:"No fue posible obtener la información del servicio."
                    });

                    return;
                }
                /* TIPO DE OPERACIÓN */
                orden.operacion =
                {
                    tipo: "NUEVO",
                    folioRaiz: null
                };
                /* MOSTRAR INFORMACIÓN EN CONSOLA */
                console.log("ERPOrden",orden);
                /* ENVIAR*/
                self.enviar(orden);
            },
            enviar: function(orden)
            {
                var self = this;
                var $btn =$("#btnGenerar");
                /* Evitar doble click.*/
                if ($btn.data("guardando")) {   return; }

                $btn.data("guardando",true);
                $btn.prop("disabled",true);
                /* AJAX */

                $.ajax(
                {
                    url:"<?=base_url?>/Bitacoras/Generar",
                    type:"POST",
                    data:JSON.stringify(orden),
                    contentType:"application/json; charset=utf-8",
                    dataType:"json",
                    beforeSend: function()
                    {
                        if (typeof ERPUI !== "undefined" && typeof ERPUI.block === "function")
                        {
                            ERPUI.block("Guardando servicio...");
                        }
                    },
                    success: function(response)
                    {
                        console.log("RESPUESTA GUARDAR",response);
                        if (!response || response.status !== true)
                        {
                            Swal.fire({
                                icon:"error",
                                title:"No se pudo guardar",
                                text:response && response.message ? response.message : "Error desconocido."
                            });
                            return;
                        }

                        var data = response.data;
                        /* SERVICIO GENERADO */
                        Swal.fire({
                            icon:"success",
                            title:"Servicio generado",
                            html:"<div class='text-left'>" +
                                    "<div>" +
                                        "<strong>Folio:</strong> " + data.vchfolio +
                                    "</div>" +
                                    "<div>" +
                                        "<strong>BIS:</strong> " + data.pkvchid_bis +
                                    "</div>" +
                                    "<div>" + 
                                        "<strong>Clave:</strong> " + data.vchbitacora +
                                    "</div>" +
                                    "<div>" +
                                        "<strong>Servicio:</strong> " + data.intno_serv +
                                    "</div>" +
                                "</div>"
                        });
                        /* GUARDAR EN ESTADO*/
                        if (window.ERPMonitor &&ERPMonitor.state)
                        {
                            ERPMonitor.state.selectedService = data;
                        }
                        /* Evento para que otras partes  del monitor puedan reaccionar.*/
                        $(document).trigger("erp:orden:guardada",[data]);
                    },
                    error: function(xhr)
                    {
                        console.error("ERROR HTTP",xhr.status,xhr.responseText);
                        var response = null;
                        try
                        {
                            response = JSON.parse(xhr.responseText);
                        }
                        catch(e)
                        {
                            /* El servidor pudo haber  regresado HTML/PHP warning.*/
                        }
                        Swal.fire({
                            icon:"error",
                            title:"Error al guardar",
                            text:response && response.message ? response.message : "El servidor no pudo procesar el servicio."
                        });
                    },
                    complete: function()
                    {
                        $btn.data("guardando",false);
                        $btn.prop("disabled",false);
                        if (typeof ERPUI !== "undefined" && typeof ERPUI.unblock === "function")
                        {
                            ERPUI.unblock();
                        }
                    }
                });
            }
        };
        /* BOTÓN GENERAR */

        $(document).off("click.erpOrden","#btnGenerar").on("click.erpOrden","#btnGenerar",function(e)
        {
            e.preventDefault();
            ERPOrden.guardar();
        });


    })(window, jQuery);