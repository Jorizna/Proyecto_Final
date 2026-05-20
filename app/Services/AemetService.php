<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AemetService
{
    private const BASE_URL = 'https://opendata.aemet.es/opendata/api';
    private const CACHE_TTL = 1800; // 30 minutos

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.aemet.key', '');
    }

    public function getPrediccion(float $latitud, float $longitud): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $cacheKey = "aemet_{$latitud}_{$longitud}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($latitud, $longitud) {
            return $this->fetchPrediccion($latitud, $longitud);
        });
    }

    private function fetchPrediccion(float $latitud, float $longitud): ?array
    {
        try {
            // Paso 1: obtener el municipio más cercano
            $municipioId = $this->getMunicipioCercano($latitud, $longitud);

            if (!$municipioId) {
                return null;
            }

            // Paso 2: obtener la predicción del municipio
            $urlResponse = Http::withHeaders([
                'api_key' => $this->apiKey,
                'Accept'  => 'application/json',
            ])->timeout(10)->get(self::BASE_URL . "/prediccion/especifica/municipio/diaria/{$municipioId}");

            if (!$urlResponse->successful()) {
                return null;
            }

            $datos = $urlResponse->json();

            if (($datos['estado'] ?? 0) !== 200 || empty($datos['datos'])) {
                return null;
            }

            // Paso 3: seguir el enlace a los datos reales
            $dataResponse = Http::timeout(10)->get($datos['datos']);

            if (!$dataResponse->successful()) {
                return null;
            }

            return $this->parsearPrediccion($dataResponse->json());
        } catch (\Throwable $e) {
            Log::warning('AemetService error: ' . $e->getMessage());
            return null;
        }
    }

    private function getMunicipioCercano(float $latitud, float $longitud): ?string
    {
        try {
            $response = Http::withHeaders([
                'api_key' => $this->apiKey,
                'Accept'  => 'application/json',
            ])->timeout(10)->get(self::BASE_URL . '/maestro/municipios');

            if (!$response->successful()) {
                return null;
            }

            $urlDatos = $response->json()['datos'] ?? null;

            if (!$urlDatos) {
                return null;
            }

            $municipios = Http::timeout(10)->get($urlDatos)->json();

            if (!is_array($municipios)) {
                return null;
            }

            $minDistancia = PHP_FLOAT_MAX;
            $municipioId = null;

            foreach ($municipios as $municipio) {
                $latM = (float) ($municipio['latitud_dec'] ?? 0);
                $lonM = (float) ($municipio['longitud_dec'] ?? 0);

                $distancia = sqrt(pow($latitud - $latM, 2) + pow($longitud - $lonM, 2));

                if ($distancia < $minDistancia) {
                    $minDistancia = $distancia;
                    $municipioId = $municipio['id'] ?? null;
                }
            }

            // Eliminar el prefijo "id" si existe (AEMET usa formato "idXXXX")
            return $municipioId ? ltrim($municipioId, 'id') : null;
        } catch (\Throwable $e) {
            Log::warning('AemetService getMunicipio error: ' . $e->getMessage());
            return null;
        }
    }

    private function parsearPrediccion(array $datos): ?array
    {
        try {
            $prediccion = $datos[0]['prediccion'] ?? null;

            if (!$prediccion) {
                return null;
            }

            $hoy = $prediccion['dia'][0] ?? null;

            if (!$hoy) {
                return null;
            }

            // Temperatura máxima/mínima del día
            $tempMax = $hoy['temperatura']['maxima'] ?? null;
            $tempMin = $hoy['temperatura']['minima'] ?? null;

            // Viento (primer período disponible)
            $viento = collect($hoy['viento'] ?? [])->first();
            $velocidadViento = $viento['velocidad'] ?? null;
            $direccionViento = $viento['direccion'] ?? null;

            // Estado del cielo (primer período disponible)
            $estadoCielo = collect($hoy['estadoCielo'] ?? [])
                ->first(fn($e) => !empty($e['descripcion']));
            $descripcionCielo = $estadoCielo['descripcion'] ?? null;

            // Probabilidad de precipitación
            $precipitacion = collect($hoy['probPrecipitacion'] ?? [])
                ->max('value');

            return [
                'temp_max'          => $tempMax,
                'temp_min'          => $tempMin,
                'viento_velocidad'  => $velocidadViento,
                'viento_direccion'  => $direccionViento,
                'estado_cielo'      => $descripcionCielo,
                'prob_precipitacion' => $precipitacion,
                'municipio'         => $datos[0]['nombre'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('AemetService parsear error: ' . $e->getMessage());
            return null;
        }
    }
}
