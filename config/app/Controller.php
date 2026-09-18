<?php
    class Controller extends Views 
    {      
        public function __construct()
        {   
            
            $views = new Views();            
            $this->cargarModel();            
          
        }
        public function cargarModel()       
        {            
            $model = get_class($this)."Model";
            $ruta = "Models/".$model.".php";            
            if (file_exists($ruta)) {
                require_once $ruta;                
                $model= new $model();  
            }            
        }
        
        
    }  
    
?>
