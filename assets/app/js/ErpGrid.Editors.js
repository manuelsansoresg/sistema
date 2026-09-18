    /* 
        ERPGrid Editors
        Compatible Kendo UI 2015
    */
    (function () 
    {
        if (typeof ERPGrid === "undefined")
            return;

        ERPGrid.prototype.editors = {};
    })();
    
    ERPGrid.prototype.createComboEditor=function(config)
    {
        var self=this;

        return function(container,options)
        {                
            var input=$("<input/>",{ name:options.field });
            input.appendTo(container);
                        
            input.kendoComboBox(
            {
                autoBind: true,
                suggest: true,
                highlightFirst: true,               
                filter: "contains",
                placeholder:"Buscar item...",
                ignoreCase:true,
                minLength:1,
                delay:150,
                dataTextField:config.textField,
                dataValueField:config.valueField,
                dataSource:
                {
                    serverFiltering:false,
                    transport:
                    {
                        read:
                        {
                            url:config.url,
                            type:"POST",
                            dataType:"json",                            
                            data:function()
                            {
                                var p = ERPGrid.Helpers.resolveParameters(config.parameters,options);
                                //console.log("Parametros enviados:");
                                //console.table(p);
                                return p;
                            }
                        }
                    },
                    schema:
                    {
                        data:function(response){
                            return response;
                        }
                    }
                }, 
                dataBound:function()
                {
                    // Solo si el editor es Servicio
                    if(config.autoSelectFirst)
                    {
                        if(this.dataSource.total()>0)
                        {
                            this.select(0);
                            var item=this.dataItem(0);
                            self.comboSelected(item,options,config);
                        }
                    }
                },               
                open:function(e){

                    var combo=this;
                    var popup=combo.popup.element;

                    if(popup.find(".erp-filter").length) return;

                    popup.prepend(
                        '<div class="erp-filter">'+
                            '<input class="k-textbox" placeholder="Buscar...">'+
                        '</div>'
                    );
                    popup.find(".erp-filter input").on("keyup",function()
                    {
                        var texto=$(this).val();

                        combo.dataSource.filter({
                            field: combo.options.dataTextField,
                            operator:"contains",
                            value:texto
                        });

                    }).focus();
                },
                select:function(e)
                {
                    var item=this.dataItem(e.item);
                    self.comboSelected(item,options,config);
                },
                change:function()
                {
                    var item=this.dataItem();
                    if(item){
                        self.comboSelected(item,options,config);
                    }
                    /*setTimeout(function()
                    {
                        self.navigator.next();
                    },20);*/
                }                              
            });           
        };
    };
    ERPGrid.prototype.comboSelected=function(item,options,config)
    {
        if(!item) return;

        options.model.set(config.valueModel,item[config.valueField]);

        options.model.set(config.textModel,item[config.textField]);

        if(config.mapping)
        {
            $.each(config.mapping,function(campo,valor)
            {
                options.model.set(campo,item[valor]);
            });
        }

        if($.isFunction(config.afterSelect))
        {
            config.afterSelect(item,options);
        }
    };