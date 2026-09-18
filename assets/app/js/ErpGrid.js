    (function(window,$)
    {
        function ERPGrid(config)
        {
            this.version="1.0";
            this.settings=$.extend(true,
            {
                grid:null,
                columns:[],
                datasource:null,
                autoAdd:true,
                keyboard:true,
                calculate:true,
                confirmDelete:true,      // <-- Nuevo
                navigator:null,
                confirmType:"sweetalert",
                confirmMessage:"¿Desea eliminar el servicio seleccionado?",
                editable:false
            },config);

            this.grid=null;
            this.ds=null;
            
            this.calculation=$.extend({},ERPGrid.DefaultCalculation,config.calculation||{});
        }        
        
        window.ERPGrid=ERPGrid;

    })(window,jQuery);

    ERPGrid.prototype.init=function()
    {

        this.createDatasource();
        this.createGrid();
        this.bindKeyboard();        
        //this.createFooter();

        return this;
    };
    ERPGrid.prototype.createDatasource=function()
    {

        var self=this;
        self.ds=new kendo.data.DataSource(
        {
            data:[],
            schema:
            {
                model:
                {
                    id:"idlinea",
                    fields:
                    {
                        idlinea:{type:"number"},
                        idcliente:{},
                        cliente:{},
                        idconcepto:{},
                        servicio:{},                                              
                        asistencia:{ type:"string"},
                        expendiente:{ type:"string"},
                        kmn:{ type:"number"},
                        km:{ type:"number"},
                        cantidad:{ type:"number", defaultValue:1},
                        precio:{ type:"number", defaultValue:0},
                        /*precioBase:{type:"number",editable:false},                        */
                        iva:{type:"number",defaultValue:0,editable:false},
                        subtotal:{type:"number",editable:false},
                        total:{type:"number",editable:false},
                        idcargo:{},
                        cargo:{},                       
                        ac:{type:"boolean"}
                    }
                }
            },
            sort:false
        });
    };    
    ERPGrid.prototype.createGrid=function()
    {
        var self=this;

        $(self.settings.grid).kendoGrid(
        {
            dataSource:self.ds,
            columns:self.settings.columns,            
            editable:{
                mode:"incell",
                createAt:"bottom"
            },            
            navigatable:true,
            selectable:"cell",
            sortable:false,            
            resizable:true,            
            scrollable:{
                virtual:false
            },                        
            save:function(e)
            {                
                self.calculateRow(e.model);
                self.calculateTotals();
            },           
            edit: function(e) 
            {
                self.calculateRow(e.model); 
                self.currentCell = e.container;   // Siempre es el <td>
            }            
        });
        
        self.grid=$(self.settings.grid).data("kendoGrid");
        self.grid.wrapper.addClass("erp-grid");
        self.navigator=new ERPGridNavigation(self);
        self.navigator.init();
    };
    ERPGrid.prototype.getEditableColumns=function()
    {
        var self=this;
        var cols=[];

        $.each(self.grid.columns,function(i,col)
        {
            // Oculta
            if(col.hidden)
                return;

            // Sin field
            if(!col.field)
                return;

            // No editable
            if(col.editable===false)
                return;

            cols.push(i);
        });

        return cols;
    };  
    ERPGrid.prototype.addRow = function ()
    {
        var self = this;
        
        var item = self.ds.add({
            idlinea: new Date().getTime(),          
            cantidad: 1,
            precio: 0,
            subtotal: 0,
            iva: 0,
            total: 0
        });
       
        
        //self.refresh();      
        
        self.calculateTotals();

        setTimeout(function(){
            self.navigator.focusLastRow(item);
        },50);
        
    };
    ERPGrid.prototype.bindDelete = function () 
    {
        var self = this;

        self.grid.wrapper.on("keydown", function (e) 
        {
            if (e.keyCode == 46) { // DELETE
                e.preventDefault();
                self.deleteRow();
            }
        });
    };
    ERPGrid.prototype.focusLastRow=function()
    {
        var row=this.grid.tbody.find("tr:last");
        this.grid.editCell(
            row.children().eq(0)
        );
    };
    ERPGrid.prototype.deleteRow=function()
    {
        var self=this;
        var row=self.grid.select();

        if(row.length==0)
            return;

        var model=self.grid.dataItem(row);

        if(!model) return;

        if(self.settings.confirmDelete)
        {
            if(!confirm(self.settings.confirmMessage))
                return;
        }

        self.ds.remove(model);
        this.refresh();
        this.calculateTotals();        
        self.trigger("rowDeleted",model);
        var rows=self.grid.tbody.find("tr");
        if(rows.length)
        {
            self.grid.select(rows.eq(Math.min(row.index(),rows.length-1)));
        }
    };
    ERPGrid.prototype.refresh=function()
    {       
        this.grid.refresh();
    };
    ERPGrid.prototype.duplicateRow=function()
    {
        var row=this.grid.select();

        if(row.length==0) return;

        var data=this.grid.dataItem(row).toJSON();
        this.ds.add(data);
    };
    ERPGrid.prototype.toJSON=function()
    {
        return this.ds.data().toJSON();
    };    
    ERPGrid.prototype.calculateRow=function(model)
    {
        var c=this.calculation;
        var cantidad=ERPGrid.Helpers.toNumber(model.get(c.quantityField));

        var precio=ERPGrid.Helpers.toNumber(model.get(c.priceField));

        var descuento=ERPGrid.Helpers.toNumber(model.get(c.discountField));

        var ivaPorcentaje=ERPGrid.Helpers.toNumber(model.get(c.taxField));

        var subtotal=cantidad*precio;

        var importeDescuento=subtotal*(descuento/100);

        subtotal-=importeDescuento;

        //var iva=subtotal*(ivaPorcentaje/100);
        var iva=subtotal*(.16);

        var total=subtotal+iva;

        model.set(c.subtotalField,ERPGrid.Helpers.round(subtotal,c.decimals));
        model.set(c.discountAmountField,ERPGrid.Helpers.round(importeDescuento,c.decimals));
        model.set(c.taxAmountField,ERPGrid.Helpers.round(iva,c.decimals));
        model.set(c.totalField,ERPGrid.Helpers.round(total,c.decimals));
        this.calculateTotals();
    };
    ERPGrid.prototype.calculateTotals=function()
    {
        var self=this;
        var c=self.calculation;
        var subtotal=0;
        var descuento=0;
        var iva=0;
        var total=0;
        self.ds.data().forEach(function(item)
        {

            subtotal+=ERPGrid.Helpers.toNumber(item.get(c.subtotalField));
            descuento+=ERPGrid.Helpers.toNumber(item.get(c.discountAmountField));
            iva+=ERPGrid.Helpers.toNumber(item.get(c.taxAmountField));
            total+=ERPGrid.Helpers.toNumber(item.get(c.totalField));
        });

        self.footer=
        {
            subtotal:subtotal,
            descuento:descuento,
            iva:iva,
            total:total
        };
        self.refreshFooter();
    };
    ERPGrid.prototype.refreshFooter=function()
    {
        if(!this.settings.footer) return;
        var f=this.settings.footer;

        $(f.subtotal).text(ERPGrid.Helpers.currency(this.footer.subtotal));
        $(f.descuento).text(ERPGrid.Helpers.currency(this.footer.descuento));
        $(f.iva).text(ERPGrid.Helpers.currency(this.footer.iva));
        $(f.total).text(ERPGrid.Helpers.currency(this.footer.total));
    };
    ERPGrid.prototype.getTotals=function()
    {
        return this.footer;
    };
    ERPGrid.prototype.clearTotals=function()
    {
        this.totals = {
            subtotal:0,
            descuento:0,
            iva:0,
            total:0
        };
    };
    ERPGrid.prototype.recalculate=function()
    {
        var self=this;

        self.ds.data().forEach(function(item){
            self.calculateRow(item);
        });

        self.calculateTotals();
        self.grid.refresh();
    }; 
    /*REgistra Eventos*/  
    ERPGrid.prototype.on=function(event,callback)
    {
        if(!this.events[event])
        {
            this.events[event]=[];
        }

        this.events[event].push(callback);

        return this;
    };
    /*Dispara el evento*/
    ERPGrid.prototype.trigger=function(event,data)
    {
        if(!this.events[event])
            return;

        $.each(this.events[event],function(i,callback){

            callback(data);

        });
    };
    /*Borrar el evento*/
    ERPGrid.prototype.off=function(event)
    {
        if(this.events[event])
        {
            delete this.events[event];
        }

        return this;
    };
    /*Obtengo la fila seleccionada var row=detalle.currentRow(); y puede cambiar el valor row.set("precio",450);*/
    ERPGrid.prototype.currentRow=function()
    {
        return this.grid.dataItem(
            this.grid.select()
        );
    };
    /*Obtengo la celda*/
    ERPGrid.prototype.currentCell=function()
    {
        return this.grid.current();
    };
    /* 
        Así podremos escribir grid.model().set("precio",350);
    */
    ERPGrid.prototype.model=function()
    {
        return this.currentRow();
    };
    /*Cuantos registro*/
    ERPGrid.prototype.rowCount=function()
    {
        return this.ds.total();
    };
