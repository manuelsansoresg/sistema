<!doctype html>
<html lang="es">
<head>
    <meta charset="<?= SITE_CHARSET ?>">
    <?= get_favicon(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Órdenes de servicio | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= BOOTSTRAP ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= DIST ?>/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= PLUGINS ?>/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?= PLUGINS ?>/datatables/extensions/Responsive/css/dataTables.responsive.css">
    <link rel="stylesheet" href="<?= ASSETS ?>/app/css/erp/orden-servicio.css">
    <style>
        .ordenes-page{padding:18px;background:#f4f6f9;min-height:100vh}.ordenes-card{background:#fff;border:1px solid #dfe3e8;border-radius:4px;padding:18px}
        .ordenes-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}.ordenes-table-wrap{overflow-x:auto}
        #tablaOrdenes{width:100%!important;white-space:nowrap}.orden-status{padding:3px 8px;border-radius:10px;background:#eef2f6;font-size:11px}
    </style>
</head>
<body class="ordenes-page">
    <section class="ordenes-card">
        <div class="ordenes-toolbar">
            <h3><i class="fa fa-list-alt"></i> Órdenes de servicio</h3>
            <a class="btn btn-primary" href="<?= base_url ?>/Bitacoras/NuevoServicio"><i class="fa fa-plus"></i> Nueva orden</a>
        </div>
        <div class="ordenes-table-wrap">
            <table id="tablaOrdenes" class="table table-striped table-bordered table-hover" aria-label="Listado de órdenes de servicio">
                <thead><tr>
                    <th>Folio</th><th>BIS</th><th>Servicio</th><th>Fecha</th><th>Cliente</th><th>Solicitante</th>
                    <th>Asegurado</th><th>Vehículo</th><th>Placas</th><th>Operador</th><th>Grúa</th>
                    <th>Contacto</th><th>Destino</th><th>Local/Foráneo</th><th>Estatus</th><th>Total</th><th>Acciones</th>
                </tr></thead>
            </table>
        </div>
    </section>
    <script src="<?= PLUGINS ?>/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?= BOOTSTRAP ?>/js/bootstrap.min.js"></script>
    <script src="<?= PLUGINS ?>/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= PLUGINS ?>/datatables/dataTables.bootstrap.min.js"></script>
    <script>window.ORDEN_CONFIG={baseUrl:<?= json_encode(base_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>};</script>
    <script src="<?= ASSETS ?>/app/js/bitacoras/orden-servicio/orden-api.js"></script>
    <script src="<?= ASSETS ?>/app/js/bitacoras/orden-servicio/orden-listado.js"></script>
</body>
</html>
