<?php
    class Session
    {                   
        public function __construct()
        {  
            if (session_status() == PHP_SESSION_NONE) 
            {             
                ini_set("session.gc_maxlifetime", "24000");
                ini_set("session.cookie_lifetime","24000");            
                session_set_cookie_params(60*60*24*14);
                session_start();
            //session_cache_expire("300");            
            }
        }
        /*enviar los valores*/
		public function setCurrentIdUsuario($Iduser)
        {                           
            $_SESSION['IdUsuario'] = $Iduser;                
        }
        public function setCurrentUsuario($user)
        {                           
            $_SESSION['Usuario'] = $user;                
        }
        public function setCurrentUsuarioNombre($usuarioNombre)
        {               
            $_SESSION['UsuarioNombre'] = $usuarioNombre;
        }
        public function setCurrentHoraInicioSesion($HoraInicioSesion)
        {               
            $_SESSION['HoraInicioSesion'] = $HoraInicioSesion; 
        }
        public function setCurrentBitModificarValidas($BitModificarValidadas)
        {               
            $_SESSION['BitModificarValidadas'] = $BitModificarValidadas;    
        }
        public function setCurrentTypeUser($tipouser)
        {
            $_SESSION['tipouser'] = $tipouser;    
        }
        public function setCurrentBitLibera($bitLibera)
        {               
            $_SESSION['bitLibera'] = $bitLibera;
        }
        public function setCurrentBitValidacionSer($bitValidacionSer)
        {               
            $_SESSION['bitvalidacionser'] = $bitValidacionSer;   
        }
		public function setCurrentBitValidar50($bitvalidar50)
        {               
            $_SESSION['bitvalidar50'] = $bitvalidar50;   
        }
		public function setCurrentbitcortesia($bitcortesia)
        {               
            $_SESSION['bitcortesia'] = $bitcortesia;   
        }
		public function setCurrentbitnomina($bitnomina)
        {               
            $_SESSION['bitnomina'] = $bitnomina;   
        }        
        public function setCurrentCEmpresa($cveEmpresa)
        {               
            $_SESSION['cveEmpresa'] = $cveEmpresa;   
        }
        public function setCurrentCSucursal($cveSucursal)
        {               
            $_SESSION['cveSucursal'] = $cveSucursal;   
        }
        public function setCurrentNEmpresa($NEmpresa)
        {               
            $_SESSION['NEmpresa'] = $NEmpresa;   
        }
        public function setCurrentNSucursal($NSucursal)
        {               
            $_SESSION['NSucursal'] = $NSucursal;   
        }
        public function setCurrentIVA($DCIVA)
        {                           
            $_SESSION['DCIVA'] = $DCIVA;                
        }
        public function setCurrentRETENCION($DCRETENCION)
        {                           
            $_SESSION['DCRET'] = $DCRETENCION;                
        }
        public function setCurrentRol($idrol)
        {                           
            $_SESSION['pkintidrol'] = $idrol;                
        }
        /* Obtener los valores asignados*/
		public function getCurrentIdUsuario(){
            return $_SESSION['IdUsuario'];
        }
        public function getCurrentUsuario(){
            return $_SESSION['Usuario'];
        }
        public function getCurrentUsuarioNombre(){
            return $_SESSION['UsuarioNombre'];
        }
        public function getCurrentHoraInicioSesion(){
            return $_SESSION['HoraInicioSesion'];
        }
        public function getCurrentBitModificarValidadas(){
            return $_SESSION['BitModificarValidadas'];
        }
        public function getCurrentTypeUser(){
            return $_SESSION['tipouser'];
        }
        public function getCurrentbitLibera(){
            return $_SESSION['bitLibera'];
        }
        public function getCurrentBitvalidacionser(){
            return $_SESSION['bitvalidacionser'];
        }
		public function getCurrentbitvalidar50(){
            return $_SESSION['bitvalidar50'];
        }
		public function getCurrentbitcortesia(){
            return $_SESSION['bitcortesia'];
        }
		public function getCurrentbitnomina(){
            return $_SESSION['bitnomina'];
        }
        public function getCurrentCEmpresa(){               
            return $_SESSION['cveEmpresa'];   
        }
        public function getCurrentCSucursal(){               
            return $_SESSION['cveSucursal'];   
        }
        public function getCurrentNEmpresa(){               
            return $_SESSION['NEmpresa'];   
        }
        public function getCurrentNSucursal(){               
            return $_SESSION['NSucursal'];   
        }
        public function getCurrentIVA(){               
            return $_SESSION['DCIVA'];   
        }
        public function getCurrentRETENCION(){               
            return $_SESSION['DCRET'];   
        }
        public function getCurrentRol(){               
            return $_SESSION['pkintidrol'];   
        }
        
        /*Cerrar Session*/
        public function closeSession(){
            session_unset();
            session_destroy();
        }
    }
?>
