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
        private $modelo;
        public function __construct()
        {
            $this->modelo = new BitacorasModel();
        }
        public function NuevoServicio()
        {
            $fiscal = DB::First('SELECT dciva, dcretencion FROM tblsucursal WHERE pkintid_sucursal = :sucursal',
                [':sucursal' => BitacorasModel::valorSesion('cveSucursal')]);
            $this->getView($this, "NewBitacora", [
                'page_name'      => "Bitacoras",
                'function_js'    => "Bitacora.js",
                'tipclasif'      => json_encode(VehiculoModel::GetClasificacionesBase()),
                'Empleado'       => json_encode($this->GetAllEmpleado()),
                'Claves'         => json_encode($this->AllClave()),
                'ivaConfigurado' => $fiscal['dciva'] ?? null,
                'retencionConfigurada' => $fiscal['dcretencion'] ?? null
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
            
            $clas = (int)($_POST['qclas'] ?? 0);
            $buscar = trim((string)($_POST['buscar'] ?? ''));
            $dataitem = $buscar === ''
                ? VehiculoModel::AllTipo($id, $clas)
                : VehiculoModel::TipoSearch($id, $clas, $buscar);
            if ($opcion == 0) {
                echo json_encode(['data' => $dataitem]);
            } else {
                $data = [];
                foreach ($dataitem as $row) $data[] = ['id' => $row['pkintid_tipo'], 'text' => $row['vchtipo']];
                echo json_encode($data);
            }
        }
        /*Gruas del operador*/             
        public function GetAllGrua()
        {            
            header("Content-type: application/json");          
            
            $opcion = $_POST['qopcion'];
            $id = $_POST['qope'];

            $data = [];
            $datos = DB::query("SELECT u.pkintid_grua, u.vchnombre_grua
                FROM tblgruaxchofer g INNER JOIN tblunidades u ON u.pkintid_grua = g.fkintid_grua
                WHERE g.fkintid_empleado = :operador AND u.bitservicio_asig = 0 ORDER BY g.tiporelacion",
                [':operador' => $id]);
            
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
            
            $qsuc = BitacorasModel::valorSesion('cveSucursal');
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
           
            $datos = DB::query("SELECT t.pkintid_tarifa AS PKINTID_TARIFA, t.vchleyenda AS VCHLEYENDA,
                t.mnprecio_unitario AS MNPRECIO_UNITARIO, (t.bitactivo + 0) AS BITACTIVO,
                t.vchtipo_servicio AS VCHTIPO_SERVICIO, (s.bitcomision + 0) AS bitcomision,
                (s.bitretencion + 0) AS bitretencion
                FROM tbltarifas t INNER JOIN tblservicios s ON s.pkintid_servicio = t.fkintid_servicio
                INNER JOIN tblclientes_sucursal cs ON cs.pkintid_cliente_sucursal = t.fkintid_cliente_sucursal
                WHERE t.fkintid_cliente_sucursal = :cliente AND cs.fkintid_sucursal = :sucursal AND s.bitactivo = 1",
                [':cliente' => $qcve, ':sucursal' => BitacorasModel::valorSesion('cveSucursal')]);
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
            $datos = DB::query("SELECT TC.PKINTID_TIPOCARGO, TC.VCHDESCRIPCION
                FROM TblTipoCargo TC
                INNER JOIN TblTipoCargoClienteSucursal TCS ON TC.PKINTID_TIPOCARGO = TCS.FKINTID_TIPOCARGO
                INNER JOIN TblClientes_Sucursal CS ON CS.PKINTID_CLIENTE_SUCURSAL = TCS.FKINTID_CLIENTE_SUCURSAL
                WHERE CS.PKINTID_CLIENTE_SUCURSAL = :cliente AND CS.FKINTID_SUCURSAL = :sucursal AND TC.BITACTIVO = 1",
                [':cliente' => $qcve, ':sucursal' => BitacorasModel::valorSesion('cveSucursal')]);
            
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
            $data = [];
            $Empleados = DB::query("SELECT pkintid_empleado, CONCAT(vchnombre, ' ', vchapellidos) AS nombre_completo,
                fkintid_puesto, (bitestatus + 0) AS bitestatus, (bitdisponible + 0) AS bitdisponible,
                (bitcomodin + 0) AS bitcomodin FROM tblempleados ORDER BY pkintid_empleado");

            foreach ($Empleados as $item)
            {
                if ($item['bitestatus'] == true && $item['bitdisponible'] == true && ($item['fkintid_puesto'] == 5 || $item['bitcomodin'] == true)){
                    $data[] = array('id' => $item["pkintid_empleado"],'text' =>$item["nombre_completo"]); 
                }
            }                        
           return $data;
        }
        /*Clave*/
        function AllClave()
        {   
            $data = [];
            $oClaves = ParametroCfdiModel::GetAllClavesServicio();

            foreach ($oClaves as $item)
            {
                if (!(int)$item['bitactivo']) continue;
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
            // Keep PHP diagnostics out of the JSON response; errors are logged below.
            ini_set('display_errors', '0');
            header('Content-Type: application/json; charset=utf-8');
            try {
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                    throw new InvalidArgumentException('Utilice POST para generar la orden.');
                }
                $orden = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($orden)) {
                    throw new InvalidArgumentException('La información de la orden no tiene un formato válido.');
                }
                $data = $this->modelo->GuardarOrden($orden);
                echo json_encode(['status' => true, 'message' => 'Orden generada correctamente',
                    'data' => $data], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                error_log('GuardarOrden: ' . $e->getMessage());
                http_response_code($e instanceof InvalidArgumentException || $e instanceof JsonException ? 400 : 500);
                $message = $e instanceof JsonException ? 'El JSON recibido no es válido.' :
                    ($e instanceof PDOException ? 'No fue posible guardar la orden. Ningún cambio fue registrado.' :
                    ($e instanceof RuntimeException || $e instanceof InvalidArgumentException ? $e->getMessage() :
                    'No fue posible procesar la orden. Ningún cambio fue registrado.'));
                echo json_encode(['status' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
            }
            exit;
        }
    }
