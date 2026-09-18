<?php

class NegocioModel extends DB
{
    public function __construct() { parent::__construct(); }
   
    static function GetTipoCargoporCliente($id)
    {   
        $query ="SELECT TC.PKINTID_TIPOCARGO,TC.VCHDESCRIPCION 
                FROM TblTipoCargo TC
                INNER JOIN TblTipoCargoClienteSucursal TCS ON TC.PKINTID_TIPOCARGO = TCS.FKINTID_TIPOCARGO
                INNER JOIN TblClientes_Sucursal CS ON TCS.FKINTID_CLIENTE_SUCURSAL = CS.PKINTID_CLIENTE_SUCURSAL
                WHERE CS.FKINTID_CLIENTE = :cliente";            
       return DB::query($query,[':cliente' => $id]);                    
    }
}