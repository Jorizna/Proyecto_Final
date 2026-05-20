<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OverpassService
{
    private const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';
    private const RADIO_METROS = 500;
    private const CACHE_TTL = 86400; // 24 horas

    public function getMasasDeAgua(float $latitud, float $longitud): array
    {
        $cacheKey = "overpass_{$latitud}_{$longitud}_" . self::RADIO_METROS;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($latitud, $longitud) {
            return $this->fetchMasasDeAgua($latitud, $longitud);
        });
    }

    private function fetchMasasDeAgua(float $latitud, float $longitud): array
    {
        $query = $this->buildQuery($latitud, $longitud);

        try {
            $response = Http::timeout(15)
                ->post(self::OVERPASS_URL, ['data' => $query]);

            if (!$response->successful()) {
                return [];
            }

            $datos = $response->json();
            $elementos = $datos['elements'] ?? [];

            return $this->parsearMasas($elementos);
        } catch (\Throwable $e) {
            Log::warning('OverpassService error: ' . $e->getMessage());
            return [];
        }
    }

    private function buildQuery(float $latitud, float $longitud): string
    {
        $radio = self::RADIO_METROS;

        return <<<OVERPASS
        [out:json][timeout:10];
        (
          way["waterway"~"river|stream|canal"](around:{$radio},{$latitud},{$longitud});
          relation["waterway"~"river|stream"](around:{$radio},{$latitud},{$longitud});
          way["natural"~"water|lake|reservoir"](around:{$radio},{$latitud},{$longitud});
          node["natural"~"water|lake"](around:{$radio},{$latitud},{$longitud});
          relation["natural"~"water"](around:{$radio},{$latitud},{$longitud});
          way["landuse"="reservoir"](around:{$radio},{$latitud},{$longitud});
        );
        out tags;
        OVERPASS;
    }

    private function parsearMasas(array $elementos): array
    {
        $tipos = [];

        foreach ($elementos as $elemento) {
            $tags = $elemento['tags'] ?? [];

            $tipo = $this->determinarTipo($tags);

            if ($tipo && !in_array($tipo, $tipos)) {
                $tipos[] = $tipo;
            }
        }

        return $tipos;
    }

    private function determinarTipo(array $tags): ?string
    {
        $waterway = $tags['waterway'] ?? '';
        $natural = $tags['natural'] ?? '';
        $landuse = $tags['landuse'] ?? '';
        $water = $tags['water'] ?? '';

        if (in_array($waterway, ['river', 'stream'])) {
            return 'Río cercano';
        }

        if ($waterway === 'canal') {
            return 'Canal cercano';
        }

        if ($landuse === 'reservoir' || $water === 'reservoir') {
            return 'Embalse cercano';
        }

        if (in_array($natural, ['water', 'lake']) || $water === 'lake') {
            return 'Lago cercano';
        }

        if ($natural === 'water') {
            return 'Masa de agua cercana';
        }

        return null;
    }
}
