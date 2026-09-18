/*  
    ERPGrid Keyboard
    Version : 1.0 Compatible : Kendo UI 2015
*/
    
    (function () 
    {

        if (typeof ERPGrid === "undefined")
            return;

        ERPGrid.prototype.bindKeyboard = function () 
        {
            var self = this;
            self.grid.table.attr("tabindex", "0");
            self.grid.table.off(".erpkeyboard");
            self.grid.table.on("keydown.erpkeyboard", function (e) 
            {
                self.onKeyDown(e);
            });
        };
    })();
    
    ERPGrid.prototype.onKeyDown=function(e)
    {
        switch(e.which)
        {
            case 9:     // TAB
                this.keyTab(e);
                break;
            case 13:    // ENTER
                this.keyEnter(e);
                break;
            case 45:    // INSERT
                this.keyInsert(e);
                break;
            case 46:    // DELETE
                this.keyDelete(e);
                break;
            case 113:   // F2
                this.keyF2(e);
                break;
            case 115:   // F4
                //this.keyF4(e);
                break;
            case 38:    // ↑
                //this.keyUp(e);
                break;
            case 40:    // ↓
                //this.keyDown(e);
                break;
            default:
                if(e.ctrlKey && e.which==68)
                {
                    this.keyDuplicate(e);
                }
        }

    };
    
    ERPGrid.prototype.keyEnter=function(e)
    {
        e.preventDefault();
        this.navigator.next();    
    };
    ERPGrid.prototype.keyTab=function(e)
    {
        e.preventDefault();

        if(e.shiftKey)
            this.navigator.previous();
        else
            this.navigator.next();        
    };      
    ERPGrid.prototype.keyUp=function(e)
    {
        //e.preventDefault();
        var self=this;

        var current=self.grid.current();

        if(!current || current.length===0)
            current=self.currentCell;

        if(!current || current.length===0)
            return;

        var td=current.index();
        var tr=current.parent().prev();

        if(tr.length)
        {
            this.grid.editCell(tr.children().eq(td));
        }
    };
    ERPGrid.prototype.keyDown=function(e)
    {
        var self=this;
        var current=self.grid.current();       

        if(!current || current.length===0)
        {
            current = self.currentCell;
        }

        if(!current || current.length===0)
            return;

        //e.preventDefault();
        //var current=this.grid.current();

        //if(current.length==0)
        //    return;

        var td=current.index();
        var tr=current.parent().next();

        if(tr.length)
        {
            this.grid.editCell(tr.children().eq(td));
        }
    };
    
    /*
    ERPGrid.prototype.createKeyboard=function()
    {
        var self=this;

        if(!self.settings.keyboard)
            return;

        self.grid.table.off("keydown.erp");
        self.grid.table.on("keydown.erp","input",function(e)
        {
            switch(e.which)
            {
                case 13:
                    self.keyEnter(e);
                    break;
                case 9:
                    self.keyTab(e);
                    break;
                case 45:
                    self.keyInsert(e);
                    break;
                case 46:
                    self.keyDelete(e);
                    break;
                case 115:
                    self.keyF4(e);
                    break;
                case 68:
                    if(e.ctrlKey)
                        self.keyDuplicate(e);
                    break;
                case 27:
                    self.keyEscape(e);
                    break;
            }
        });
    };
    ERPGrid.prototype.keyEnter=function(e)
    {
        e.preventDefault();
        this.nextCell();
    };*/
    ERPGrid.prototype.nextCell=function()
    {
        var self=this;

        var current=self.grid.current();

        if(!current || current.length===0)
            current=self.currentCell;

        if(!current || current.length===0)
            return;

        // Si current NO es un TD, buscar el TD padre
        if(!current.is("td"))
            current=current.closest("td");

        if(!current.length)
            return;

        var tr=current.closest("tr");

        var col=current.index();

        var cols=self.getEditableColumns();

        var siguiente=cols.indexOf(col)+1;

        if(siguiente>=cols.length)
        {
            if(tr.is(":last-child"))
            {
                self.addRow();
                return;
            }

            tr=tr.next();
            siguiente=0;
        }

        self.grid.current(tr.children().eq(cols[siguiente]));
        self.grid.editCell(tr.children().eq(cols[siguiente]));
    };
    ERPGrid.prototype.previousCell=function()
    {

    }
    ERPGrid.prototype.keyInsert=function(e)
    {
        e.preventDefault();
        this.addRow();
    };
    ERPGrid.prototype.keyDelete=function(e)
    {
        e.preventDefault();
        this.deleteRow();
    };
    ERPGrid.prototype.keyDuplicate=function(e)
    {
        e.preventDefault();
        this.duplicateRow();
    };
    ERPGrid.prototype.keyF4=function(e)
    {
        e.preventDefault();
        var current=this.grid.current();

        if(current.length==0)
            return;

        current.dblclick();
    }; 
    ERPGrid.prototype.keyEscape=function(e)
    {
        e.preventDefault();
        this.grid.closeCell();
    }  
    ERPGrid.prototype.focusCell=function(row,column)
    {
        var self=this;
        var tr=self.grid.tbody.children().eq(row);

        self.grid.editCell(
            tr.children().eq(column)
        );
    };
    ERPGrid.prototype.focusLastRow=function()
    {
        var self=this;
        var row=self.ds.footer()-1;

        self.focusCell(row,0);
    };
    