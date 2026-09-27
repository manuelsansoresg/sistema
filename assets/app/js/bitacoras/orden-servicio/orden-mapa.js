(function (window, $) {
    'use strict';
    var app = window.OrdenApp = window.OrdenApp || {};

    app.Mapa = {
        /**
         * Restaura coordenadas persistidas. No geocodifica porque vchcampo1/2
         * ya representan el punto exacto elegido por el usuario.
         */
        cargarUbicacion: function (tipo, ubicacion) {
            ubicacion = ubicacion || {};
            var contacto = tipo === 'contacto';
            $('#' + (contacto ? 'txtcontactolat' : 'txtterminolat')).val(ubicacion.lat == null ? '' : ubicacion.lat);
            $('#' + (contacto ? 'txtcontactolon' : 'txtterminolon')).val(ubicacion.lon == null ? '' : ubicacion.lon);
            if (ubicacion.direccion) $('#' + (contacto ? 'txtcontacto' : 'txttermino')).val(ubicacion.direccion);
        },
        obtenerUbicacion: function (tipo) {
            return app.Form ? app.Form.obtenerUbicacion(tipo) : null;
        },
        abrir: function (tipo) {
            if (typeof abrirModalUbicacion === 'function') abrirModalUbicacion(tipo);
        }
    };
}(window, jQuery));
