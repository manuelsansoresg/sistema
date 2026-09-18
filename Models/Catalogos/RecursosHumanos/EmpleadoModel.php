<?php
class EmpleadoModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }
      
    static function GetAll()
    {
        try {
            $sql = "SELECT e.pkintid_empleado, 
                           e.claveempleado, 
                           CONCAT(e.vchnombre, ' ', e.vchapellidos) AS nombre_completo,
                           e.vchcurp, 
                           e.vchrfc, 
                           d.vchtelefono_cel AS numero_telefono, 
                           p.vchdescripcion AS vchpuesto, 
                           s.vchnombre_sucursal AS vchsucursal,
                           (e.bitestatus + 0) AS bitactivo,
                           e.fkintid_puesto,
                           e.bitestatus,
                           e.bitdisponible,
                           e.bitcomodin
                    FROM tblempleados e
                    LEFT JOIN tblpuestos p ON e.fkintid_puesto = p.pkintid_puesto
                    LEFT JOIN tbldirecciones d ON e.fkintid_direccion = d.pkintid_direccion
                    LEFT JOIN tblsucursal s ON e.fkintid_sucursal = s.pkintid_sucursal
                    ORDER BY e.pkintid_empleado ASC";
            $res = DB::query($sql);
            return $res ?: [];
        } catch (Exception $e) {
            error_log("Error en EmpleadoModel::GetAll: " . $e->getMessage());
            return [];
        }
    }
   
}
