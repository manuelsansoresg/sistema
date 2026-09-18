    /* FREECAR ERP - Sidebar Controller - Version 1.0 */
    window.ERP = window.ERP || {};

    ERP.Sidebar = (function () 
    {
        const STORAGE_OPEN = "erp.sidebar.open";
        const STORAGE_RECENT = "erp.sidebar.recent";

        function init() 
        {
            rememberOpen();
            restoreOpen();
            bindSearch();
            bindTree();
            highlightCurrent();
            rememberRecent();
        }

        /* Tree */
        function bindTree()
        {
            $(".sidebar-menu").on("click",".treeview > a",function()
            {
                let id=$(this).parent().data("id");
                if(id)
                {
                    localStorage.setItem(STORAGE_OPEN,id);
                }
            });
        }
        /*  Restaurar */
        function restoreOpen()
        {
            let id=localStorage.getItem(STORAGE_OPEN);

            if(!id)return;

            $(".treeview[data-id='"+id+"']")
                .addClass("active")
                .children(".treeview-menu")
                .show();
        }
        /* Guardar */
        function rememberOpen()
        {
            $(".treeview.active").each(function()
            {
                let id=$(this).data("id");

                if(id)
                {
                    localStorage.setItem(STORAGE_OPEN,id);
                }
            });
        }
        /*  Resaltar URL */
        function highlightCurrent()
        {
            let url=window.location.href;

            $(".sidebar-menu a").each(function()
            {
                let href=$(this).attr("href");
                if(!href)return;

                if(url.indexOf(href)!==-1)
                {
                    $(this)
                        .closest("li")
                        .addClass("active")
                        .parents(".treeview")
                        .addClass("active");
                }
            });
        }

        /* Buscar */
        function bindSearch()
        {
            $(".sidebar-form input").on("keyup",function()
            {
                let value=$(this).val().toLowerCase();
                $(".sidebar-menu li").each(function()
                {
                    let txt=$(this).text().toLowerCase();
                    $(this).toggle(txt.indexOf(value)>-1);
                });
            });
        }
        /* Recientes */
        function rememberRecent()
        {
            $(".sidebar-menu").on("click","a",function()
            {
                let txt=$(this).text().trim();
                let arr=JSON.parse(localStorage.getItem(STORAGE_RECENT)||"[]");
                arr=arr.filter(x=>x!==txt);
                arr.unshift(txt);
                arr=arr.slice(0,10);
                localStorage.setItem(STORAGE_RECENT,JSON.stringify(arr));
            });
        }

        return{
            init:init
        };
    })();