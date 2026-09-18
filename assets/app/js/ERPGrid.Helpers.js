    /*
     ERP Framework
     ERPGrid.Helpers.js
     Compatible Kendo UI 2015    
    */
    (function(window,$){

        if(typeof ERPGrid==="undefined")
            return;

        ERPGrid.Helpers={};

    })(window,jQuery);
    
    ERPGrid.Helpers.value=function(selector)
    {
        if(!selector) return null;

        var control=$(selector);

        if(control.length==0) return null;

        // Kendo Combo
        var combo=control.data("kendoComboBox");

        if(combo) return combo.value();

        // NumericTextBox
        var numeric=control.data("kendoNumericTextBox");

        if(numeric) return numeric.value();

        // DatePicker
        var fecha=control.data("kendoDatePicker");

        if(fecha) return fecha.value();

        return control.val();
    };
    ERPGrid.Helpers.checked=function(selector)
    {
        return $(selector).is(":checked");
    };
    ERPGrid.Helpers.radio=function(selector)
    {
        return $(selector+":checked").val();
    };
    ERPGrid.Helpers.rowValue=function(model,campo)
    {
        if(!model) return null;

        return model.get(campo);
    };
    ERPGrid.Helpers.toNumber=function(valor)
    {
        if(valor==null) return 0;

        valor=parseFloat(valor);

        if(isNaN(valor)) return 0;

        return valor;

    };
    ERPGrid.Helpers.round=function(valor,decimales)
    {
        decimales=decimales||2;

        return parseFloat(valor.toFixed(decimales));
    };
    ERPGrid.Helpers.currency=function(valor)
    {
        valor=this.toNumber(valor);

        return kendo.toString(valor,"c2");
    };
    ERPGrid.Helpers.empty=function(valor)
    {
        return valor==null || valor=="" || valor==undefined;
    };
    ERPGrid.Helpers.guid=function()
    {
        return kendo.guid();
    };
    ERPGrid.Helpers.resolveParameters=function(parameters, options)
    {
        var result={};

        $.each(parameters,function(key,value)
        {
            // Función
            if($.isFunction(value))
            {
                result[key]=value(options);
                return;
            }

            // Campo del modelo (@campo)
            if(typeof value==="string" && value.charAt(0)=="@")
            {
                var field=value.substr(1);

                result[key]=options.model.get(field);

                return;
            }

            // Selector jQuery
            if(typeof value==="string" && value.charAt(0)=="#")
            {
                var obj=$(value);

                if(obj.is(":checkbox"))
                    result[key]=obj.is(":checked");
                else
                    result[key]=obj.val();

                return;
            }

            result[key]=value;

        });

        return result;
    };

    /*ERPGrid.Helpers.resolveParameters=function(parameters,options)
    {
        var data={};

        if(!parameters) return data;
        if($.isFunction(parameters))
        { 
            //return parameters(options.model.toJSON());    
            parameters = parameters(options);    
        }

        $.each(parameters,function(key,value)
        {           

            // Campo del modelo
            if(typeof value==="string" && value.indexOf("@")==0)
            {
                data[key]=options.model.get(value.substring(1));
                return;
            }
            if(typeof value==="string" && value.charAt(0)=="#")
            {
                data[key]=$(value).val();
                return;
            }
            // Checkbox
            if(typeof value==="string" && value.indexOf(":checked")>0)
            {
                var selector=value.replace(":checked","");
                data[key]=$(selector).is(":checked");
                return;
            }
            if(typeof value==="string" && value.indexOf(":checked")>0)
            {
                var s=value.replace(":checked","");
                data[key]=$(s).is(":checked")?1:0;
                return;
            }
            // Selector HTML
            if(typeof value==="string" && value.charAt(0)=="@")
            {
                data[key]=options.model.get(value.substring(1));
                return;
            }
            if(typeof value==="string" && value.indexOf("#")==0)
            {
                data[key]=$(value).val();
                return;
            }
            
            // Función
            if($.isFunction(value))
            {                                 
                data[key]=value(options);
                return;
            }
            data[key]=value;
        });

        return data;
    };*/
    ERPGrid.Helpers.highlight=function(text,busca)
    {
        return text.replace(new RegExp(busca,"ig"),"<b>$&</b>");
    }