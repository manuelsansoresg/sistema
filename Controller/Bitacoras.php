<?php
    ini_set('date.timezone','America/Mexico_City');
    require_once ROOT . '/config/app/Conexion.php';
    require_once ROOT . '/Models/Bitacoras/BitacorasModel.php';
    require_once ROOT . '/Models/Catalogos/Herramientas/VehiculoModel.php';
    require_once ROOT . '/Models/Catalogos/RecursosHumanos/EmpleadoModel.php';
    require_once ROOT . '/Models/Catalogos/Herramientas/ParametroCfdiModel.php';
    require_once ROOT . '/Models/Catalogos/RecursosHumanos/GruaChoferModel.php';
    require_once ROOT . '/Models/Clientes/ClienteModel.php';
    require_once ROOT . '/Models/Catalogos/Herramientas/NegocioModel.php';
    require_once ROOT . '/config/app/MapsUtileria.php';
    require_once ROOT . '/config/app/funciones.php';
       
    class Bitacoras extends Controller
    {  
        public function __construct()
        {
        }
        public function NuevoServicio()
        {             
            views::getView($this, "NewBitacora", [
                'page_name'      => "Bitacoras",
                'function_js'    => "Bitacora.js",
                'tipclasif'      => json_encode(VehiculoModel::GetClasificacionesBase()),
                'Empleado'       => json_encode($this->GetAllEmpleado()),
                'Claves'         => json_encode($this->AllClave())
            ]);
        }               
        /*Marcas*/    
        public function GetAllMarca()
        {            
            header("Content-type: application/json");                       
            
            $opcion = $_POST['qopcion'];
            $id = $_POST['qclas'];
            
            if($opcion==0)
            {
                echo "{\"data\":" .json_encode( VehiculoModel::All($id)). "}";
            }
            else
            {
                if(!isset($_POST['buscar']))
                {
                    $dataitem = VehiculoModel::All($id);
                    $data = array();
                
                    foreach ($dataitem as $row)
                    {   $data[] = array('id' => $row["ID"],'text' =>$row["VCHMARCA"]); }
                }
                else
                {
                    $dataitem = VehiculoModel::MarcaSearch($id,$_POST['buscar']);
                    $data = array();
                
                    foreach ($dataitem as $row)
                    {   $data[] = array('id' => $row["ID"],'text' =>$row["VCHMARCA"]); } 
                }
                
                echo json_encode($data);
            }                        
        }
        /* Tipo */
        public function GetAllTipo()
        {            
            header("Content-type: application/json");            
            
            $opcion = $_POST['qopcion'];
            $id = $_POST['qmarca'];
            
            if($opcion==0)
            {
                echo "{\"data\":" .json_encode(VehiculoModel::AllTipo($id)). "}";
            }
            else
            {
                if(!isset($_POST['buscar']))
                {
                    $dataitem = VehiculoModel::AllTipo($id);
                    $data = array();
                
                    foreach ($dataitem as $row)
                    {   $data[] = array('id' => $row["pkintid_tipo"],'text' =>$row["vchtipo"]); }
                }
                else
                {
                    $dataitem = VehiculoModel::TipoSearch($id,$_POST['buscar']);
                    $data = array();
                
                    foreach ($dataitem as $row)
                    {   $data[] = array('id' => $row["pkintid_tipo"],'text' =>$row["vchtipo"]); } 
                }
                
                echo json_encode($data);
            }
                        
        } 
        /*Gruas del operador*/             
        public function GetAllGrua()
        {            
            header("Content-type: application/json");          
            
            $opcion = $_POST['qopcion'];
            $id = $_POST['qope'];

            $datos = GruaChoferModel::GetGruaxEmpleado($id);
            
            foreach($datos as $item)
            {
                $data[] = array('id' => $item["pkintid_grua"],'text' =>$item["vchnombre_grua"]); 
            }            
            
            echo json_encode($data);                        
        }
        /*Cliente*/
        public function GetCliente_Suc_RFC()
        {
            header("Content-type: application/json");
            
            $qsuc   = empty($_POST['qsuc']) ? 0 : $_POST['qsuc'] ;
            $qtodos = empty($_POST['qtodos']) ? false : $_POST['qtodos'];
            $data = array();
            
            $datos = ClienteModel::GetCliente_Suc_RFC($qsuc);            
            
            if ($qtodos=="true")
            {
                foreach ($datos as $row)
                {                           
                    if ($row['BITACTIVO']==true)
                    {
                        $data[] = array('id'        => $row["PKINTID_CLIENTE_SUCURSAL"],
                                        'text'      => $row["VCHRAZON_SOCIAL"],
                                        'idcargo'   => $row["pkintid_tipocargo"],
                                        'cargo'     => $row["vchdescripcion"]); 
                    }
                }
            }    
            else
            {
                foreach ($datos as $row)
                {    
                    if ($row['BITACTIVO']==true && ($row['BITC_FRECUENTE']== true))
                    {
                        $data[] = array('id' => $row["PKINTID_CLIENTE_SUCURSAL"],'text' =>$row["VCHRAZON_SOCIAL"],'idcargo' => $row["pkintid_tipocargo"],'cargo' => $row["vchdescripcion"]);
                    }
                }
            }
           

            
           echo json_encode($data);
            
        }
        /*Conceptos*/
        public function GetTarifaAll()
        {
            header("Content-type: application/json");                      
            
            $qcve = empty($_POST['qcve']) ? false : $_POST['qcve'];
            $qLF = empty($_POST['qlf']) ? false : $_POST['qlf']; /*Tipo de Servicio*/
            $data = array();
           
            $datos = ClienteModel::TarifaConceptos($qcve);
            //'on'
            //MNPRECIO_UNITARIO
            foreach ($datos as $row)
            {                           
                if (($row['BITACTIVO']==true && trim($row['VCHTIPO_SERVICIO'])==trim($qLF))|| ($row['BITACTIVO']==true && trim($row['VCHTIPO_SERVICIO']) == "A"))
                {
                    $data[] = array('id' => $row["PKINTID_TARIFA"],'text' =>$row["VCHLEYENDA"],'precio'  => $row["MNPRECIO_UNITARIO"],'ac'=> $row["bitcomision"],'ret' => $row["bitretencion"]); 
                }
            }  
            
            echo json_encode($data);            
        }
        public function GetTipoCargo()
        {
            header("Content-type: application/json");
                      
            $qcve = empty($_POST['qcve']) ? false : $_POST['qcve'];             
            $datos = NegocioModel::GetTipoCargoporCliente($qcve);
            
            $data = array();                
            
            foreach ($datos as $row)
            {                         
               $data[] = array('id' => $row["PKINTID_TIPOCARGO"],'text' =>$row["VCHDESCRIPCION"]);             
            }
            
            echo json_encode($data);
                     
        }
        /*empleados*/
        function GetAllEmpleado()
        {   
            $Empleados = EmpleadoModel::GetAll();

            foreach ($Empleados as $item)
            {
                if ($item['fkintid_puesto'] == 5 && $item['bitestatus'] == true && $item['bitdisponible'] == true || $item['bitcomodin'] == true){
                    $data[] = array('id' => $item["pkintid_empleado"],'text' =>$item["nombre_completo"]); 
                }
            }                        
           return $data;
        }
        /*Clave*/
        function AllClave()
        {   
            $oClaves = ParametroCfdiModel::GetAllClavesServicio();

            foreach ($oClaves as $item)
            {
                if ($item['pkintid_clave'] == 3){
                    $data[] = array('id' => $item["pkintid_clave"],'text' =>$item["vchdescripcion"]); 
                }
                else
                {
                    $data[] = array('id' => $item["pkintid_clave"],'text' =>$item["vchdescripcion"]); 
                }
            }            
            /*Ordenarlo*/
            array_multisort(array_column($data, 'id'), SORT_ASC, $data);
                                   
            return $data;
        }        
        public function GuardarOrden()
        {
            header('Content-Type: application/json; charset=utf-8');
            try
            {
                /* RECIBIR JSON */
                $json = file_get_contents('php://input');

                if (!$json)
                {
                    throw new Exception('No se recibió información de la orden.');
                }

                $orden = json_decode($json, true);

                if (!is_array($orden))
                {
                    throw new Exception('La información recibida no tiene un formato válido.');
                }
                /* VALIDACIONES BÁSICAS */
                if (empty($orden['vehiculo']))
                {
                    throw new Exception('No se recibió la información del vehículo.');
                }
                if (empty($orden['servicio']))
                {
                    throw new Exception('No se recibió la información del servicio.');
                }
                if (empty($orden['detalle']) || !is_array($orden['detalle']))
                {
                    throw new Exception('La orden debe contener al menos un concepto.');
                }
                /* GUARDAR */
                $resultado = $this->modelo->GuardarOrden($orden);

                if (!$resultado || empty($resultado['status']))
                {
                    throw new Exception(!empty($resultado['message'])? $resultado['message']: 'No fue posible guardar la orden.');
                }
                /*  RESPUESTA */
                echo json_encode(Response::success('Orden generada correctamente.',[
                               'folio'       => isset($resultado['folio'])? $resultado['folio'] : '',
                               'pkvchid_bis' => isset($resultado['pkvchid_bis'])? $resultado['pkvchid_bis']: '',
                               'intno_serv'  => isset($resultado['intno_serv']) ? $resultado['intno_serv']: 1 
                        ]
                    )
                );
            }
            catch (Exception $e)
            {
                http_response_code(400);
                echo json_encode(Response::error($e->getMessage()));
            }
            exit;
        }                
    }
?>
