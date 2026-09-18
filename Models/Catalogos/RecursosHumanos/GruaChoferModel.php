<?php
class GruaChoferModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }
      
    static function GetGruaxEmpleado($qidemp)
    {
        $sql = "SELECT u.pkintid_grua, u.vchnombre_grua,u.vchno_eco,u.vchplaca_estatal,u.vchplaca_federal,u.vchaño,u.bitcombustible, g.tiporelacion
                FROM tblgruaxchofer g
                   INNER JOIN tblunidades u ON u.pkintid_grua = g.fkintid_grua
                WHERE g.fkintid_empleado = :idemp
                ORDER BY g.tiporelacion ASC";

        return DB::query($sql, [':idemp' => $qidemp]);
    }
   
}
