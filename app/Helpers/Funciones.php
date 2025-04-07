<?php

namespace App\Helpers;

use App\Models\Configuracion;
use Carbon\Carbon;
use Exception;

class Funciones
{
    /**
     * Obtiene los valores de configuración del sistema.
     *
     * @param $clave
     * @return mixed
     */
    public static function get_config($clave): mixed
    {
        return Configuracion::where('clave', $clave)->value('valor');
    }

    public static function getDateFormatDMY($fecha): string
    {
        $fecha = Carbon::parse($fecha)->locale('es');
        return $fecha->format('d/m/Y');
    }

    /**
     * Formatea una fecha a un formato largo legible en español.
     *
     * Convierte una fecha dada a un formato largo, incluyendo el día de la semana, el día del mes, el mes y el año, en español.
     *  Utiliza la librería Carbon para el manejo de fechas y la función `isoFormat` para definir el formato deseado.
     *
     * @param string $fecha La fecha a formatear. Puede ser una cadena de texto que Carbon pueda entender.
     * @return string La fecha formateada en español, con el formato:
     *                "DíaDeLaSemana DD de Mes de YYYY". Por ejemplo: "Lunes 01 de Enero de 2024".
     *                El primer caracter del día de la semana está en mayúscula.
     * @throws Exception Si ocurre un error al parsear la fecha.
     */
    public static function fechaLarga(string $fecha): string
    {
        $fecha_larga = Carbon::parse($fecha)->locale('es');
        return ucfirst($fecha_larga->isoFormat('dddd DD \d\e MMMM \d\e YYYY'));
    }

    public static function mostrarComoMoneda($importe): string
    {
        return "$ " . number_format($importe, 2, ',', '.');
    }

    /**
     * Resalta el texto que coincide con el filtro
     *
     * @param $texto
     * @param $filtro
     * @return array|string|null
     */
    public static function resaltar($texto, $filtro): array|string|null
    {
        if ($filtro !== '') {
            // Eliminar puntos del filtro
            $filtro_limpio = str_replace('.', '', $filtro);

            // Buscar la posición de la coincidencia en el texto sin puntos
            $texto_limpio = str_replace('.', '', $texto);
            $pos = stripos($texto_limpio, $filtro_limpio);

            if ($pos !== false) {
                // Contar cuántos puntos hay antes de la posición encontrada
                $puntos_antes = substr_count(substr($texto, 0, $pos), '.');

                // Ajustar la posición en el texto original considerando los puntos
                $pos_original = $pos + $puntos_antes;

                // Calcular la longitud considerando los puntos dentro del rango
                $longitud = strlen($filtro_limpio);
                $puntos_dentro = substr_count(substr($texto, $pos_original, $longitud + 2), '.');
                $longitud_total = $longitud + $puntos_dentro;

                // Extraer y resaltar la parte coincidente del texto original
                $parte_inicial = substr($texto, 0, $pos_original);
                $parte_coincidente = substr($texto, $pos_original, $longitud_total);
                $parte_final = substr($texto, $pos_original + $longitud_total);

                return $parte_inicial .
                    '<span style="background-color: yellow; color: red">' .
                    $parte_coincidente .
                    '</span>' .
                    $parte_final;
            }
        }
        return $texto;
    }
}
