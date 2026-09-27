<?php

class BitacorasModel extends DB
{
    private $schema = [];
    private $pdo;

    // The connection is opened only when saving, and then passed to every operation.
    public function __construct(?PDO $pdo = null) { $this->pdo = $pdo; }

    public static function valorSesion(string $key)
    {
        $value = $_SESSION[$key] ?? null;
        if (is_string($value) && preg_match('/^(?:i|d|s|b|N):/', $value)) {
            $value = unserialize($value, ['allowed_classes' => false]);
        }
        return $value;
    }

    public static function validar(array $orden): void
    {
        foreach (['vehiculo', 'servicio', 'opciones', 'detalle'] as $section) {
            if (!isset($orden[$section]) || !is_array($orden[$section])) {
                throw new InvalidArgumentException('Falta información válida de ' . $section . '.');
            }
        }
        foreach (['tipo' => 'tipo de vehículo', 'marca' => 'marca', 'clase' => 'tipo del catálogo'] as $key => $label) {
            self::id($orden['vehiculo'][$key] ?? null, $label);
        }
        foreach (['clave', 'operador', 'grua'] as $key) {
            self::id($orden['servicio'][$key] ?? null, $key);
        }
        if (!in_array($orden['servicio']['camino'] ?? 'S', ['S', 'F'], true)) throw new InvalidArgumentException('Seleccione Sobre Camino o Fuera del camino.');
        if (!empty($orden['servicio']['promesa'])) self::id($orden['servicio']['promesa'], 'tiempo promesa en minutos');
        foreach (['contacto', 'termino'] as $key) {
            if (trim(self::texto($orden['servicio'][$key] ?? null)) === '') {
                throw new InvalidArgumentException('Complete el lugar de ' . $key . '.');
            }
        }
        $local = ($orden['opciones']['local'] ?? false) === true;
        $foraneo = ($orden['opciones']['foraneo'] ?? false) === true;
        if ($local === $foraneo) {
            throw new InvalidArgumentException('Seleccione Local o Foráneo.');
        }
        if (!$orden['detalle']) {
            throw new InvalidArgumentException('Agregue al menos un concepto.');
        }
        foreach ($orden['detalle'] as $i => $item) {
            $label = 'fila ' . ($i + 1);
            if (!is_array($item)) {
                throw new InvalidArgumentException('El detalle de la ' . $label . ' no es válido.');
            }
            self::id($item['idcliente'] ?? null, 'cliente de la ' . $label);
            self::id($item['idconcepto'] ?? null, 'concepto de la ' . $label);
            self::numero($item['cantidad'] ?? null, 'cantidad de la ' . $label, true);
            self::numero($item['precio'] ?? null, 'precio de la ' . $label);
            self::id($item['idcargo'] ?? null, 'tipo de cargo de la ' . $label);
        }
        if (($orden['opciones']['citas'] ?? false) === true) {
            self::fechaCita($orden['cita'] ?? []);
        }
    }

    private static function texto($value): string
    {
        if ($value === null) return '';
        if (!is_scalar($value)) throw new InvalidArgumentException('Se recibió un campo de texto inválido.');
        return trim((string)$value);
    }

