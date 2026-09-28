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
            $this->cargarFormularioOrden('nuevo');
        }

        public function EditarServicio($bis = '')
        {
            $bis = rawurldecode(trim((string)$bis));
            if ($bis === '') {
                http_response_code(400);
                echo 'El BIS de la orden es obligatorio.';
                return;
            }
            $this->cargarFormularioOrden('editar', $bis);
        }

        private function cargarFormularioOrden(string $modo, ?string $bis = null): void
        {
            $sucursal = (int)(BitacorasModel::valorSesion('cveSucursal') ?: 25);
            $fiscal = DB::First('SELECT dciva, dcretencion FROM tblsucursal WHERE pkintid_sucursal = :sucursal',
                [':sucursal' => $sucursal]);
            $this->getView($this, $modo === 'editar' ? 'EditBitacora' : 'NewBitacora', [
                'page_name'      => "Bitacoras",
                'function_js'    => "Bitacora.js",
                'tipclasif'      => json_encode(VehiculoModel::GetClasificacionesBase()),
                'Empleado'       => json_encode($this->GetAllEmpleado($bis)),
                'Claves'         => json_encode($this->AllClave()),
                'ivaConfigurado' => $fiscal['dciva'] ?? null,
                'retencionConfigurada' => $fiscal['dcretencion'] ?? null,
                'modoOrden'      => $modo,
                'bisOrden'       => $bis
            ]);
        }

        public function Ordenes()
        {
            $this->getView($this, 'Ordenes', ['page_name' => 'Órdenes de servicio']);
        }

        public function GetOrdenes()
        {
            $this->responderJson(function () {
                $inicio = max(0, (int)($_GET['start'] ?? 0));
                $limite = max(1, min(100, (int)($_GET['length'] ?? 25)));
                $buscar = trim((string)($_GET['search']['value'] ?? ''));
                return $this->modelo->ObtenerOrdenes($inicio, $limite, $buscar);
            });
        }

        public function GetOrden($bis = '')
        {
            $bis = rawurldecode(trim((string)$bis));
            $this->responderJson(function () use ($bis) {
                if ($bis === '') throw new InvalidArgumentException('El BIS es obligatorio.');
                return $this->modelo->ObtenerOrden($bis);
            });
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
            
            $buscar = trim((string)($_POST['buscar'] ?? ''));
            $dataitem = $buscar === ''
                ? VehiculoModel::AllTipo($id)
                : VehiculoModel::TipoSearch($id, $buscar);
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
            $bis = trim((string)($_POST['bis'] ?? ''));

            $data = [];
            $datos = DB::query("SELECT u.pkintid_grua, u.vchnombre_grua
                FROM tblgruaxchofer g INNER JOIN tblunidades u ON u.pkintid_grua = g.fkintid_grua
                LEFT JOIN tblbitacora b ON b.pkvchid_bis = :bis
                    AND b.fkintid_sucursal = :sucursal AND b.fkintid_grua = u.pkintid_grua
                WHERE g.fkintid_empleado = :operador
                  AND (u.bitservicio_asig = 0 OR b.pkvchid_bis IS NOT NULL)
                ORDER BY g.tiporelacion",
                [':operador' => $id, ':bis' => $bis,
                    ':sucursal' => BitacorasModel::valorSesion('cveSucursal') ?: 25]);
            
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
             /**
             * TODO: Descomentar linea para que tome la sesion
             */
            //$qsuc = BitacorasModel::valorSesion('cveSucursal');
            $qsuc = 25;
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

            $qcve = filter_input(INPUT_POST, 'qcve', FILTER_VALIDATE_INT);
            $qLF = strtoupper(trim((string)($_POST['qlf'] ?? '')));
            if (!$qcve || !in_array($qLF, ['L', 'F'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'Cliente o tipo de servicio inválido.']);
                return;
            }

            $sucursal = (int)(BitacorasModel::valorSesion('cveSucursal') ?: 25);
            $fiscal = DB::First('SELECT dciva FROM tblsucursal WHERE pkintid_sucursal = :sucursal',
                [':sucursal' => $sucursal]);
            $tasaIva = isset($fiscal['dciva']) ? (float)$fiscal['dciva'] : 0.0;

            $data = array();
            foreach (ClienteModel::TarifaConceptos($qcve, $qLF) as $row) {
                $data[] = array(
                    'id' => $row['PKINTID_TARIFA'],
                    'text' => $row['VCHLEYENDA'],
                    'precio' => $row['MNPRECIO_UNITARIO'],
                    'ac' => $row['bitcomision'],
                    'ret' => $row['bitretencion'],
                    'tasaIva' => $tasaIva
                );
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
        function GetAllEmpleado(?string $bis = null)
        {
            $data = [];
            // En edición el operador actual debe seguir visible aunque ya esté marcado como ocupado.
            $Empleados = DB::query("SELECT e.pkintid_empleado, CONCAT(e.vchnombre, ' ', e.vchapellidos) AS nombre_completo,
                e.fkintid_puesto, (e.bitestatus + 0) AS bitestatus, (e.bitdisponible + 0) AS bitdisponible,
                (e.bitcomodin + 0) AS bitcomodin, b.pkvchid_bis AS orden_actual
                FROM tblempleados e
                LEFT JOIN tblbitacora b ON b.pkvchid_bis = :bis
                    AND b.fkintid_sucursal = :sucursal AND b.fkintid_empleado = e.pkintid_empleado
                ORDER BY e.pkintid_empleado", [':bis' => (string)$bis,
                    ':sucursal' => BitacorasModel::valorSesion('cveSucursal') ?: 25]);

            foreach ($Empleados as $item)
            {
                if ($item['bitestatus'] == true && ($item['bitdisponible'] == true || !empty($item['orden_actual'])) && ($item['fkintid_puesto'] == 5 || $item['bitcomodin'] == true)){
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

        public function ActualizarOrden()
        {
            $this->responderJson(function () {
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                    throw new InvalidArgumentException('Utilice POST para actualizar la orden.');
                }
                $orden = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($orden)) throw new InvalidArgumentException('La información de la orden no tiene un formato válido.');
                $bis = trim((string)($orden['pkvchid_bis'] ?? ''));
                if ($bis === '') throw new InvalidArgumentException('El BIS de la orden es obligatorio.');
                return $this->modelo->ActualizarOrden($bis, $orden);
            }, 'Orden actualizada correctamente');
        }

        /**
         * Mantiene los endpoints JSON libres de warnings y con un contrato uniforme.
         */
        private function responderJson(callable $accion, string $mensaje = 'Consulta realizada correctamente'): void
        {
            ini_set('display_errors', '0');
            header('Content-Type: application/json; charset=utf-8');
            try {
                $data = $accion();
                echo json_encode(['status' => true, 'message' => $mensaje, 'data' => $data],
                    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                error_log('Bitacoras JSON: ' . $e->getMessage());
                $esSolicitud = $e instanceof InvalidArgumentException || $e instanceof JsonException;
                http_response_code($esSolicitud ? 400 : ($e instanceof RuntimeException ? 404 : 500));
                $message = $e instanceof PDOException
                    ? 'No fue posible procesar la solicitud en la base de datos.'
                    : $e->getMessage();
                echo json_encode(['status' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
            }
            exit;
        }
    }
