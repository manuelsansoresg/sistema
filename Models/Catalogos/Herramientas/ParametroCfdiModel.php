<?php
class ParametroCfdiModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /* CLAVES DE SERVICIO */
    static function GetAllClavesServicio()
    {
        $sql = "SELECT pkintid_clave, vchdescripcion, intclave, vchtipo_clave, (bitestado + 0) AS bitactivo 
                FROM tblclave ORDER BY intclave ASC";
        return DB::query($sql);
    }

   
}
