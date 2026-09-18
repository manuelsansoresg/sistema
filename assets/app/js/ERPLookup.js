    function ERPLookup(config)
    {
        this.settings=$.extend(true,
        {            
            id: "lookup_" + kendo.guid(),
            title:"Catálogo",
            width:450,
            height:430,
            url:null,
            columns:[],
            parameters:{},
            modal:true,
            selectable:"row",
            autoFocusSearch: true,
            onSelect:null
        },config);

        this.window=null;
        this.grid=null;
        this.ds=null;
        this.cache={};
    }
    
    this.originalData = [];   // Todos los registros del servidor
    this.filteredData = [];   // Resultado del filtro actual

    ERPLookup.prototype.init=function()
    {
        /*this.createWindow();
        this.createToolbar();
        this.createGrid();
        this.bindKeyboard();*/

        this.createHtml();
        this.createWindow();
        this.createToolbar();
        this.createsearch();
        this.createGrid();
        this.createFooter();  
        
        //console.log($("#"+this.settings.id+" .erpLookupToolbar").length);      
        
    };
    ERPLookup.prototype.createHtml=function()
    {
        var self=this;
        var html='';
        html+='<div id="'+self.settings.id+'" class="erpLookup">';
        html+='    <div class="erpLookupToolbar"></div>';
        html+='    <div class="erpLookupGrid"></div>';
        html+='    <div class="erpLookupFooter"></div>';
        html+='</div>';
        $("body").append(html);
    };

    /*ERPLookup.prototype.createWindow=function()
    {
        var self=this;
        var id="lookup_"+kendo.guid();
        self.settings.id=id;

        $("body").append
        (
            '<div id="'+id+'">'+
                '<div class="erpLookupSearchBox">'+
                    '<i class="fa fa-search"></i>'+
                    '<input class="erpLookupSearch placeholder="Buscar...">'+
                    '<div class="left">'+
                    '<span class="erpLookupCount"></span>'+
                    '</div>' +
                    '<div class="right">' +
                    '    F4 Buscar |' +
                    '    Enter Seleccionar |' +
                    '    Esc Salir' +
                    '</div>' +
                '</div>'+
                '<div class="erpLookupGrid"></div>'+
            '</div>'
        );

        $("#"+id).kendoWindow(
        {
            title:self.settings.title,
            modal:true,
            visible:false,
            width:self.settings.width,
            height:self.settings.height,
            resizable:true,
            actions:["Close"]
            

        });

        self.window=$("#"+id).data("kendoWindow");        
    };*/
    ERPLookup.prototype.createWindow=function()
    {
        var self=this;

        self.window=$("#"+self.settings.id)
            .kendoWindow({
                title:self.settings.title,
                modal:true,
                visible:false,
                width:self.settings.width,
                height:self.settings.height,                
                resize:function()
                {
                    self.resize();
                }
            })
            .data("kendoWindow");

        //console.log(self.window);        
        self.window.wrapper.addClass("erpLookupWindow");
    };
    ERPLookup.prototype.createToolbar=function()
    {
        var self=this;
        var html='';
        html+='<div class="erpLookupSearchBox">';
        html+='    <i class="fa fa-search"></i>';
        html+='    <input class="erpLookupSearch" ';
        html+='           placeholder="Buscar...">';
        html+='</div>';

        $("#"+self.settings.id+" .erpLookupToolbar").html(html);
        //console.log($("#"+this.settings.id).html());
        // Guardar referencia
        self.txtSearch=$("#"+self.settings.id+" .erpLookupSearch");

        self.txtSearch.on("keyup",function()
        {
            self.filter($(this).val());
        });
    }
    ERPLookup.prototype.createGrid=function()
    {
        var self=this;

        self.ds=new kendo.data.DataSource(
        {
            transport:
            {
                read:
                {
                    url:self.settings.url,
                    type:"POST",
                    dataType:"json",
                    data:function(){
                        return self.resolveParameters();
                    }                                 
                }
            },
            requestEnd:function(e)
            {
                self.originalData=e.response;
                self.filteredData=e.response;
            },
            pageSize:25
        });

        $("#"+self.settings.id+" .erpLookupGrid").kendoGrid(
        {
            dataSource:self.ds,
            selectable:"row",
            scrollable: true,
            sortable:true,
            filterable:false,
            pageable:true,
            resizable:true,
            autocomplete:true,
            columns:self.settings.columns,
            change:function()
            {
                self.select();
            },
            dataBound:function()
            {
                /*self.grid.tbody.off("dblclick").on("dblclick","tr",function()
                {
                    self.grid.select(this);
                    self.select();
                });*/
               
            }
            
        });

        self.grid=$("#"+self.settings.id+" .erpLookupGrid").data("kendoGrid"); 
        self.resize();       
    };
    ERPLookup.prototype.resize=function()
    {
        var self=this;
        var total=self.window.element.height();
        var toolbar=self.window.element.find(".erpLookupToolbar").outerHeight();
        var footer=self.window.element.find(".erpLookupFooter").outerHeight();
        var disponible=total-toolbar-footer;
        self.window.element.find(".erpLookupGrid").height(disponible);
        self.grid.resize();
    };
    ERPLookup.prototype.createFooter=function()
    {
        var self=this;
        var html='';
        html+='<div class="erpLookupFooterLeft">';
        html+='0 registros';
        html+='</div>';
        html+='<div class="erpLookupFooterRight">';
        html+='Enter Seleccionar';
        html+=' | F4 Buscar';
        html+=' | Esc Salir';
        html+='</div>';

        $("#"+self.settings.id+" .erpLookupFooter").html(html);
    }
    ERPLookup.prototype.open=function(options)
    {
        var self=this;

        self.ds.read();
        self.window.center();
        self.window.open(); 
        self.resize();  
        
        if(self.settings.autoFocusSearch !== false)
        {
            setTimeout(function(){
                self.txtSearch.val("");
                self.filter("");
                self.txtSearch.focus();
            },30);
        }                  
    };    
    ERPLookup.prototype.close=function()
    {
        this.window.close();
    };        
    ERPLookup.prototype.createsearch=function()
    {
        var self=this;
        var txt= $("#"+self.settings.id).find(".erpLookupSearch");
        
        txt.on("keyup",function()
        {
            //console.log("Buscando:",$(this).val());
            self.filter($(this).val());

        });       
        txt.on("focus",function()
        {
            $(this).select();
        });
    };    
    ERPLookup.prototype.filter=function(texto)
    {
        var self=this;
        texto=$.trim(texto).toLowerCase();

        if(texto==="")
        {
            self.ds.data(self.originalData);
            return;
        }

        var resultado=$.grep(self.originalData,function(item)
        {
            var encontrado=false;
            $.each(self.settings.searchFields,function(i,campo)
            {
                var valor=item[campo];

                if(valor===null || valor===undefined)
                    return;

                valor=String(valor).toLowerCase();

                if(valor.indexOf(texto)>=0)
                {
                    encontrado=true;
                    return false;
                }
            });

            return encontrado;

        });

        self.ds.data(resultado);

        $("#"+self.settings.id).find(".erpLookupCount").text(resultado.length+" registros");

    };
    ERPLookup.prototype.bindKeyboard=function()
    {
        var self=this;
        
        $("#"+self.settings.id).on("keydown",function(e)
        {
            switch(e.keyCode)
            {
                case 13:
                    e.preventDefault();
                    self.select();
                break;
                case 27:
                    self.close();
                break;
                case 38:
                    self.move(-1);
                break;
                case 40:
                    self.move(1);
                break;
            }
        });
    };
    ERPLookup.prototype.move=function(dir)
    {
        var self=this;
        var rows=self.grid.tbody.find("tr");
        var row=self.grid.select();
        var index=row.index();
        index+=dir;

        if(index<0) index=0;

        if(index>=rows.length) index=rows.length-1;

        self.grid.select(rows.eq(index));
    };
    ERPLookup.prototype.focusSearch=function()
    {
        $("#"+this.settings.id).find(".erpLookupSearch").focus().select();
    };
    ERPLookup.prototype.editor=function(config)
    {
        var lookup=this;
        return function(container,options)
        {
            var input=$("<input/>",
            {
                class:"erpLookupInput",
            });

            input.appendTo(container);

            if(options.model.get(config.textModel))
                input.val(options.model.get(config.textModel));
            
            container.off("dblclick.erpLookup").on("dblclick.erpLookup", function(e)
            {
                e.preventDefault();
                e.stopPropagation();

                lookup.openEditor(options, config, input);
            });
            
            /*input.on("focus", function()
            {
                lookup.openEditor(options, config, input);
            });*/

            input.on("keydown",function(e)
            {
                if(e.keyCode==115){ //F4
                    e.preventDefault();
                    //console.log("Modelo completo:", options.model.toJSON());
                    //console.log("idcliente:", options.model.get("idcliente"));
                    lookup.openEditor(options,config,input);                    
                }
            });
        };
    };
    ERPLookup.prototype.openEditor=function(options,config,input)
    {
        this.editorOptions=options;
        this.editorConfig=config;
        this.editorInput=input;
        this.open(options);
    };
    /*ERPLookup.prototype.applyMapping=function(item)
    {
        var model=this.editorOptions.model;
        var config=this.editorConfig;
        
        model.set(config.valueModel,item.id);
        model.set(config.textModel,item.text);

        if(config.mapping)
        {
            $.each(config.mapping,function(modelField,jsonField)
            {
                model.set(modelField,item[jsonField]);
            });
        }
        this.editorInput.val(item.text);

        //this.gridOwner.grid.refresh();
    };*/
    ERPLookup.prototype.select=function()
    {
        var self=this;
        var item=self.grid.dataItem(self.grid.select());

        if(!item) return;

        // Guardar ID
        self.editorOptions.model.set(self.editorConfig.valueModel,item[self.editorConfig.valueField]);

        // Guardar descripción
        self.editorOptions.model.set(self.editorConfig.textModel,item[self.editorConfig.textField]);

        // Mapping adicional
        if(self.settings.mapping)
        {
            $.each(self.settings.mapping,function(destino,origen)
            {
                self.editorOptions.model.set(destino,item[origen]);
            });
        }

        self.editorInput.val(item[self.editorConfig.textField]);
        //self.editorInput.val(item[self.editorConfig.valueModel]);

        self.close();
        this.erpGrid.navigator.next();
    };
    ERPLookup.prototype.loadData=function(options)
    {
        var self=this;

        /*var key=JSON.stringify(ERPGrid.Helpers.resolveParameters(self.settings.parameters));

        if(self.cache[key])
        {
            callback(self.cache[key]);
            return;
        }*/

        $.ajax(
        {
            url:self.settings.url,
            type:"POST",
            data:self.resolveParameters(options),
            dataType:"json",
            success:function(r)
            {
                 self.originalData=data;
                 self.filteredData=data;
                 self.ds.data(data);
                 self.window.open();

                 /*if(callback)
                    callback(data);*/

                //self.cache[key]=r;
                //callback(r);
            }
        });
    }
    ERPLookup.prototype.search=function(text)
    {
        var self=this;
        text=text.toLowerCase();
        var result=[];

        $.each(self.data,function(i,item)
        {
            var ok=false;
            $.each(self.settings.searchFields,function(j,f)
            {
                if(String(item[f]).toLowerCase().indexOf(text)>=0)
                {
                    ok=true;
                }
            });

            if(ok)
                result.push(item);
        });
        self.ds.data(result);
    }
    ERPLookup.prototype.autoComplete=function(text)
    {
        var self=this;
        var first=null;

        $.each(self.data,function(i,row)
        {
            if(row.text.toLowerCase().startsWith(text.toLowerCase()))
            {
                first=row;
                return false;
            }
        });
        if(first)
        {
            self.preview(first);
        }
    }
    ERPLookup.prototype.resolveParameters=function()
    {
        var self=this;
        var p={};

        $.each(self.settings.parameters,function(key,value)
        {
            
            //console.log(key, value);
            if($.isFunction(value))
            {
                p[key]=value();
                return;
            }

            if(typeof value==="string")
            {
                // Campo del modelo
                if(value.charAt(0)=="@")
                {                    
                    if(!self.editorOptions || !self.editorOptions.model){
                        p[key]=null;
                        return;
                    }

                    var campo=value.substr(1);
                  
                    p[key]=self.editorOptions.model.get(campo);
                    return;
                }

                // Selector
                if(value.charAt(0)=="#")
                {
                    if(value.indexOf(":checked")>0)
                        p[key]=$(value).is(":checked");
                    else
                        p[key]=$(value).val();

                    return;
                }
            }

            p[key]=value;
        });
        //console.log("Parámetros enviados:", p);
        return p;
    };
    ERPLookup.prototype.addHistory=function(item)
    {
        var self=this;
        var h=JSON.parse(localStorage.getItem("erp_lookup_history")||"[]");
        h.unshift(item);
        h=h.slice(0,20);
        localStorage.setItem("erp_lookup_history",JSON.stringify(h));
    }