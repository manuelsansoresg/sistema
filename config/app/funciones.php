<?php
    // Elimino duplicados de un array sin importar su tipo. Si $keys es array vacio lo trato como de tipo simple
    function DeleteDuplicados(array $array, array $keys = [], $idem = true): array
    {
        // Comparo los valores e identifico que operador de desigualdad usar
        $comparar = function ($a, $b, $key) use ($idem) 
        {
            if ($idem) {
                return getValor($a, $key) !== getValor($b, $key);
            }
            return getValor($a, $key) != getValor($b, $key);
        };

        // Itero sobre las diferentes $keys y para determinar la duplicidad
        $determinarDuplicidad = function ($a, $b) use ($keys, $comparar) 
        {
            return array_reduce(
                $keys, fn ($acumulador, $key) => ($acumulador === null ? $comparar($a, $b, $key) : $acumulador) || $comparar($a, $b, $key)
            );
        };

        // Itero para y voy eliminado los duplicados usando un criterio de filtro definido por $keys
        return array_reduce($array, fn ($acumulador, $valor) => array_merge(array_filter(
            $acumulador, fn ($valor_filter) => $determinarDuplicidad($valor_filter, $valor)
            ),[$valor]),[]
        );
    }
    // Devuelvo el valor segun el tipo (array, objeto o simple)
    function getValor($valor, $key = null)
    {
        switch (gettype($valor)) 
        {
            case 'object':
                return $valor->{$key};
            case 'array':
                return $valor[$key];
            default:
                return $valor;
        }
    }
    /*
    * Completar 0 a la Izquierda
    *
    * Rellena con ceros a la izquierda
    *
    * @param $valor valor a rellenar
    * @param $long longitud total del valor
    * @return valor rellenado
    */

    function zero_fill($valor, $long = 0)
    {
        return str_pad($valor, $long, '0', STR_PAD_LEFT);
    }
?>
