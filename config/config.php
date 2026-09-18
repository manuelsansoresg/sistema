<?php
    
    $requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https'
        : 'http';
    $requestHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = in_array($scriptDirectory, ['/', '.'], true)
        ? ''
        : rtrim($scriptDirectory, '/');

    define('base_url', $requestScheme . '://' . $requestHost . $basePath);
    

    define('SITE_LANG', 'es');
     
	/* Database connection values */
	define("DB_HOST", "localhost");
	define("DB", "facturacion");
	define("DB_USER", "root");
	define("DB_PASS", "demor00txx");
	define("DB_CHARSET", "utf8");
    
    /* ----------------------------------------------------- */
    /*             INFORMACION DEL SITIO                  */
    /* ----------------------------------------------------- */
    define('SITE_CHARSET', 'UTF-8');
    define('SITE_NAME', 'Empresa SA de CV');
    define('SITE_VERSION', '1.0.0');
    define('SITE_LOGO', 'logo.png');
    define('SITE_FAVICON', 'logo.png');
    define('SITE_DESC', 'Empresa SA de CV');
    define('SITE_LOGO_MAIN', 'logo.png');
    define('SITE_ANO', '2025');
    
    /* ----------------------------------------------------- */
    /*             DIRECTORIOS DE LA APP                    */
    /* ----------------------------------------------------- */
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', dirname(__DIR__));
    define('CONTROLLER', ROOT . DS . 'Controller');
    define('VIEW', ROOT . DS . "Views");
    define('TEMPLATE', VIEW . DS . "Templates");
    define('IMAGE_PATH', ROOT . DS . "assets" . DS . "img" . DS);

    /* ----------------------------------------------------- */
    /*             ARCHIVOS PUBLICOS                         */
    /* ----------------------------------------------------- */
    define('ASSETS', base_url . '/assets');    
    define('BOOTSTRAP',ASSETS."/bootstrap");
    define('DIST',ASSETS."/dist");
    define('CSS', ASSETS . "/css");
    define('FAVICON', ASSETS . "/img");
    define('FONTS', ASSETS . "/fonts");
    define('IMG', ASSETS . "/img");
    define('JS', ASSETS . "/js");
    define('LIBS', ASSETS . "/libs");
    define('PLUGINS', ASSETS . "/plugins");
	define('FRAME', ASSETS . "/scripts");
    
    /* ----------------------------------------------------- */
    /*              CONTROLLER - METHOD - ERORR DEFAULT              */
    /* ----------------------------------------------------- */
   
    define('CONTROLLER_DEFAULT', 'Login');
    define('METHOD_DEFAULT', 'index');   
    define('CONTROLLER_ERROR', 'Error404');    
?>
