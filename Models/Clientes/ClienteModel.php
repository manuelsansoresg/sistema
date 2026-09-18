<?php

class ClienteModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }
    static function GetCliente_Suc_RFC($suc)
    {
        $sql = " SELECT CS.PKINTID_CLIENTE_SUCURSAL,CS.FKINTID_CLIENTE,C.VCHRFC,C.VCHRAZON_SOCIAL,C.BITACTIVO, CS.BITC_FRECUENTE, T.pkintid_tipocargo,T.vchdescripcion
            FROM  TblClientes C
            INNER JOIN TblClientes_Sucursal CS ON CS.FKINTID_CLIENTE = C.PKINTID_CLIENTE
            LEFT JOIN tbltipocargoclientesucursal TC ON TC.fkintid_cliente_sucursal = CS.pkintid_cliente_sucursal
                AND TC.bitdefault = 1
            LEFT JOIN tbltipocargo T ON T.pkintid_tipocargo = TC.fkintid_tipocargo
            WHERE FKINTID_SUCURSAL = :suc AND C.BITACTIVO = 1";

        return DB::query($sql, [':suc' => intval($suc)]);
    }
    static function TarifaConceptos($cliente)
    {
        $sql = "SELECT t.PKINTID_TARIFA,t.VCHCLAVE,t.VCHDESCRIPCION,t.MNPRECIO_UNITARIO,t.VCHLEYENDA,t.BITCONTACTO,
                    t.BITTERMINO,t.BITOBS,t.BITACTIVO,t.VCHTIPO_SERVICIO,t.FKINTID_USUARIO,t.DTFECHA_USUARIO,
                    t.FKINTID_CLIENTE_SUCURSAL,t.FKINTID_SERVICIO,t.INTKM,t.INTCOD_SAT,t.VCHCOD_MEDIDA,s.bitcomision,
                    s.bitretencion
                FROM TblTarifas t
                INNER JOIN tblservicios s ON t.FKINTID_SERVICIO = s.pkintid_servicio
                WHERE FKINTID_CLIENTE_SUCURSAL = :cliente";

        return DB::query($sql, [':cliente' => intval($cliente)]);
    }
}
