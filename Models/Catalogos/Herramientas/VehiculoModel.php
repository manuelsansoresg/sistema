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
   
    static function AllTipo($idMarca)
    {
        $sql = "SELECT PKINTID_TIPO AS pkintid_tipo, VCHTIPO AS vchtipo,
                       FKINTID_MARCA AS fkintid_marca
                FROM TblTipo
                WHERE FKINTID_MARCA = :marca
                ORDER BY VCHTIPO";

        return DB::query($sql, [':marca' => $idMarca]);
    }

    static function TipoSearch($idMarca, $palabra)
    {
        $sql = "SELECT PKINTID_TIPO AS pkintid_tipo, VCHTIPO AS vchtipo,
                       FKINTID_MARCA AS fkintid_marca
                FROM TblTipo
                WHERE FKINTID_MARCA = :marca
                  AND VCHTIPO LIKE :buscar
                ORDER BY VCHTIPO
                LIMIT 20";

        return DB::query($sql, [':marca' => $idMarca,
            ':buscar' => '%' . $palabra . '%']);
    }

    static function All($cvevehiculo)
    {
        $sql = " SELECT DISTINCT(M.PKINTID_MARCA) ID, M.VCHMARCA, M.BITESTATUS
                FROM TblMarca M INNER JOIN 
                    TblTipo T ON T.FKINTID_MARCA = M.PKINTID_MARCA
                    WHERE T.INTID_CLASVEHICULO = :clas AND M.BITESTATUS = 1 ORDER BY M.VCHMARCA";

        return DB::query($sql, [':clas' => $cvevehiculo]);
    }
    static function MarcaSearch($cve, $palabra)
    {
        $sql = " SELECT DISTINCT(M.PKINTID_MARCA) ID, M.VCHMARCA, M.BITESTATUS
                FROM TblMarca M 
                  INNER JOIN TblTipo T ON T.FKINTID_MARCA = M.PKINTID_MARCA 
                WHERE T.INTID_CLASVEHICULO = :cve
                  AND M.VCHMARCA LIKE :palabra AND M.BITESTATUS = 1 ORDER BY M.VCHMARCA LIMIT 20";

        return DB::query($sql, [':cve' => $cve, ':palabra' => "%{$palabra}%"]);
    }
}
