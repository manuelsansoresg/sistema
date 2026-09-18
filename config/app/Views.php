<?php    
    class Views 
    {
        public function getView($controlador, $vista, $data=[],$subfolder='')
        {            
            foreach($data as $key => $value)
            {
                $$key = $value;
            }
            //$controlador = get_class($controlador);            
            
            $controlador = basename(str_replace('\\', '/', get_class($controlador)));        
            
            if ($controlador == 'Home') {
                $vista = 'page/'.$vista.'.php';
            }else
			{
				if ($subfolder !='')
                {
                    $vista = 'page/'.$subfolder.'/'.$controlador.'/'.$vista.'.php';                
                }else{    
                    $vista = 'page/'.$controlador.'/'.$vista.'.php';                
                }
            }           
            require $vista;
        }               
    }  
   
?>
