    function OpenWindows(qurl,qTitulo,qancho,qalto)
    {    
        var param = "toolbar=0,scrollbars=0,location=0,statusbar=0,directories=no,menubar=0,resizable=1,width="+qancho+",height="+qalto+",left = 390,top = 50";
        window.open(qurl, qTitulo,param);        
    }/*
    function OpenModal(titulo, url, ancho = 'xl') 
    {
        $("#modalTitulo").html(titulo);
        $("#modalFrame").attr("src", url);
        let dialog = $("#modalGeneral .modal-dialog");

        dialog.removeClass(
            'modal-sm modal-lg modal-xxl modal-full'
        );

        switch(ancho)
        {
            case 'sm':
                dialog.addClass('modal-sm');
            break;
            case 'lg':
                dialog.addClass('modal-lg');
            break;
            case 'xl':
                dialog.addClass('modal-xxl');
            break;
            case 'full':
                dialog.addClass('modal-full');
            break;
        }

        $("#modalGeneral").modal('show');
    }*/
    /*Eventos numericos*/
    function AcceptNum(evt,ID,Negativo,Porcentaje)
    {
        var nav4 = window.Event ? true : false;
        var key = nav4 ? evt.which : evt.keyCode;
        
        if($('#'+ID).val())
        {
            var Cadena=$('#'+ID).val();   //xGetElementById(ID).value;
            if(key==45 && Negativo){//si es - y esta activado para aceptar numeros negativos
                if(Cadena.indexOf("-")==-1)//si no encuentra -, lo colocamos al inicio, retornamos falso para que no se el incluya - donde presionamos
                    $('#'+ID).val()="-"+Cadena;
                return false;
            }
            else if(key==46){//si es punto
                if(Cadena.length==0)//si la cadena tiene longitud 0 no puedo meter .
                    return false;
                if(Cadena.indexOf(".")==-1)//solo debe haber un . en la cadena
                    return true;
                return false;
            }
            else if(key==48){//si es cero
                if(Cadena.length==1 && Cadena.indexOf("0")==0)//si hay un caracter en la cadena y ese caracter es cero, no puedo meter otro cero
                    return false;
                return true;
            }
            else if(key==37 && Porcentaje){//si es % y esta activado porcentaje
                if(Cadena.indexOf("%")==-1)
                    $('#'+ID).val()=Cadena+"%";
                return false;
            }
        }
        if(key==13) return false;//necesario para evitar recargas de pagina (ocurre ocacionalmente al presionar enter en el input text de una tabla. ejemplo presupuesto->formulacion)
        return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
    } 
    /*Formato con comas*/
    function FormatearNumero(num)
    {
        if(!num)
            num=0;
        num = num.toString().replace(/$|,/g,'');
        if(isNaN(num))
            num = "0";
        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num*100+0.50000000001);
        cents = num%100;
        num = Math.floor(num/100).toString();
        if(cents<10)
        cents = "0" + cents;
        for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
            num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
            
        return (((sign)?'':'-') + num + '.' + cents);
    }  
    /*
        El numero y numero de decimales a contemplar
        Ejemplo : 1236.12 y lo conviernte
        var num = numberFormat(1236.12,4);
        resultado : 1236.1200
    
    */
    function numberFormat(num,dec)
    {
        if(xTrim(String(num))=="")
            return "0.00";
        var N=redondear(num,dec);
        if(N==0)
            return "0.00";
        var aux=String(N).split(".");
        if(aux.length==1)
            return N+".00";
        else if(aux.length==2)
            if(aux[1].length==1)
                return N+"0";
        return String(N);
    }
    /*Redondear numeros*/
    function redondear(num,dec)
    {
        var numstr=String(num);//Ej. redondear(3.31545,2)

        if(numstr.indexOf(".") == -1)
        {
            numstr = numstr + ".";
            for(nfi=0;nfi<dec;nfi++)
                numstr = numstr + "0";
        }

        partes=numstr.split("."); //dividimos por el punto para separar el entero del decimal Ej. 3|31545

        if (partes[1].length>dec)
        {
            comadecimal="0."+partes[1];
            partes[1]=partes[1].substr(0,dec+1); //tomamos los dec+1 dígitos de la parte decimal Ej.315

            //truncamos las parte decimal a dec dígitos
            truncamiento=comadecimal.substr(0,dec+2);
            decimal=parseFloat(truncamiento);

            if (parseInt(partes[1].charAt(dec))>=5) //si el siguiente a dec >= 5 Ej. el 3er caracter de 315 es 5
                decimal=decimal+(1/(Math.pow(10,dec))); //incrementamos en 1 la parte decimal Ej. 0.32

            //sumamos la parte entera más la decimal Ej. 3+0.32=3.32
            numstr=parseFloat(parseInt(partes[0],10)+decimal);
        }
        return (parseFloat(numstr));
    }
    /*Limite de decimales agregar*/
    function limitDecimalPlaces(e, count) 
    {
        if (e.target.value.indexOf('.') == -1) { return; }
        if ((e.target.value.length - e.target.value.indexOf('.')) > count) 
        {
            e.target.value = parseFloat(e.target.value).toFixed(count);
        }
    }
    /*Solo numeros*/
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
    /*Quitar los espacio*/
    function xTrim(s) 
    {
        return s.replace(/^\s+|\s+$/g, '');
    }
    
    function FormatearDBFecha(qfecha)
    {
        let fecha = new Date(qfecha);
        
        // Extraer los componentes de la fecha
        let dia = fecha.getDate();
        let mes = fecha.getMonth() + 1; // Agregar 1 porque los meses comienzan en 0
        let anio = fecha.getFullYear();

        // Crear la fecha en el formato deseado (dd/mm/aaaa)
        let fechaFormateada = completarCodigoCeros(dia,2) + '/' + completarCodigoCeros(mes,2) + '/' + anio;
        
        return fechaFormateada;
    }
    
    function completarCodigoCeros(cadena,tamano)
    {
    //     if(tamano<completarCodigoCeros_CantidadMin)
    //         tamano=completarCodigoCeros_CantidadMin;
        if(!tamano)
            tamano=3;
        cadena=String(cadena);
        var p=new String("");
        for(;tamano>cadena.length;tamano--)
            p+='0';
        return (p+=cadena);
    }
    
    /* Example
        <input type="number" oninput="limitDecimalPlaces(event, 2)" onkeypress="return isNumberKey(event)"  />
    */
    /*Limpiar controles*/
    function resetform() 
    {
        //$("form select").each(function() { this.selectedIndex = 0 });
        //$("form input[type=text] , form textarea").each(function() { this.value = '' });
        //Aplicamos el sentido contrario y limpiamos los inputs
        $('input[type=text]').each(function () {
            //$(this).attr('disabled', true)
            $(this).val('')
        })
        
    }
   
    window._confirm = function ($message = "", $func = "", $param = []) 
    {
        if ($func != "" && typeof window[$func] == "function") 
        {
            var modal_el = $("#confirm_modal");
            modal_el.find(".modal-body").html($message);                   
            modal_el.modal("show");        
            modal_el.find("#confirm-btn").off("click").on("click", function (e) 
            {
                e.preventDefault();                
                if ($param.length > 0 && !!$.isArray($param))
                    window[$func].apply(this, $param);
                else window[$func]($param);                    
                    modal_el.modal("hide");
                    $('.modal-backdrop').remove();
            });        
        } else 
        {
            alert("Function does not exists.");
        }
    }