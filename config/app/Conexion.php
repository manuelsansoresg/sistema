<?php

    class Conexion
    {
        private $conect;

        public function __construct()
        {
            $connectionString = "mysql:host=". DB_HOST . ";dbname=" . DB . ";charset=" . DB_CHARSET;
            try {
                $this->conect = new PDO($connectionString, DB_USER, DB_PASS);
                $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // echo "Se ha conectado a la dB";

            } catch (PDOException $e) {
                throw new RuntimeException('No se pudo conectar con la base de datos.', 0, $e);
            }
        }

        public function conect()
        {
            return $this->conect;
        }
        public static function query($sql, $params = [])
        {
            try {

                $db = new Conexion();
                $link = $db->conect();

                $sqlLimpio = ltrim($sql);
                $type = strtoupper(strtok($sqlLimpio, " \n\r\t"));

                $useTransaction = in_array($type, [
                    'INSERT',
                    'UPDATE',
                    'DELETE'
                ]);

                if ($useTransaction) {
                    $link->beginTransaction();
                }

                $query = $link->prepare($sql);

                foreach ($params as $key => $value) {
                    if (is_int($value)) {
                        $query->bindValue($key, $value, PDO::PARAM_INT);
                    } elseif (is_bool($value)) {
                        $query->bindValue($key, $value, PDO::PARAM_BOOL);
                    } elseif (is_null($value)) {
                        $query->bindValue($key, null, PDO::PARAM_NULL);
                    } else {
                        $query->bindValue($key, $value, PDO::PARAM_STR);
                    }
                }

                $query->execute();

                if ($type === 'SELECT' || $query->columnCount() > 0) {
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                }

                switch ($type) {
                    case 'INSERT':
                        $id = $link->lastInsertId();
                        $link->commit();
                        return $id;

                    case 'UPDATE':
                        $link->commit();
                        return $query->rowCount();

                    case 'DELETE':
                        $affected = $query->rowCount();

                        if ($affected > 0) {
                            $link->commit();
                            return true;
                        }

                        $link->rollBack();
                        return false;

                    default:
                        if ($useTransaction) {
                            $link->commit();
                        }

                        return true;
                }

            } catch (PDOException $e) {

                if (isset($link) && $link->inTransaction()) {
                    $link->rollBack();
                }

                self::handleDBError($e);
            }
        }
        /**
         BEGIN TRANSACTION
        */
        public static function begin()
        {   
            $db = new Conexion();
            $link = $db->conect();    
            return $link->beginTransaction();
        }
        /**
        * COMMIT
        */
        public static function commit()
        {
            $db = new Conexion();        
            $pdo = $db->conect();
                    
            if ($pdo->inTransaction()) {
                return $pdo->commit();
            }

            return false;
        }

        /**
         * ROLLBACK
         */
        public static function rollback()
        {
            $db = new Conexion();        
            $pdo = $db->conect();

            if ($pdo->inTransaction()) {
                return $pdo->rollBack();
            }

            return false;
        }

        /**
         * SELECT
         */
        public static function select($sql, $params = [])
        {
            $query = self::conect()->prepare($sql);

            $query->execute($params);

            return $query->fetchAll();
        }

        /**
         * SELECT ONE
         */
        public static function First($sql, $params = [])
        {
            $db = new Conexion();        
            $pdo = $db->conect();

            $query = $pdo->prepare($sql);

            $query->execute($params);

            return $query->fetch();
        }

        /**
         * INSERT
         */
        public static function ADD($sql, $params = [])
        {
            try 
            {
                $db = new Conexion();        
                $pdo = $db->conect();

                $query = $pdo->prepare($sql);

                $query->execute($params);
                
                return $pdo->lastInsertId();

            } catch (PDOException $e) 
            {            
                //throw new Exception($e->getMessage());
                 self::handleDBError($e);
            }
        }

        /**
         * UPDATE
         */
        public static function UpdateRegister($sql, $params = [])
        {
            try 
            {
                $db = new Conexion();        
                $pdo = $db->conect();

                $query = $pdo->prepare($sql);

                $query->execute($params);
                
                return $query->rowCount();
            }
            catch (PDOException $e) 
            {            
                //throw new Exception($e->getMessage());
                 self::handleDBError($e);
            }
        }

        /**
         * DELETE
         */
        public static function DeleteRegister($sql, $params = [])
        {
            try {

                $db = new Conexion();
                $pdo = $db->conect();

                $query = $pdo->prepare($sql);
                $query->execute($params);

                return true;

            } catch (PDOException $e) 
            {
                //throw new Exception($e->getMessage());
                 self::handleDBError($e);
            }
        }
        public static function transaction(callable $callback, ?PDO $pdo = null)
        {
            if ($pdo === null) {
                $db = new self();
                $pdo = $db->conect();
            }

            try
            {
                $pdo->beginTransaction();

                $result = $callback($pdo);

                $pdo->commit();

                return $result;
            }
            catch (Throwable $e)
            {
                if ($pdo->inTransaction())
                {
                    $pdo->rollBack();
                }

                throw $e;
            }
        }

        /**
         * ALTER | CREATE | DROP
         */
        public static function statement($sql)
        {
            return self::conect()->exec($sql);
        }

        public static function handleDBError(PDOException $e)
        {
            $message = $e->getMessage();
            
            if ($e->getCode() == 23000 && str_contains($message, '1451'))
            {
                return throw new Exception('No se puede eliminar el registro porque tiene datos relacionados.');
            }
            if ($e->getCode() == 23000 && str_contains($message, '1062'))
            {
                return throw new Exception('El registro ya existe.');
            }           
            if ($e->getCode() == 'HY093')
            {       
                return throw new Exception('Error interno: los parámetros enviados a la consulta no coinciden.');
            }
            //DATA TOO LONG
            if ($e->getCode() == '22001')
            {
                preg_match("/column '([^']+)'/", $message, $matches);

                $column = $matches[1] ?? 'campo';

                return throw new Exception("El valor capturado es demasiado largo para el campo {$column}.");
            }
            //ERROR SQL
            if ($e->getCode() == '42000')
            {
                return throw new Exception('La consulta SQL contiene un error de sintaxis.');
            }

            return throw new Exception($message);
        }
    }