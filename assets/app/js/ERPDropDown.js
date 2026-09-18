    function ERPDropDown(config)
    {
        this.settings=$.extend(true,
        {
            url:null,
            valueField:"id",
            textField:"text",
            valueModel:null,
            textModel:null,
            parameters:{},
            mapping:{},
            optionLabel:"Seleccione...",
            autoBind:true,
            afterSelect:null
        },config);
    }
    ERPDropDown.prototype.editor=function()
    {
        var self=this;

        return function(container,options)
        {
            var input = $("<input/>").appendTo(container);
    
            /*$("<input/>").appendTo(container).kendoDropDownList({*/
            input.kendoDropDownList({
                autoBind:self.settings.autoBind,
                optionLabel:self.settings.optionLabel,
                dataTextField:self.settings.textField,
                dataValueField:self.settings.valueField,
                filter:"contains",          // <- Activa búsqueda
                ignoreCase:true,                
                highlightFirst:true,
                dataSource:
                {                    
                    transport:
                    {
                        read:
                        {
                            url:self.settings.url,
                            type:"POST",
                            dataType:"json",
                            data:function()
                            {
                                 return ERPGrid.Helpers.resolveParameters(self.settings.parameters,options);
                            }
                        }
                    }
                },
                select:function(e)
                {
                    var item=this.dataItem(e.item);
                    self.select(item,options);
                },
                change:function(e)
                {
                    var combo=this;

                    setTimeout(function(){
                        grid.closeCell();
                        grid.nextCell();
                    },30);
                }          
            });
             
            var ddl = input.data("kendoDropDownList");

            setTimeout(function () 
            {
                ddl.focus();
                ddl.open();
            }, 10);

        };
    };
    ERPDropDown.prototype.select=function(item,options)
    {
        var self=this;
        if(!item) return;

        // Valor
        if(self.settings.valueModel)
            options.model.set(self.settings.valueModel,item[self.settings.valueField]);

        // Texto
        if(self.settings.textModel)
            options.model.set(self.settings.textModel,item[self.settings.textField]);

        // Mapping
        $.each(self.settings.mapping,function(destino,origen)
        {
            options.model.set(destino,item[origen]);
        });

        if($.isFunction(self.settings.afterSelect))
            self.settings.afterSelect(item,options);        

    };