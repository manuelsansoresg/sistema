(function (window, $) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};
    function texto(valor) { return $('<div>').text(valor == null ? '' : valor).html(); }
    function moneda(valor) { return '$' + Number(valor || 0).toLocaleString('es-MX',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function estatus(valor) {
        var nombres={A:'Activa', C:'Cancelada', T:'Terminada'};
        return '<span class="orden-status">' + texto(nombres[valor] || valor || 'Sin estatus') + '</span>';
    }

    app.Listado = {
        init:function () {
            $('#tablaOrdenes').DataTable({
                serverSide:true, processing:true,
                ajax:function (parametros, callback) {
                    app.Api.obtenerOrdenes(parametros).done(function (respuesta) {
                        var pagina=respuesta && respuesta.status ? respuesta.data : {};
                        callback({draw:parametros.draw, data:pagina.items || [],
                            recordsTotal:pagina.total || 0, recordsFiltered:pagina.filtrados || 0});
                    }).fail(function () { callback({draw:parametros.draw, data:[], recordsTotal:0, recordsFiltered:0}); });
                },
                order:[[3,'desc']], pageLength:25, scrollX:true,
                language:{processing:'Cargando...', search:'Buscar:', lengthMenu:'Mostrar _MENU_',
                    info:'Mostrando _START_ a _END_ de _TOTAL_', paginate:{previous:'Anterior',next:'Siguiente'}, zeroRecords:'No se encontraron órdenes'},
                columns:[
                    {data:'folio',render:texto},{data:'pkvchid_bis',render:texto},{data:'intno_serv'},
                    {data:'dtfecha_serv',render:texto},{data:'cliente',render:texto},{data:'solicitante',render:texto},
                    {data:'asegurado',render:texto},{data:'vehiculo',render:texto},{data:'placas',render:texto},
                    {data:'operador',render:texto},{data:'grua',render:texto},{data:'contacto',render:texto},
                    {data:'destino',render:texto},{data:'local_foraneo',render:function(v){return v === 'L' ? 'Local' : 'Foráneo';}},
                    {data:'estatus',render:estatus},{data:'total',className:'text-right',render:moneda},
                    {data:'pkvchid_bis',orderable:false,searchable:false,render:function (bis) {
                        return '<a class="btn btn-xs btn-primary" href="' + window.ORDEN_CONFIG.baseUrl + '/Bitacoras/EditarServicio/' + encodeURIComponent(bis) + '"><i class="fa fa-pencil"></i> Editar</a>';
                    }}
                ]
            });
        }
    };
    $(function () { app.Listado.init(); });
}(window, jQuery));