    private static function id($value, string $label): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int)$value <= 0) {
            throw new InvalidArgumentException('Seleccione ' . $label . '.');
        }
        return (int)$value;
    }

    private static function numero($value, string $label, bool $positive = false): float
    {
        if (!is_numeric($value) || !is_finite((float)$value) || ($positive ? (float)$value <= 0 : (float)$value < 0)) {
            throw new InvalidArgumentException('Ingrese un valor válido para ' . $label . '.');
        }
        return (float)$value;
    }

    private static function fechaCita($cita): string
    {
        if (!is_array($cita)) throw new InvalidArgumentException('Complete la cita.');
        $text = self::texto($cita['fecha'] ?? '') . ' ' . self::texto($cita['hora'] ?? '');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $text);
        if (!$date || $date->format('Y-m-d H:i') !== $text) {
            throw new InvalidArgumentException('Complete una fecha y hora válidas para la cita.');
        }
        self::id($cita['duracion'] ?? null, 'duración en minutos de la cita');
        return $date->format('Y-m-d H:i:s');
    }

    private function consulta(PDO $pdo, string $sql, array $params = []): PDOStatement
    {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) || is_bool($value) ? PDO::PARAM_INT : ($value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(is_int($key) ? $key + 1 : $key, is_bool($value) ? (int)$value : $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    // Resolve only existing tables and columns; no CREATE/ALTER or guessed substitutes.
    private function cargarEsquema(PDO $pdo): void
    {
        $this->schema = [];
        $tables = $this->consulta($pdo, 'SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tables as $table) {
            $this->schema[strtolower($table['TABLE_NAME'])] = ['name' => $table['TABLE_NAME'], 'engine' => $table['ENGINE'], 'columns' => []];
        }
        $columns = $this->consulta($pdo, 'SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $this->schema[strtolower($column['TABLE_NAME'])]['columns'][strtolower($column['COLUMN_NAME'])] = $column['COLUMN_NAME'];
        }
    }

    private function tabla(string $name, bool $write = false): string
    {
        $table = $this->schema[strtolower($name)] ?? null;
        if (!$table) throw new RuntimeException('Falta confirmar la tabla real ' . $name . '.');
        if ($write && strcasecmp((string)$table['engine'], 'InnoDB') !== 0) {
            throw new RuntimeException('La tabla ' . $table['name'] . ' debe permitir transacciones y bloqueo de filas (InnoDB).');
        }
        return '`' . str_replace('`', '``', $table['name']) . '`';
    }

    private function existeColumna(string $table, string $column): bool
    {
        return isset($this->schema[strtolower($table)]['columns'][strtolower($column)]);
    }

    private function exigirColumnas(string $table, array $columns): void
    {
        $this->tabla($table);
        foreach ($columns as $column) {
            if (!$this->existeColumna($table, $column)) {
                throw new RuntimeException('Falta confirmar el campo real ' . $table . '.' . $column . '.');
            }
        }
    }

    private function insertar(PDO $pdo, string $table, array $data): void
    {
        $this->exigirColumnas($table, array_keys($data));
        $columns = array_map(function ($column) { return '`' . $column . '`'; }, array_keys($data));
        $sql = 'INSERT INTO ' . $this->tabla($table, true) . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')';
        $this->consulta($pdo, $sql, array_values($data));
    }

    private function actualizar(PDO $pdo, string $table, array $data, string $where, array $whereParams): int
    {
        $this->exigirColumnas($table, array_keys($data));
        $assignments = array_map(function ($column) {
            return '`' . $column . '` = ?';
        }, array_keys($data));
        $sql = 'UPDATE ' . $this->tabla($table, true) . ' SET ' . implode(', ', $assignments) . ' WHERE ' . $where;
        return $this->consulta($pdo, $sql, array_merge(array_values($data), $whereParams))->rowCount();
    }

    private function obtenerConexion(): PDO
    {
        return $this->pdo ?: (new Conexion())->conect();
    }

    private function sucursalActual(): int
    {
        // La instalación actual conserva la sucursal 25 como respaldo para sesiones legacy.
        return (int)(self::valorSesion('cveSucursal') ?: 25);
    }

    private function usuarioActual(): int
    {
        return (int)(self::valorSesion('IdUsuario') ?: 2);
    }

    /* =========================================================
     * CONSULTA DE ÓRDENES
     * ========================================================= */

    /**
     * Recupera las órdenes de la sucursal en una sola consulta.
     *
     * @return array
     */
    public function ObtenerOrdenes(int $inicio = 0, int $limite = 25, string $buscar = ''): array
    {
        $pdo = $this->obtenerConexion();
        $this->cargarEsquema($pdo);
        $from = ' FROM ' . $this->tabla('tblbitacora') . ' b
                LEFT JOIN ' . $this->tabla('TblClientes_Sucursal') . ' cs
                    ON cs.pkintid_cliente_sucursal = b.fkintid_cliente_sucursal
                LEFT JOIN ' . $this->tabla('TblClientes') . ' c ON c.pkintid_cliente = cs.fkintid_cliente
                LEFT JOIN ' . $this->tabla('TblTipo') . ' t ON t.pkintid_tipo = b.fkintid_tipo
                LEFT JOIN ' . $this->tabla('TblMarca') . ' m ON m.pkintid_marca = t.fkintid_marca
                LEFT JOIN ' . $this->tabla('TblClas_Vehiculo') . ' cv
                    ON cv.pkintid_clasvehiculo = b.fkintid_clasvehiculo
                LEFT JOIN ' . $this->tabla('tblempleados') . ' e ON e.pkintid_empleado = b.fkintid_empleado
                LEFT JOIN ' . $this->tabla('tblunidades') . ' u ON u.pkintid_grua = b.fkintid_grua';
        $where = ' WHERE b.fkintid_sucursal = ?';
        $params = [$this->sucursalActual()];
        if ($buscar !== '') {
            $where .= ' AND (b.vchfolio LIKE ? OR b.pkvchid_bis LIKE ? OR c.vchrazon_social LIKE ?
                OR b.vchsolicita LIKE ? OR b.vchafiliado LIKE ? OR b.vchplacas LIKE ?
                OR CONCAT_WS(" ", e.vchnombre, e.vchapellidos) LIKE ? OR u.vchnombre_grua LIKE ?)';
            $like = '%' . $buscar . '%';
            for ($i = 0; $i < 8; $i++) $params[] = $like;
        }
        $total = (int)$this->consulta($pdo,
            'SELECT COUNT(*) FROM ' . $this->tabla('tblbitacora') . ' WHERE fkintid_sucursal = ?',
            [$this->sucursalActual()])->fetchColumn();
        $filtrados = $buscar === '' ? $total : (int)$this->consulta($pdo, 'SELECT COUNT(*)' . $from . $where, $params)->fetchColumn();
        $sql = 'SELECT b.vchfolio AS folio, b.pkvchid_bis, b.intno_serv, b.dtfecha_serv,
                    c.vchrazon_social AS cliente, b.vchsolicita AS solicitante,
                    b.vchafiliado AS asegurado,
                    CONCAT_WS(" ", cv.vchconcepto, m.vchmarca, t.vchtipo) AS vehiculo,
                    b.vchplacas AS placas,
                    CONCAT_WS(" ", e.vchnombre, e.vchapellidos) AS operador,
                    u.vchnombre_grua AS grua, b.vchdir_contacto AS contacto,
                    b.vchdir_entrega AS destino, b.vchtipo_servicio AS local_foraneo,
                    b.vchestatus AS estatus, b.mnsaldo_orden AS total' . $from . $where .
                ' ORDER BY b.dtfecha_serv DESC, b.pkvchid_bis DESC LIMIT ? OFFSET ?';
        $params[] = max(1, min($limite, 100));
        $params[] = max(0, $inicio);
        return ['items' => $this->consulta($pdo, $sql, $params)->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total, 'filtrados' => $filtrados];
    }

    /**
     * Recupera una orden completa para hidratar el formulario de edición.
     *
     * @param string $bis Identificador pkvchid_bis.
     * @return array
     */
    public function ObtenerOrden(string $bis): array
    {
        $pdo = $this->obtenerConexion();
        $this->cargarEsquema($pdo);
        $sql = 'SELECT b.*, t.fkintid_marca, t.intid_clasvehiculo,
                    cv.vchconcepto AS clasificacion_texto, m.vchmarca AS marca_texto,
                    t.vchtipo AS tipo_texto,
                    CONCAT_WS(" ", e.vchnombre, e.vchapellidos) AS operador_texto,
                    u.vchnombre_grua AS grua_texto
                FROM ' . $this->tabla('tblbitacora') . ' b
                LEFT JOIN ' . $this->tabla('TblTipo') . ' t ON t.pkintid_tipo = b.fkintid_tipo
                LEFT JOIN ' . $this->tabla('TblMarca') . ' m ON m.pkintid_marca = t.fkintid_marca
                LEFT JOIN ' . $this->tabla('TblClas_Vehiculo') . ' cv
                    ON cv.pkintid_clasvehiculo = b.fkintid_clasvehiculo
                LEFT JOIN ' . $this->tabla('tblempleados') . ' e ON e.pkintid_empleado = b.fkintid_empleado
                LEFT JOIN ' . $this->tabla('tblunidades') . ' u ON u.pkintid_grua = b.fkintid_grua
                WHERE b.pkvchid_bis = ? AND b.fkintid_sucursal = ?';
        $cabecera = $this->consulta($pdo, $sql, [$bis, $this->sucursalActual()])->fetch(PDO::FETCH_ASSOC);
        if (!$cabecera) {
            throw new RuntimeException('La orden no existe o pertenece a otra sucursal.');
        }

        $detalleSql = 'SELECT d.fkintid_tarifa AS idconcepto,
                    d.fkintid_cliente_sucursal AS idcliente, d.fkintid_tipocargo AS idcargo,
                    c.vchrazon_social AS cliente, COALESCE(t.vchleyenda, t.vchdescripcion) AS servicio,
                    tc.vchdescripcion AS cargo, d.dmcantidad AS cantidad,
                    d.mnprecio_unitario AS precio, t.mnprecio_unitario AS precioBase,
                    d.mnsubtotal AS subtotal, d.mniva AS iva,
                    d.mnretencion AS retencion, d.mnsaldo AS total,
                    d.vchkm_cubre AS km, d.vchkm_cubrenomina AS kmn,
                    d.vchcomp_pago AS docto, d.vchno_autorizacion AS asistencia,
                    d.vchno_expediente AS expendiente, (d.bitapl_comision + 0) AS ac,
                    (s.bitretencion + 0) AS retencionAplica
                FROM ' . $this->tabla('tbldesgloce_serv') . ' d
                LEFT JOIN ' . $this->tabla('TblClientes_Sucursal') . ' cs
                    ON cs.pkintid_cliente_sucursal = d.fkintid_cliente_sucursal
                LEFT JOIN ' . $this->tabla('TblClientes') . ' c ON c.pkintid_cliente = cs.fkintid_cliente
                LEFT JOIN ' . $this->tabla('TblTarifas') . ' t ON t.pkintid_tarifa = d.fkintid_tarifa
                LEFT JOIN ' . $this->tabla('tblservicios') . ' s ON s.pkintid_servicio = t.fkintid_servicio
                LEFT JOIN ' . $this->tabla('TblTipoCargo') . ' tc ON tc.pkintid_tipocargo = d.fkintid_tipocargo
                WHERE d.fkvchid_bis = ? ORDER BY d.pkid_desgloce';
        $detalle = $this->consulta($pdo, $detalleSql, [$bis])->fetchAll(PDO::FETCH_ASSOC);

        $cita = null;
        if (isset($this->schema['tblbitacora_cita'])) {
            $cita = $this->consulta($pdo,
                'SELECT dtfecha_cita, intduracion_min AS duracion, vchobservacion AS observacion
                 FROM ' . $this->tabla('tblbitacora_cita') . '
                 WHERE fkvchid_bis = ? AND vchestatus = "A" ORDER BY pkintid_cita DESC LIMIT 1',
                [$bis])->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        if ($cita) {
            $fechaCita = new DateTimeImmutable($cita['dtfecha_cita']);
            $cita['fecha'] = $fechaCita->format('Y-m-d');
            $cita['hora'] = $fechaCita->format('H:i');
            unset($cita['dtfecha_cita']);
        }

        return [
            'pkvchid_bis' => $cabecera['pkvchid_bis'],
            'folio' => $cabecera['vchfolio'],
            'intno_serv' => (int)$cabecera['intno_serv'],
            'fecha' => $cabecera['dtfecha_serv'],
            'vehiculo' => [
                'tipo' => (int)$cabecera['fkintid_clasvehiculo'],
                'tipoTexto' => $cabecera['clasificacion_texto'],
                'marca' => (int)$cabecera['fkintid_marca'],
                'marcaTexto' => $cabecera['marca_texto'],
                'clase' => (int)$cabecera['fkintid_tipo'],
                'claseTexto' => $cabecera['tipo_texto'],
                'color' => $cabecera['vchcolor'], 'placa' => $cabecera['vchplacas'],
                'modelo' => $cabecera['vchanio'], 'serie' => $cabecera['vchno_serie'],
                'motor' => $cabecera['vchno_motor']
            ],
            'servicio' => [
                'solicita' => $cabecera['vchsolicita'], 'asegurado' => $cabecera['vchafiliado'],
                'contacto' => $cabecera['vchdir_contacto'], 'etiqcont' => $cabecera['vchetiqueta_contacto'],
                'termino' => $cabecera['vchdir_entrega'], 'etiqterm' => $cabecera['vchetiqueta_destino'],
                'telefono' => $cabecera['vchtelefono_otro'], 'celular' => $cabecera['vchtelefono_cel'],
                'clave' => (int)$cabecera['fkintid_clave'],
                'operador' => (int)$cabecera['fkintid_empleado'], 'operadorTexto' => $cabecera['operador_texto'],
                'grua' => (int)$cabecera['fkintid_grua'], 'gruaTexto' => $cabecera['grua_texto'],
                'promesa' => (int)$cabecera['inttiempo_promesa_min'], 'camino' => $cabecera['vchservicio']
            ],
            'opciones' => [
                'local' => $cabecera['vchtipo_servicio'] === 'L',
                'foraneo' => $cabecera['vchtipo_servicio'] === 'F',
                'citas' => (bool)$cabecera['bitcita']
            ],
            'ubicaciones' => [
                'contacto' => array_merge(['direccion' => $cabecera['vchdir_contacto']], $this->parsearCoordenadas($cabecera['vchcampo1'])),
                'termino' => array_merge(['direccion' => $cabecera['vchdir_entrega']], $this->parsearCoordenadas($cabecera['vchcampo2']))
            ],
            'cita' => $cita,
            'comentarios' => $cabecera['vchobservaciones1'],
            'observaciones' => $cabecera['vchobservaciones'],
            'ubicacion' => $cabecera['vchubicacion'],
            'detalle' => $detalle
        ];
    }

    /* =========================================================
     * CREACIÓN DE ORDEN
     * ========================================================= */

    public function GuardarOrden(array $orden): array
    {
        self::validar($orden);
        /***
         * TODO: descomentar para dinamismo
         */
        //$sucursal = self::id(self::valorSesion('cveSucursal'), 'sucursal de la sesión');
        //$usuario = self::id(self::valorSesion('IdUsuario'), 'usuario de la sesión');
        $sucursal = 25;
        $usuario = 2;
        return Conexion::transaction(function (PDO $pdo) use ($orden, $sucursal, $usuario) {
            $this->cargarEsquema($pdo);
            foreach (['tblsucursal', 'tblbitacora', 'tbltiempo_km', 'tbldesgloce_serv', 'tblempleados', 'tblunidades'] as $table) {
                $this->tabla($table, true);
            }
            $this->exigirColumnas('tblsucursal', ['pkintid_sucursal', 'intno_bitacora', 'vchletra', 'dciva', 'dcretencion']);
            $this->exigirColumnas('tblempleados', ['pkintid_empleado', 'bitdisponible', 'bitestatus', 'fkintid_puesto', 'bitcomodin']);
            $this->exigirColumnas('tblunidades', ['pkintid_grua', 'bitservicio_asig']);
            $branch = $this->consulta($pdo, 'SELECT pkintid_sucursal, intno_bitacora, vchletra, dciva, dcretencion FROM ' . $this->tabla('tblsucursal') . ' WHERE pkintid_sucursal = ? FOR UPDATE', [$sucursal])->fetch(PDO::FETCH_ASSOC);
            if (!$branch || !trim((string)$branch['vchletra']) || !is_numeric($branch['intno_bitacora']) || (int)$branch['intno_bitacora'] < 0) {
                throw new RuntimeException('La sucursal no tiene un consecutivo válido configurado.');
            }
            // Real tblsucursal rates are fractions (0.16 / 0.04); detail rates are percentages.
            $tasa = self::numero($branch['dciva'], 'IVA configurado en la sucursal');
            $retencion = self::numero($branch['dcretencion'], 'retención configurada en la sucursal');
            if ($tasa > 1 || $retencion > 1) throw new RuntimeException('Revise las tasas fiscales de la sucursal.');
            $numero = (int)$branch['intno_bitacora'] + 1;
            $folio = trim($branch['vchletra']) . str_pad((string)$numero, 7, '0', STR_PAD_LEFT);
            $bis = $folio . '-1';
            $v = $orden['vehiculo']; $s = $orden['servicio'];
            $tipo = $this->consulta($pdo, 'SELECT t.pkintid_tipo FROM ' . $this->tabla('TblTipo') . ' t INNER JOIN ' . $this->tabla('TblMarca') . ' m ON m.PKINTID_MARCA = t.fkintid_marca INNER JOIN ' . $this->tabla('TblClas_Vehiculo') . ' cv ON cv.PKINTID_CLASVEHICULO = t.intid_clasvehiculo WHERE t.pkintid_tipo = ? AND t.fkintid_marca = ? AND t.intid_clasvehiculo = ? AND t.fkintid_sucursal = ? AND m.BITESTATUS = 1', [$v['clase'], $v['marca'], $v['tipo'], $sucursal])->fetch();
            if (!$tipo) throw new InvalidArgumentException('Marca y Tipo deben corresponder al vehículo y a la sucursal.');
            if (!$this->consulta($pdo, 'SELECT pkintid_clave FROM ' . $this->tabla('tblclave') . ' WHERE pkintid_clave = ? AND bitestado = 1', [$s['clave']])->fetch()) {
                throw new InvalidArgumentException('La clave del servicio no existe.');
            }
            $operador = $this->consulta($pdo, 'SELECT (bitdisponible + 0) AS bitdisponible, (bitestatus + 0) AS bitestatus, fkintid_puesto, (bitcomodin + 0) AS bitcomodin FROM ' . $this->tabla('tblempleados') . ' WHERE pkintid_empleado = ? FOR UPDATE', [$s['operador']])->fetch(PDO::FETCH_ASSOC);
            if (!$operador || !(int)$operador['bitdisponible'] || !(int)$operador['bitestatus'] || ((int)$operador['fkintid_puesto'] !== 5 && !(int)$operador['bitcomodin'])) {
                throw new RuntimeException('El operador está ocupado o no está habilitado para el servicio.');
            }
            $unidad = $this->consulta($pdo, 'SELECT (bitservicio_asig + 0) AS ocupado FROM ' . $this->tabla('tblunidades') . ' WHERE pkintid_grua = ? FOR UPDATE', [$s['grua']])->fetch(PDO::FETCH_ASSOC);
            if (!$unidad || (int)$unidad['ocupado']) throw new RuntimeException('La grúa está ocupada o no existe.');
            if (!$this->consulta($pdo, 'SELECT fkintid_grua FROM ' . $this->tabla('tblgruaxchofer') . ' WHERE fkintid_grua = ? AND fkintid_empleado = ?', [$s['grua'], $s['operador']])->fetch()) {
                throw new InvalidArgumentException('La grúa no está asignada al operador seleccionado.');
            }
            $localForaneo = $orden['opciones']['local'] === true ? 'L' : 'F';
            $detalle = $this->prepararDetalle($pdo, $orden['detalle'], $sucursal, $localForaneo, $tasa, $retencion);
            $now = $this->consulta($pdo, 'SELECT NOW() AS fecha')->fetch(PDO::FETCH_ASSOC)['fecha'];
            $data = [
                'vchfolio' => $folio, 'pkvchid_bis' => $bis, 'vchbitacora' => $folio, 'intno_serv' => 1,
                'dtfecha_serv' => $now, 'vchsolicita' => self::texto($s['solicita'] ?? ''),
                'vchafiliado' => self::texto($s['asegurado'] ?? ''),
                'fkintid_sucursal' => $sucursal, 'fkintid_clave' => $s['clave'], 'fkintid_tipo' => $v['clase'],
                'vchanio' => self::texto($v['modelo'] ?? ''), 'vchcolor' => self::texto($v['color'] ?? ''),
                'vchplacas' => self::texto($v['placa'] ?? ''), 'vchno_serie' => self::texto($v['serie'] ?? ''),
                'vchno_motor' => self::texto($v['motor'] ?? ''), 'fkintid_grua' => $s['grua'],
                'fkintid_cliente_sucursal' => $detalle[0]['fkintid_cliente_sucursal'],
                'vchdir_contacto' => self::texto($s['contacto']), 'vchdir_entrega' => self::texto($s['termino']),
                'vchetiqueta_contacto' => self::texto($s['etiqcont'] ?? ''),
                'vchetiqueta_destino' => self::texto($s['etiqterm'] ?? ''),
                'inttiempo_promesa_min' => empty($s['promesa']) ? 0 : self::id($s['promesa'], 'tiempo promesa en minutos'),
                'vchtelefono_cel' => self::texto($s['celular'] ?? ''), 'vchtelefono_otro' => self::texto($s['telefono'] ?? ''),
                'fkintid_usuario' => $usuario, 'dtfecha_usuario' => substr($now, 0, 10), 'fkintid_empleado' => $s['operador'],
                'vchservicio' => $s['camino'] ?? 'S', 'fkintid_clasvehiculo' => $v['tipo'], 'vchtipo_servicio' => $localForaneo,
                'vchestatus' => 'A', 'vchobservaciones' => self::texto($orden['observaciones'] ?? ''),
                'vchobservaciones1' => self::texto($orden['comentarios'] ?? ''), 'vchubicacion' => self::texto($orden['ubicacion'] ?? ''),
                'vchcampo1' => $this->coordenadas($orden['ubicaciones']['contacto'] ?? []),
                'vchcampo2' => $this->coordenadas($orden['ubicaciones']['termino'] ?? [])
            ];
            $data['mnsaldo_orden'] = round(array_sum(array_column($detalle, 'mnsaldo')), 2);
            foreach (['bitvalidado', 'bitliquidar', 'bitvale', 'bitadmision', 'bitinventario', 'bitllaves', 'bithoja_calidad', 'bitliberacion', 'bitliberado', 'bitestadia', 'bitvalidac50', 'mncantidad_cobro'] as $field) $data[$field] = 0;
            // Cita is stored only in the header fields already referenced by the legacy code.
            if ($this->existeColumna('tblbitacora', 'bitcita') && $this->existeColumna('tblbitacora', 'dtfecha_cita')) {
                $data['bitcita'] = ($orden['opciones']['citas'] ?? false) === true ? 1 : 0;
                if ($data['bitcita']) $data['dtfecha_cita'] = self::fechaCita($orden['cita']);
            }
            $this->insertar($pdo, 'tblbitacora', $data);
            $this->insertarTiempoInicial($pdo, $bis, $now, $orden);
            $this->insertarDetalle($pdo, $bis, $usuario, $now, $detalle);
            if (($orden['opciones']['citas'] ?? false) === true && isset($this->schema['tblbitacora_cita'])) {
                $this->insertar($pdo, 'tblbitacora_cita', [
                    'fkvchid_bis' => $bis, 'dtfecha_cita' => self::fechaCita($orden['cita']),
                    'intduracion_min' => (int)$orden['cita']['duracion'],
                    'vchobservacion' => self::texto($orden['cita']['observacion'] ?? ''),
                    'vchestatus' => 'A', 'fkintid_usuario' => $usuario, 'dtfecha_captura' => $now
                ]);
            }
            if ($this->consulta($pdo, 'UPDATE ' . $this->tabla('tblempleados', true) . ' SET bitdisponible = 0 WHERE pkintid_empleado = ? AND bitdisponible = 1', [$s['operador']])->rowCount() !== 1) {
                throw new RuntimeException('El operador dejó de estar disponible.');
            }
            if ($this->consulta($pdo, 'UPDATE ' . $this->tabla('tblunidades', true) . ' SET bitservicio_asig = 1 WHERE pkintid_grua = ? AND bitservicio_asig = 0', [$s['grua']])->rowCount() !== 1) {
                throw new RuntimeException('La grúa dejó de estar disponible.');
            }
            if ($this->consulta($pdo, 'UPDATE ' . $this->tabla('tblsucursal', true) . ' SET intno_bitacora = ? WHERE pkintid_sucursal = ?', [$numero, $sucursal])->rowCount() !== 1) {
                throw new RuntimeException('No fue posible actualizar el consecutivo.');
            }
            return ['orden' => $folio, 'folio' => $folio, 'pkvchid_bis' => $bis, 'intno_serv' => 1];
        }, $this->pdo);
    }

    /* =========================================================
     * EDICIÓN DE ORDEN
     * ========================================================= */

    /**
     * Actualiza los datos capturables sin regenerar folio, BIS, servicio o tiempos.
     *
     * @param string $bis Identificador inmutable de la orden.
     * @param array $orden Datos normalizados por el formulario compartido.
     * @return array
     */
    public function ActualizarOrden(string $bis, array $orden): array
    {
        self::validar($orden);
        $sucursal = $this->sucursalActual();
        $usuario = $this->usuarioActual();

        return Conexion::transaction(function (PDO $pdo) use ($bis, $orden, $sucursal, $usuario) {
            $this->cargarEsquema($pdo);
            foreach (['tblsucursal', 'tblbitacora', 'tbltiempo_km', 'tbldesgloce_serv', 'tblempleados', 'tblunidades'] as $table) {
                $this->tabla($table, true);
            }

            $cabecera = $this->consulta($pdo,
                'SELECT vchfolio, pkvchid_bis, intno_serv, fkintid_empleado, fkintid_grua
                 FROM ' . $this->tabla('tblbitacora') . '
                 WHERE pkvchid_bis = ? AND fkintid_sucursal = ? FOR UPDATE',
                [$bis, $sucursal])->fetch(PDO::FETCH_ASSOC);
            if (!$cabecera) {
                throw new RuntimeException('La orden no existe o pertenece a otra sucursal.');
            }

            $vehiculo = $orden['vehiculo'];
            $servicio = $orden['servicio'];
            $this->validarVehiculo($pdo, $vehiculo, $sucursal);
            $this->validarClave($pdo, (int)$servicio['clave']);

            $fiscal = $this->consulta($pdo,
                'SELECT dciva, dcretencion FROM ' . $this->tabla('tblsucursal') . ' WHERE pkintid_sucursal = ? FOR UPDATE',
                [$sucursal])->fetch(PDO::FETCH_ASSOC);
            if (!$fiscal) throw new RuntimeException('No fue posible cargar la configuración de la sucursal.');
            $tasa = self::numero($fiscal['dciva'], 'IVA configurado en la sucursal');
            $retencion = self::numero($fiscal['dcretencion'], 'retención configurada en la sucursal');
            if ($tasa > 1 || $retencion > 1) throw new RuntimeException('Revise las tasas fiscales de la sucursal.');

            $localForaneo = ($orden['opciones']['local'] ?? false) === true ? 'L' : 'F';
            $detalle = $this->prepararDetalle($pdo, $orden['detalle'], $sucursal, $localForaneo, $tasa, $retencion);
            $operadorAnterior = (int)$cabecera['fkintid_empleado'];
            $gruaAnterior = (int)$cabecera['fkintid_grua'];
            $operadorNuevo = (int)$servicio['operador'];
            $gruaNueva = (int)$servicio['grua'];

            $this->validarRecursosEdicion($pdo, $operadorAnterior, $operadorNuevo, $gruaAnterior, $gruaNueva);

            $datosCabecera = [
                'vchsolicita' => self::texto($servicio['solicita'] ?? ''),
                'vchafiliado' => self::texto($servicio['asegurado'] ?? ''),
                'fkintid_clave' => (int)$servicio['clave'], 'fkintid_tipo' => (int)$vehiculo['clase'],
                'vchanio' => self::texto($vehiculo['modelo'] ?? ''), 'vchcolor' => self::texto($vehiculo['color'] ?? ''),
                'vchplacas' => self::texto($vehiculo['placa'] ?? ''), 'vchno_serie' => self::texto($vehiculo['serie'] ?? ''),
                'vchno_motor' => self::texto($vehiculo['motor'] ?? ''),
                'fkintid_grua' => $gruaNueva, 'fkintid_empleado' => $operadorNuevo,
                'fkintid_cliente_sucursal' => $detalle[0]['fkintid_cliente_sucursal'],
                'vchdir_contacto' => self::texto($servicio['contacto']),
                'vchetiqueta_contacto' => self::texto($servicio['etiqcont'] ?? ''),
                'vchdir_entrega' => self::texto($servicio['termino']),
                'vchetiqueta_destino' => self::texto($servicio['etiqterm'] ?? ''),
                'inttiempo_promesa_min' => empty($servicio['promesa']) ? 0 : (int)$servicio['promesa'],
                'vchtelefono_cel' => self::texto($servicio['celular'] ?? ''),
                'vchtelefono_otro' => self::texto($servicio['telefono'] ?? ''),
                'fkintid_usuario' => $usuario, 'dtfecha_usuario' => date('Y-m-d'),
                'vchservicio' => $servicio['camino'] ?? 'S',
                'fkintid_clasvehiculo' => (int)$vehiculo['tipo'], 'vchtipo_servicio' => $localForaneo,
                'vchobservaciones' => self::texto($orden['observaciones'] ?? ''),
                'vchobservaciones1' => self::texto($orden['comentarios'] ?? ''),
                'vchubicacion' => self::texto($orden['ubicacion'] ?? ''),
                // vchcampo1/2 son los campos legacy oficiales para coordenadas "lat,lon".
                'vchcampo1' => $this->coordenadas($orden['ubicaciones']['contacto'] ?? []),
                'vchcampo2' => $this->coordenadas($orden['ubicaciones']['termino'] ?? []),
                'mnsaldo_orden' => round(array_sum(array_column($detalle, 'mnsaldo')), 2),
                'bitcita' => ($orden['opciones']['citas'] ?? false) === true ? 1 : 0,
                'dtfecha_cita' => ($orden['opciones']['citas'] ?? false) === true ? self::fechaCita($orden['cita']) : null
            ];
            $this->actualizar($pdo, 'tblbitacora', $datosCabecera, 'pkvchid_bis = ? AND fkintid_sucursal = ?', [$bis, $sucursal]);

            $this->reemplazarDetalleOrden($pdo, $bis, $usuario, $detalle);
            $this->cambiarOperadorOrden($pdo, $operadorAnterior, $operadorNuevo);
            $this->cambiarGruaOrden($pdo, $gruaAnterior, $gruaNueva);
            $this->actualizarCita($pdo, $bis, $usuario, $orden);
            $this->actualizarTiempoPromesa($pdo, $bis, (int)($servicio['promesa'] ?? 0));

            return [
                'orden' => $cabecera['vchfolio'], 'folio' => $cabecera['vchfolio'],
                'pkvchid_bis' => $cabecera['pkvchid_bis'], 'intno_serv' => (int)$cabecera['intno_serv']
            ];
        }, $this->pdo);
    }

    private function validarVehiculo(PDO $pdo, array $vehiculo, int $sucursal): void
    {
        $tipo = $this->consulta($pdo,
            'SELECT t.pkintid_tipo FROM ' . $this->tabla('TblTipo') . ' t
             INNER JOIN ' . $this->tabla('TblMarca') . ' m ON m.pkintid_marca = t.fkintid_marca
             WHERE t.pkintid_tipo = ? AND t.fkintid_marca = ? AND t.intid_clasvehiculo = ?
               AND t.fkintid_sucursal = ? AND m.bitestatus = 1',
            [(int)$vehiculo['clase'], (int)$vehiculo['marca'], (int)$vehiculo['tipo'], $sucursal])->fetch();
        if (!$tipo) throw new InvalidArgumentException('Marca y Tipo deben corresponder al vehículo y a la sucursal.');
    }

    private function validarClave(PDO $pdo, int $clave): void
    {
        if (!$this->consulta($pdo,
            'SELECT pkintid_clave FROM ' . $this->tabla('tblclave') . ' WHERE pkintid_clave = ? AND bitestado = 1',
            [$clave])->fetch()) {
            throw new InvalidArgumentException('La clave del servicio no existe.');
        }
    }

    /* =========================================================
     * RECURSOS: OPERADOR / GRÚA
     * ========================================================= */

    /**
     * Permite conservar los recursos ocupados por esta orden, pero exige que todo
     * recurso nuevo continúe disponible y relacionado entre sí.
     */
    private function validarRecursosEdicion(PDO $pdo, int $operadorAnterior, int $operadorNuevo, int $gruaAnterior, int $gruaNueva): void
    {
        $operador = $this->consulta($pdo,
            'SELECT (bitdisponible + 0) AS disponible, (bitestatus + 0) AS activo,
                    fkintid_puesto, (bitcomodin + 0) AS comodin
             FROM ' . $this->tabla('tblempleados') . ' WHERE pkintid_empleado = ? FOR UPDATE',
            [$operadorNuevo])->fetch(PDO::FETCH_ASSOC);
        if (!$operador || !(int)$operador['activo'] || ((int)$operador['fkintid_puesto'] !== 5 && !(int)$operador['comodin'])) {
            throw new RuntimeException('El operador no está habilitado para el servicio.');
        }
        if ($operadorNuevo !== $operadorAnterior && !(int)$operador['disponible']) {
            throw new RuntimeException('El nuevo operador está ocupado.');
        }

        $grua = $this->consulta($pdo,
            'SELECT (bitservicio_asig + 0) AS ocupada FROM ' . $this->tabla('tblunidades') . '
             WHERE pkintid_grua = ? FOR UPDATE', [$gruaNueva])->fetch(PDO::FETCH_ASSOC);
        if (!$grua || ($gruaNueva !== $gruaAnterior && (int)$grua['ocupada'])) {
            throw new RuntimeException('La nueva grúa está ocupada o no existe.');
        }
        if (!$this->consulta($pdo,
            'SELECT fkintid_grua FROM ' . $this->tabla('tblgruaxchofer') . '
             WHERE fkintid_grua = ? AND fkintid_empleado = ?', [$gruaNueva, $operadorNuevo])->fetch()) {
            throw new InvalidArgumentException('La grúa no está asignada al operador seleccionado.');
        }
    }

    private function reemplazarDetalleOrden(PDO $pdo, string $bis, int $usuario, array $detalle): void
    {
        $this->consulta($pdo, 'DELETE FROM ' . $this->tabla('tbldesgloce_serv', true) . ' WHERE fkvchid_bis = ?', [$bis]);
        $now = $this->consulta($pdo, 'SELECT NOW() AS fecha')->fetch(PDO::FETCH_ASSOC)['fecha'];
        $this->insertarDetalle($pdo, $bis, $usuario, $now, $detalle);
    }

    private function cambiarOperadorOrden(PDO $pdo, int $anterior, int $nuevo): void
    {
        if ($anterior === $nuevo) return;
        $this->consulta($pdo, 'UPDATE ' . $this->tabla('tblempleados', true) . ' SET bitdisponible = 1 WHERE pkintid_empleado = ?', [$anterior]);
        if ($this->consulta($pdo,
            'UPDATE ' . $this->tabla('tblempleados', true) . ' SET bitdisponible = 0
             WHERE pkintid_empleado = ? AND bitdisponible = 1', [$nuevo])->rowCount() !== 1) {
            throw new RuntimeException('El nuevo operador dejó de estar disponible.');
        }
    }

    private function cambiarGruaOrden(PDO $pdo, int $anterior, int $nueva): void
    {
        if ($anterior === $nueva) return;
        $this->consulta($pdo, 'UPDATE ' . $this->tabla('tblunidades', true) . ' SET bitservicio_asig = 0 WHERE pkintid_grua = ?', [$anterior]);
        if ($this->consulta($pdo,
            'UPDATE ' . $this->tabla('tblunidades', true) . ' SET bitservicio_asig = 1
             WHERE pkintid_grua = ? AND bitservicio_asig = 0', [$nueva])->rowCount() !== 1) {
            throw new RuntimeException('La nueva grúa dejó de estar disponible.');
        }
    }

    private function actualizarCita(PDO $pdo, string $bis, int $usuario, array $orden): void
    {
        if (!isset($this->schema['tblbitacora_cita'])) return;
        $activa = ($orden['opciones']['citas'] ?? false) === true;
        $existente = $this->consulta($pdo,
            'SELECT pkintid_cita FROM ' . $this->tabla('tblbitacora_cita') . '
             WHERE fkvchid_bis = ? ORDER BY pkintid_cita DESC LIMIT 1 FOR UPDATE', [$bis])->fetch(PDO::FETCH_ASSOC);
        if (!$activa) {
            if ($existente) $this->actualizar($pdo, 'tblbitacora_cita', ['vchestatus' => 'I'], 'pkintid_cita = ?', [(int)$existente['pkintid_cita']]);
            return;
        }

        $fecha = self::fechaCita($orden['cita']);
        $data = [
            'dtfecha_cita' => $fecha, 'intduracion_min' => (int)$orden['cita']['duracion'],
            'vchobservacion' => self::texto($orden['cita']['observacion'] ?? ''), 'vchestatus' => 'A',
            'fkintid_usuario_modificacion' => $usuario, 'dtfecha_modificacion' => date('Y-m-d H:i:s')
        ];
        if ($existente) {
            $this->actualizar($pdo, 'tblbitacora_cita', $data, 'pkintid_cita = ?', [(int)$existente['pkintid_cita']]);
        } else {
            unset($data['fkintid_usuario_modificacion'], $data['dtfecha_modificacion']);
            $this->insertar($pdo, 'tblbitacora_cita', array_merge([
                'fkvchid_bis' => $bis, 'fkintid_usuario' => $usuario, 'dtfecha_captura' => date('Y-m-d H:i:s')
            ], $data));
        }
    }

    private function actualizarTiempoPromesa(PDO $pdo, string $bis, int $minutos): void
    {
        // Nunca se reemplaza tbltiempo_km: sólo se ajusta la promesa mientras el contacto real no exista.
        $sql = 'UPDATE ' . $this->tabla('tbltiempo_km', true) . '
                SET dtcontacto_aprox = CASE
                    WHEN ? > 0 THEN DATE_ADD(dtllamada, INTERVAL ? MINUTE)
                    ELSE NULL END
                WHERE pkvchid_bis = ? AND dtcontacto IS NULL';
        $this->consulta($pdo, $sql, [$minutos, $minutos, $bis]);
    }

    /* =========================================================
     * TIEMPOS Y UBICACIONES
     * ========================================================= */

    private function insertarTiempoInicial(PDO $pdo, string $bis, string $now, array $orden): void
    {
        $data = [
            'pkvchid_bis' => $bis,
            'dtllamada' => $now,
            'bitsm' => 0,
            'bitchecado' => 0,
            'bitservconcluido' => 0
        ];

        if ($this->existeColumna('tbltiempo_km', 'intestatus')) {
            $data['intestatus'] = 1;
        }

        $promesa = filter_var(
            $orden['servicio']['promesa'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        if ($promesa !== false) {
            $data['dtcontacto_aprox'] = (new DateTimeImmutable($now))
                ->modify('+' . $promesa . ' minutes')
                ->format('Y-m-d H:i:s');
        }

        $this->insertar($pdo, 'tbltiempo_km', $data);
    }

    private function coordenadas($location): string
    {
        if (!is_array($location) || !isset($location['lat'], $location['lon'])) return '';
        if (!is_numeric($location['lat']) || !is_numeric($location['lon']) || abs((float)$location['lat']) > 90 || abs((float)$location['lon']) > 180) {
            throw new InvalidArgumentException('Las coordenadas de ubicación no son válidas.');
        }
        return $location['lat'] . ',' . $location['lon'];
    }

    /**
     * Convierte el formato legacy "lat,lon" sin intentar geocodificarlo.
     */
    private function parsearCoordenadas(?string $valor): array
    {
        $partes = array_map('trim', explode(',', (string)$valor));
        if (count($partes) !== 2 || !is_numeric($partes[0]) || !is_numeric($partes[1])) {
            return ['lat' => null, 'lon' => null];
        }
        $lat = (float)$partes[0];
        $lon = (float)$partes[1];
        if (abs($lat) > 90 || abs($lon) > 180) return ['lat' => null, 'lon' => null];
        return ['lat' => $lat, 'lon' => $lon];
    }

    /* =========================================================
     * DETALLE DE SERVICIO
     * ========================================================= */

    private function prepararDetalle(PDO $pdo, array $items, int $sucursal, string $tipoServicio, float $tasa, float $retencion): array
    {
        $result = [];
        foreach ($items as $i => $item) {
            $cliente = $this->consulta($pdo, 'SELECT cs.PKINTID_CLIENTE_SUCURSAL FROM ' . $this->tabla('TblClientes_Sucursal') . ' cs INNER JOIN ' . $this->tabla('TblClientes') . ' c ON c.PKINTID_CLIENTE = cs.FKINTID_CLIENTE WHERE cs.PKINTID_CLIENTE_SUCURSAL = ? AND cs.FKINTID_SUCURSAL = ? AND c.BITACTIVO = 1', [$item['idcliente'], $sucursal])->fetch();
            if (!$cliente) throw new InvalidArgumentException('El cliente de la fila ' . ($i + 1) . ' no está activo en la sucursal.');
            $tarifa = $this->consulta($pdo, 'SELECT t.MNPRECIO_UNITARIO AS precio, t.VCHTIPO_SERVICIO AS tipo, (s.bitretencion + 0) AS retencion, (s.bitcomision + 0) AS comision FROM ' . $this->tabla('TblTarifas') . ' t INNER JOIN ' . $this->tabla('tblservicios') . ' s ON s.pkintid_servicio = t.FKINTID_SERVICIO WHERE t.PKINTID_TARIFA = ? AND t.FKINTID_CLIENTE_SUCURSAL = ? AND t.BITACTIVO = 1 AND s.bitactivo = 1 FOR UPDATE', [$item['idconcepto'], $item['idcliente']])->fetch(PDO::FETCH_ASSOC);
            $tipoTarifa = $tarifa ? strtoupper(trim((string)$tarifa['tipo'])) : '';
            if ($tipoTarifa === '') $tipoTarifa = 'A';
            if (!$tarifa || !in_array($tipoTarifa, ['A', $tipoServicio], true)) {
                throw new InvalidArgumentException('La tarifa de la fila ' . ($i + 1) . ' no corresponde al cliente o al servicio.');
            }
            $base = self::numero($tarifa['precio'], 'precio del catálogo');
            $precio = round(self::numero($item['precio'], 'precio'), 4);
            if ($precio < $base) throw new InvalidArgumentException('El precio de la fila ' . ($i + 1) . ' es menor al de la tarifa.');
            $cargos = $this->consulta($pdo, 'SELECT tc.FKINTID_TIPOCARGO AS cargo FROM ' . $this->tabla('TblTipoCargoClienteSucursal') . ' tc INNER JOIN ' . $this->tabla('tbltipocargo') . ' c ON c.pkintid_tipocargo = tc.FKINTID_TIPOCARGO WHERE tc.FKINTID_CLIENTE_SUCURSAL = ? AND c.bitactivo = 1', [$item['idcliente']])->fetchAll(PDO::FETCH_ASSOC);
            $cargo = empty($item['idcargo']) ? null : (int)$item['idcargo'];
            if (($cargos || $cargo !== null) && !in_array($cargo, array_map(function ($row) { return (int)$row['cargo']; }, $cargos), true)) {
                throw new InvalidArgumentException('Seleccione un tipo de cargo válido en la fila ' . ($i + 1) . '.');
            }
            $cantidad = round(self::numero($item['cantidad'], 'cantidad', true), 3);
            if ($cantidad <= 0) throw new InvalidArgumentException('La cantidad debe ser al menos 0.001.');
            $subtotal = round($cantidad * $precio, 2);
            $iva = round($subtotal * $tasa, 2);
            $tasaRetencion = (int)$tarifa['retencion'] ? $retencion : 0;
            $importeRetencion = round($subtotal * $tasaRetencion, 2);
            $total = round($subtotal + $iva - $importeRetencion, 2);
            if (!is_finite($total)) throw new InvalidArgumentException('El importe del concepto es demasiado grande.');
            $result[] = ['dmcantidad' => $cantidad, 'mnprecio_unitario' => $precio,
                'fkintid_tarifa' => $item['idconcepto'], 'fkintid_tipocargo' => $cargo,
                'fkintid_cliente_sucursal' => $item['idcliente'], 'mnsubtotal' => $subtotal,
                'mniva' => $iva, 'dmiva' => $tasa * 100, 'mnimporte_org' => $subtotal, 'mnsaldo' => $total,
                'mnretencion' => $importeRetencion, 'dmretencion' => $tasaRetencion * 100,
                'bitapl_comision' => (int)$tarifa['comision'],
                'mnmonto_pago' => 0, 'vchkm_cubre' => self::texto($item['km'] ?? ''),
                'vchkm_cubrenomina' => self::texto($item['kmn'] ?? ''), 'vchcomp_pago' => self::texto($item['docto'] ?? '') ?: 'NOTA DE VENTA',
                'vchno_autorizacion' => self::texto($item['asistencia'] ?? ''),
                'vchno_expediente' => self::texto($item['expendiente'] ?? '')];
        }
        return $result;
    }

    private function insertarDetalle(PDO $pdo, string $bis, int $usuario, string $fecha, array $items): void
    {
        foreach ($items as $item) {
            $this->insertar($pdo, 'tbldesgloce_serv', array_merge($item,
                ['fkvchid_bis' => $bis, 'fkintid_usuario' => $usuario, 'dtfecha_captura' => $fecha]));
        }
    }
}
