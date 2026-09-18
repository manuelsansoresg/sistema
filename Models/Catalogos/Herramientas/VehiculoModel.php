<?php

class VehiculoModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    static function GetClasificacionesBase()
    {
        $sql = "SELECT PKINTID_CLASVEHICULO AS pkintid_clasvehiculo,
                       VCHCONCEPTO AS vchconcepto
                FROM TblClas_Vehiculo
                ORDER BY VCHCONCEPTO";

        return DB::query($sql);
    }
   
    static function AllTipo($cvetipo)
    {
        $suc = unserialize($_SESSION['cveSucursal']);

        $sql = " SELECT t.pkintid_tipo,t.fkintid_sucursal,t.vchtipo,t.fkintid_marca,t.intid_clasvehiculo,cv.VCHCONCEPTO
                From TblTipo t
                Inner join TblClas_Vehiculo cv on t.INTID_CLASVEHICULO = cv.PKINTID_CLASVEHICULO
                Where t.FKINTID_SUCURSAL = :suc and t.FKINTID_MARCA = :cvem ";

        return DB::query($sql, [':suc' => $suc, ':cvem' => $cvetipo]);
    }
    static function TipoSearch($cve, $palabra)
    {
        $suc = unserialize($_SESSION['cveSucursal']);

        $sql = " SELECT t.pkintid_tipo,t.fkintid_sucursal,t.vchtipo,t.fkintid_marca,t.intid_clasvehiculo,cv.VCHCONCEPTO
                FROM TblTipo t 
                INNER JOIN TblClas_Vehiculo cv on t.INTID_CLASVEHICULO = CV.PKINTID_CLASVEHICULO
                WHERE t.FKINTID_SUCURSAL = :suc AND t.FKINTID_MARCA = :cvem AND t.VCHTIPO LIKE :palabra LIMIT 20";

        return DB::query($sql, [':suc' => $suc, ':cvem' => $cve, ':palabra' => "%{$palabra}%"]);
    }        
    static function All($cvevehiculo)
    {
        $sql = " SELECT DISTINCT(M.PKINTID_MARCA) ID, M.VCHMARCA, M.BITESTATUS
                FROM TblMarca M INNER JOIN 
                    TblTipo T ON T.FKINTID_MARCA = M.PKINTID_MARCA INNER JOIN
                    TblClas_Vehiculo CV ON CV.PKINTID_CLASVEHICULO = T.INTID_CLASVEHICULO
                    WHERE PKINTID_CLASVEHICULO = :clas AND BITESTATUS = 1";

        return DB::query($sql, [':clas' => $cvevehiculo]);
    }
    static function MarcaSearch($cve, $palabra)
    {
        $sql = " SELECT DISTINCT(M.PKINTID_MARCA) ID, M.VCHMARCA, M.BITESTATUS
                FROM TblMarca M 
                  INNER JOIN TblTipo T ON T.FKINTID_MARCA = M.PKINTID_MARCA 
                  INNER JOIN TblClas_Vehiculo CV ON CV.PKINTID_CLASVEHICULO = T.INTID_CLASVEHICULO
                WHERE PKINTID_CLASVEHICULO = :cve AND M.VCHMARCA LIKE :palabra AND BITESTATUS = 1 LIMIT 20";

        return DB::query($sql, [':cve' => $cve, ':palabra' => "%{$palabra}%"]);
    }
}
