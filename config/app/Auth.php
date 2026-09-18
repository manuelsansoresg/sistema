<?php
    require_once ROOT . '/Models/Seguridad/LoginModel.php';

    class Auth
    {
        /* controlador para la cache*/
        protected static int $cacheTTL = 300;
        /* Obtener los permisos de usuarios */
        public static function SessionGetUser(int $iduser,bool $force = false)
        {   
            /*validar la credencial */
            if($iduser <= 0){ self::clearPermissions(); return; }            
            /*version session*/
            $qsessionversion = $_SESSION["permissions_version"]?? 0;
            /* ultima version*/
            $qlastupdate = $_SESSION["permissions_updated_at"] ?? 0;
            /**/
            $qcachevalid =(time() - $qlastupdate)< self::$cacheTTL;
            /* existe el cache */
            if(!$force && isset($_SESSION["permissions"]) && $qcachevalid ){ return; }            
            /*Consultar la version*/
            $user = DB::first("SELECT intversion FROM tblusuario WHERE pkintid_usuario = :id",[":id"=>$iduser]);
            /* Usuario invalido*/   
            if(!$user){ self::clearPermissions(); return; }
            /* version actual */
            $qdbversion = (int)$user["intversion"];
            /* si la version no cambia*/
            if (!$force && isset($_SESSION["permissions"]) && $qdbversion === $qsessionversion)
            { $_SESSION["permissions_updated_at"]= time(); return; }
            /*Consultar los permisos*/            
            $respuesta = DB::query("SELECT m.vchurl,a.vchclave
                                FROM tblpermiso p
                                INNER JOIN tblmenu m ON m.pkintidmenu = p.fkintidmenu
                                INNER JOIN tblacciones a ON a.pkintidaccion = p.fkintidaccion
                                INNER JOIN tblusuario_roles ur ON ur.fkintidrol = p.fkintidrol
                                WHERE ur.fkintid_usuario = :qiduser AND p.intpermitido = 1",[':qiduser' =>$iduser]);

            $permisos = [];
            /*creando permisos*/
            foreach($respuesta as $row)
            {
                $ruta = trim($row['vchurl']);
                $accion = trim($row['vchclave']);

                /*if (empty($ruta) || empty($accion))
                { continue; }*/
                /* Permisos */ 
                $permissions[$ruta][$accion] = true;
            }
            $_SESSION["permissions"] = $permissions;
            $_SESSION["permissions_version"] = $qdbversion;
            $_SESSION["permissions_updated_at"] = time();
        }
        /* Refrecar*/
        public static function RefreshActu(): void
        {
            if(empty($_SESSION["IdUsuario"])){  return; }
            self::SessionGetUser( (int) (unserialize($_SESSION["IdUsuario"])),true);
        }
        public static function refresh()
        {
            //header('Content-Type: application/json');
            self::RefreshActu();
            
            //echo json_encode(["status"=>true]);
        }
        /* Limpiar las sessiones */
        public static function clearPermissions(): void
        {
            unset(
                $_SESSION["permissions"],
                $_SESSION["permissions_version"],
                $_SESSION["permissions_updated_at"]
            );
        }
        /**
        *
        * @void sessiones
        *
        */
        public static function noAuth()
        {
            if (!isset($_SESSION['login'])) 
            {
                header('Location:' . base_url . '/Login');
            }
        }        
        public static function logout()
        {
            UsuarioModel::CerrarSesion(unserialize($_SESSION["IdUsuario"]));            
            
            session_start();            
            $_SESSION = [];
            /* Eliminar cookie */
            if (ini_get("session.use_cookies")) {

                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }                    
            /* Destruir sesión */
            session_destroy();
            header('Location:' . base_url . '/Login');
        }  
    }
?>
