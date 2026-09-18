<?php
//error_reporting(0);
//ini_set('display_errors', 0);
    session_start();
    require_once('config/config.php');
    require_once 'Helpers/Helpers.php';

    //$ruta = !(empty($_GET['url'])) ? $_GET['url'] : CONTROLLER_DEFAULT . "/" . METHOD_DEFAULT;
    $ruta = !(empty($_GET['url'])) ? trim($_GET['url'], '/'): CONTROLLER_DEFAULT . "/" . METHOD_DEFAULT; 
    
    
    $array = explode("/", $ruta);
    $controller = $array[0];
    $controllerAliases = [
        'bitacora' => 'Bitacoras',
    ];
    $controller = $controllerAliases[strtolower($controller)] ?? $controller;
    $metodo = METHOD_DEFAULT;    
    $parametro = "";  
    
    if (!(empty($array[1])))
    {
        if (!(empty($array[1] != ""))) 
        {
            $metodo = $array[1];
        }
    }
    if (!(empty($array[2]))) 
    {
        if (!(empty($array[2] != "")))
        {
            for ($i = 2; $i < count($array); $i++) 
            {
                $parametro .= $array[$i] . ",";
            }
            $parametro = trim($parametro, ",");
        }
    }
            
    require_once 'Config/App/autoload.php';

    //$dirController = CONTROLLER . "/" . $controller . ".php";
    //$dirController = $folder ? CONTROLLER . "/{$folder}/{$controller}.php" : CONTROLLER . "/{$controller}.php";
    $dirController = findController($controller, CONTROLLER);
    $errorController = CONTROLLER . "/" . CONTROLLER_ERROR . ".php";
    /* Limpiar las sessiones*/
    //*Auth::logout();   
     //phpinfo();
    //var_dump($errorController);
    if (file_exists($dirController)) 
    {
        require_once $dirController;        
        $controller = new $controller();
        if (method_exists($controller, $metodo)) 
        {
            $controller->$metodo($parametro);            
        } else 
        {
            require_once $errorController;
            $controller = new Error404;
            $controller->index();
            // echo 'No existe el Metodo';
        }
    } else 
    {        
        require_once $errorController;
        $controller = new Error404;
        $controller->index();
        // echo 'No existe el controlador';
    }
    
    function findController($controller, $path)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file)
        {
            if ($file->isFile() && strcasecmp($file->getFilename(),$controller . '.php') === 0) 
            {
                return $file->getPathname();
            }
        }
        return false;
    }
    
?>
