<?php    
    class BitacorasModel extends DB
    {
        public function __construct()
        {
            parent::__construct();
        }   
        /* FOLIO */
        static function obtenerSiguienteFolio(int $idSucursal): array
        {
            $sql = "SELECT pkintid_sucursal,intno_bitacora,vchletra
                    FROM tblsucursal
                    WHERE pkintid_sucursal = :sucursal FOR UPDATE";
            
            $rows = DB::query($sql,[':sucursal' => $idSucursal]);

            if (!$rows || empty($rows[0]))
            {
                throw new RuntimeException('No se encontró la sucursal.');
            }

            $row = $rows[0];
           
            $numero = ((int)$row['intno_bitacora']) + 1;
            $letra = trim((string)$row['vchletra']);

            if ($letra === '')
            {
                throw new RuntimeException('La sucursal no tiene letra configurada.');
            }
            /* FORMATO ACTUAL DEL SISTEMA A00000001*/                   
            $folio = $letra.str_pad((string)$numero,7,'0',STR_PAD_LEFT);

            return ['numero' => $numero,'folio'  => $folio];
        }
        static function obtenerNumeroBis(string $folioRaiz): int
        {
            $sql = " SELECT COALESCE(MAX(intno_serv), 0) + 1 as serv FROM tblbitacora WHERE pkvchid_bis LIKE :patron";

            $row = DB::query($sql,[":patron" => $folioRaiz]);
            
            $numero = (int)$row["serv"];

            return max($numero, 2);
        }                 
        /* ACTUALIZAR CONSECUTIVO */
        static function actualizarConsecutivo(int $idSucursal,int $numero): bool
        {
            $sql = " UPDATE tblsucursal SET intno_bitacora = :numero WHERE pkintid_sucursal = :sucursal";
            $result = DB::query($sql,[':numero'   => $numero,':sucursal' => $idSucursal]);

            return $result !== false;            
        }
        /* INSERTAR BITACORA.Aquí colocamos solamente los campos que ya tenemos definidos en nuestro flujo.*/
        static function insertarBitacora(array $data): bool
        {
            /* Esta consulta la terminaremos de ajustar con el CREATE TABLE real de tblbitacora.*/ 
            $sql = "INSERT INTO tblbitacora(
                    vchfolio,pkvchid_bis,vchbitacora,intno_serv,dtfecha_serv,vchsolicita,
                    bitvalidado,bitliquidar,bitvale,bitadmision,bitinventario,bitllaves,
                    bithoja_calidad,bitliberacion,bitliberado,bitestadia,bitvalidac50,mncantidad_cobro,
                    fkintid_sucursal,fkintid_clave,fkintid_tipo,vchanio,vchcolor,vchplacas,
                    vchno_serie,vchno_motor,fkintid_grua,fkintid_cliente_sucursal,vchdir_contacto,vchdir_entrega,
                    vchtelefono_cel,vchtelefono_otro,fkintid_usuario,dtfecha_usuario,fkintid_empleado, vchservicio,
                    fkintid_clasvehiculo,vchtipo_servicio, vchestatus,vchobservaciones,vchobservaciones1,vchubicacion,
                    vchcampo1,vchcampo2,bitcita,dtfecha_cita
                )
                VALUES
                (
                    :folio,:bis,:bitacora,:intno_serv,:fecha_serv,:solicita,
                    0,0,0,0,0,0,
                    0,0,0,0,0,0,
                    :sucursal,:clave,:tipo,:anio,:color,:placas,
                    :serie,:motor,:grua,:cliente,:contacto,:termino,
                    :celular,:telefono,:usuario,CURDATE(),:empleado,'S',
                    :clasvehiculo,:tipo_servicio,'A',:observaciones,:comentarios,:ubicacion,
                    :coord_contacto,:coord_termino,:bit_cita,:fecha_cita
                )";

            return DB::query($sql,[
                ':folio'        => $data['folio'],        
                ':bis'          => $data['bis'],
                ':bitacora'     => $data['bitacora'],
                ':intno_serv'   => $data['intno_serv'],   
                ':fecha_serv'   => $data['fecha_serv'],
                ':solicita'     => $data['solicita'],
                ':sucursal'     => $data['sucursal'],
                ':clave'        => $data['clave'],
                ':tipo'         => $data['tipo'],
                ':anio'         => $data['anio'],
                ':color'        => $data['color'],
                ':placas'       => $data['placas'],
                ':serie'        => $data['serie'],
                ':motor'        => $data['motor'],
                ':grua'         => $data['grua'],
                ':cliente'      => $data['cliente'],
                ':contacto'     => $data['contacto'],
                ':termino'      => $data['termino'],
                ':celular'      => $data['celular'],
                ':telefono'     => $data['telefono'],
                ':usuario'      => $data['usuario'],
                ':empleado'     => $data['empleado'],
                ':clasvehiculo' => $data['clasvehiculo'],
                ':tipo_servicio'=> $data['tipo_servicio'],
                ':observaciones'=> $data['observaciones'],
                ':comentarios'  => $data['comentarios'],
                ':ubicacion'    => $data['ubicacion'],
                ':coord_contacto' => $data['coord_contacto'],
                ':coord_termino'  => $data['coord_termino'],
                ':bit_cita'       => $data['bit_cita'],
                ':fecha_cita'     => $data['fecha_cita']
            ])!== false;
        }
        /* TIEMPO / KM */
        static function insertarTiempoKm(string $bis): bool
        {
            /* Aquí también utilizaremos los campos exactos de tu tbltiempo_km.*/

            $sql = "
                INSERT INTO tbltiempo_km
                (
                    pkvchid_bis,
                    dtllamada,
                    dtimpresion,
                    dtcontacto_aprox,
                    bitsm,
                    bitchecado,
                    bitservconcluido,
                    intestatus
                )
                VALUES
                (
                    :bis,
                    now(),
                    now(),
                    now(),
                    0,
                    0,
                    0,
                    1
                )";
            return DB::query($sql,[':bis' => $bis]) !== false;          
        }
        /* DETALLE */
        static function insertarDetalle(string $bis,int $usuario,array $detalle): int
        {
            $totalFilas = 0;
            /* Por ahora no voy a inventar la estructura completa de tbldesgloce_serv.
               El flujo ya queda preparado para recibir:  $detalle desde KendoGrid.
            */
            $sql = "INSERT INTO tbldesgloce_serv(
                    fkvchid_bis,
                    dmcantidad,
                    mnsubtotal,
                    fkintid_tarifa,
                    fkintid_tipocargo,
                    mnprecio_unitario,
                    fkintid_cliente_sucursal,
                    fkintid_usuario,
                    dtfecha_captura,
                    mnsaldo,
                    mnmonto_pago,
                    mniva,
                    mnimporte_org,
                    dmiva,                    
                    vchkm_cubre,
                    vchkm_cubrenomina,
                    vchcomp_pago,
                    vchno_autorizacion,
                    vchno_expediente
                )
                VALUES
                (
                    :bis,
                    :cantidad,
                    :subtotal,
                    :tarifa,
                    :cargo,
                    :precio,
                    :cliente,
                    :usuario,
                    NOW(6),
                    :subtotal,
                    0,
                    :iva
                    :km,
                    :kmn,
                    :docto,
                    :aut,
                    :exp
                )";

            foreach ($detalle as $item)
            {
                if (empty($item['idcliente']) ||empty($item['idconcepto']))
                {
                    continue;
                }

                $cantidad = (float)($item['cantidad'] ?? 0);
                $precio   = (float)($item['precio'] ?? 0);
                $iva      = (float)($item['iva'] ?? 0);

                $subtotal = (float)($item['subtotal']?? ($cantidad * $precio));
                $total = $subtotal + $iva;

                $ok = DB::query($sql,[
                    ':bis'      => $bis,
                    ':cantidad' => $cantidad,
                    ':subtotal' => $subtotal,
                    ':tarifa'   => (int)$item['idconcepto'],
                    ':cargo'    => (int)($item['idcargo'] ?? 0),
                    ':precio'   => $precio,
                    ':cliente'  => (int)$item['idcliente'],
                    ':usuario'  => $usuario,
                    ':saldo'    => $total,
                    ':iva'      => $iva,
                    ':km'       => $item['km'],
                    ':kmn'      => $item['kmn'],
                    ':docto'    => $item['docto'],
                    ':aut'      => $item['asistencia'],
                    ':exp'      => $item['expendiente'] 
                ]);

                if ($ok === false)
                {
                    throw new RuntimeException('No fue posible guardar un detalle del servicio.');
                }

                $totalFilas++;
            }                        
            return $totalFilas;            
        }
        /*  CITA */
        static function insertarCita(string $bis,int $usuario,array $cita): int
        {
            $sql = "
                INSERT INTO tblbitacora_cita
                (
                    fkvchid_bis,
                    dtfecha_cita,
                    intduracion_min,
                    vchobservacion,
                    vchestatus,
                    fkintid_usuario,
                    dtfecha_captura
                )
                VALUES
                (
                    :bis,
                    :fecha,
                    :duracion,
                    :observacion,
                    'A',
                    :usuario,
                    NOW(6)
                )";

            return DB::query($sql,
                [
                    ':bis'          => $bis,
                    ':fecha'        => $cita['fecha'],
                    ':duracion'     => (int)($cita['duracion']?? 60),
                    ':observacion'  => $cita['observacion'] ?? null,
                    ':usuario'      => $usuario
                ]);
        }
        /* AUDITORIA */
        static function insertarAuditoria(string $bis,int $usuario,array $data,?string $ip): int
        {
            $sql = "
                INSERT INTO tblbitacora_auditoria
                (
                    fkvchid_bis,
                    vchtabla,
                    vchcampo,
                    vchtipo_movimiento,
                    vchvalor_anterior,
                    vchvalor_nuevo,
                    fkintid_usuario,
                    dtfecha,
                    vchip,
                    vchdescripcion
                )
                VALUES
                (
                    :bis,
                    'tblbitacora',
                    NULL,
                    'CREAR',
                    NULL,
                    :nuevo,
                    :usuario,
                    NOW(6),
                    :ip,
                    :descripcion
                )
            ";

            return DB::query($sql,
            [
                ':bis'          => $bis,
                ':nuevo'        => json_encode( $data, JSON_UNESCAPED_UNICODE |JSON_UNESCAPED_SLASHES),
                ':usuario'      => $usuario,
                ':ip'           => $ip,
                ':descripcion'  => 'Creación del servicio ' .$bis
            ]);
            
        }
        static function GuardarOrden($orden)
        {
            $transaccionIniciada = false;

            try
            {
                /* INICIAR UNA SOLA TRANSACCIÓN*/
                DB::begin();
                $transaccionIniciada = true;   
                /* 1. OBTENER FOLIO */
                $folio = $this->obtenerSiguienteFolio();

                if (!$folio)
                {
                    throw new Exception('No fue posible obtener el siguiente folio.');
                }

                /* 2. OBTENER BIS */
                $pkvchid_bis = $this->obtenerNumeroBis($folio);

                if (!$pkvchid_bis)
                {
                    throw new Exception('No fue posible generar el identificador BIS.');
                }
                /* 3. INSERTAR BITÁCORA*/
                $this->insertarBitacora($orden,$folio,$pkvchid_bis);
                /* 4. INSERTAR TIEMPO / KM*/
                $this->insertarTiempoKm($orden,$folio,$pkvchid_bis);
                /* 5. INSERTAR DETALLE KENDO */
                $this->insertarDetalle($orden['detalle'],$folio,$pkvchid_bis);
                /* 6. CITA*/
                if (isset($orden['opciones']['citas']) &&$orden['opciones']['citas'] === true)
                {
                    $this->insertarCita($orden,$folio,$pkvchid_bis);
                }
                /* 7. AUDITORÍA*/
                $this->insertarAuditoria($orden,$folio,$pkvchid_bis);
                /* 8. ACTUALIZAR FOLIO */
                $this->actualizarFolio($folio);
                /* TODO CORRECTO*/
                DB::commit();
                $transaccionIniciada = false;

                return Response::success('Orden generada correctamente.',['folio' => $folio,'pkvchid_bis' => $pkvchid_bis,'intno_serv' => 1]);
            }
            catch (Exception $e)
            {
                /* SI ALGO FALLÓ: DESHACER TODO*/
                if ($transaccionIniciada)
                {
                    try
                    {
                        DB::rollback();
                    }
                    catch (Exception $rollbackError)
                    {  
                        /*
                         * No ocultamos el error original.
                         */
                    }
                }

                throw $e;
            }
        }


        
    }    
?>