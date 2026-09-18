    /*    
         ERPGrid.Calculation.js
         Version 1.0
         Compatible Kendo UI 2015   
    */

    (function(window,$){

        if(typeof ERPGrid==="undefined")
            return;

        ERPGrid.DefaultCalculation=
        {
            quantityField:"cantidad",
            priceField:"precio",
            discountField:"descuento",
            taxField:"porciva",
            subtotalField:"subtotal",
            discountAmountField:"importedescuento",
            taxAmountField:"iva",
            totalField:"total",
            decimals:2
        };

    })(window,jQuery);