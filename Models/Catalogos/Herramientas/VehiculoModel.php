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
   
    static function AllTipo($marca, $clasificacion)
    {
        return self::TipoSearch($marca, $clasificacion, '');
    }

    static function TipoSearch($marca, $clasificacion, $buscar)
    {
        $sql = "SELECT DISTINCT T.PKINTID_TIPO AS pkintid_tipo, T.VCHTIPO AS vchtipo
                FROM TblTipo T
                WHERE T.FKINTID_MARCA = :marca
                  AND T.INTID_CLASVEHICULO = :clas
                  AND T.VCHTIPO LIKE :buscar
                ORDER BY T.VCHTIPO";

        return DB::query($sql, [':marca' => $marca, ':clas' => $clasificacion,
            ':buscar' => '%' . $buscar . '%']);
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
