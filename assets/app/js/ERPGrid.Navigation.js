    /*Constuctor*/
    function ERPGridNavigation(grid)
    {        
        this.erpGrid = grid;      // Instancia ERPGrid
        this.grid = grid.grid;    // kendoGrid
        this.currentCell = null;
    }
    /*Inicializar*/
    ERPGridNavigation.prototype.init=function()
    {
        var self=this;
        self.grid.bind("edit",function(e)
        {
            self.currentCell=e.container;
        });
    };
    /* Obtener celda actual */
    ERPGridNavigation.prototype.getCurrentCell=function()
    {
        return this.currentCell;
    };
    /*Posicionar*/
    ERPGridNavigation.prototype.setCurrent=function(td)
    {
        this.currentCell=td;
    };
    /*Siguiente*/
    ERPGridNavigation.prototype.next=function()
    {
        var self=this;

        if(!self.currentCell)
            return;

        var td=self.currentCell;
        var tr=td.closest("tr");
        var col=td.index();
        var cols=[];
        /*
        $.each(self.grid.columns,function(i,c)
        {
            if(c.editable!==false)
                cols.push(i);
        });
        */
        
        /* OBTENER SOLO COLUMNAS EDITABLES */

        $.each(self.grid.columns,function(i,c)
        {
            if(self.isEditableColumn(c))
            {
                cols.push(i);
            }
        });
        /*Posicion actual*/    
        var pos=cols.indexOf(col);

        /*if(pos==-1)
            return;*/

        /* Si la celda actual no es editable, buscamos desde la columna actual.*/
        if(pos===-1)
        {
            pos=-1; 
            $.each(cols,function(i,index)
            {
                if(index>col && pos===-1)
                {
                    pos=i;
                }
            }); 
            if(pos===-1)
            {
                pos=0;
            }
        }
        else
        {
            pos++;
        }
        //pos++;         

        if(pos>=cols.length)
        {
            if(tr.is(":last-child"))
            {
                self.grid.addRow();
                return;
            }

            tr=tr.next();
            pos=0;
        }
        
        /* OBTENER SIGUIENTE CELDA */
        var nextCol=cols[pos];
        var nextColumn=self.grid.columns[nextCol]

        /* Seguridad adicional */
        if(!self.isEditableColumn(nextColumn))
        {
            return;
        }

        //td=tr.children().eq(cols[pos]);
        td=tr.children().eq(nextCol);
        self.currentCell=td;
        self.grid.current(td);
        self.grid.editCell(td);
    };
    /*Anterior*/
    ERPGridNavigation.prototype.previous=function()
    {
        var self=this;

        if(!self.currentCell)
            return;

        var td=self.currentCell;
        var tr=td.closest("tr");
        var col=td.index();
        var cols=[];

        $.each(self.grid.columns,function(i,c)
        {
            if(c.editable!==false)
                cols.push(i);
        });

        var pos=cols.indexOf(col);

        pos--;

        if(pos<0)
        {
            if(tr.prev().length==0)
                return;

            tr=tr.prev();
            pos=cols.length-1;
        }

        td=tr.children().eq(cols[pos]);
        self.currentCell=td;
        self.grid.current(td);
        self.grid.editCell(td);
    };
    ERPGridNavigation.prototype.focusLastRow = function (item)
    {
        var self = this;

        setTimeout(function ()
        {
            //var tr = self.grid.tbody.find("tr:last");
            var tr = self.grid.tbody.find("tr[data-uid='" + item.uid + "']");

            if (!tr.length)
                return;
                        
            var td = tr.children().eq(self.erpGrid.getEditableColumns()[0]);

            self.currentCell = td;

            //self.grid.current(td);
            self.grid.editCell(td);

        }, 50);
    };
    ERPGridNavigation.prototype.isEditableColumn=function(column)
    {
        if(!column)
            return false;
        /* Columnas explícitamente bloqueadas*/
        if(column.editable===false)
            return false;

        /* Campos calculados*/
        var camposCalculados=["iva","subtotal","total"];

        if( column.field && camposCalculados.indexOf(column.field)!==-1)
        {
            return false;
        }

        return true;
    };