<?php

namespace App\Services;

use Illuminate\Http\Request;

class GeoJsonViewer
{
    public static function supported(?string $path): bool
    {
        return in_array(strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION)), ['geojson', 'json'], true);
    }

    public function respond(Request $request, $source, string $backUrl, string $downloadUrl)
    {
        abort_unless(self::supported($source->file_path), 415, 'This file is not GeoJSON');
        $path = PrivateReportFiles::existing($source->file_path);
        if ($request->query('format') === 'geojson') {
            abort_if(filesize($path) > 20 * 1024 * 1024, 413, 'GeoJSON is too large to preview');
            try {
                $input = json_decode(file_get_contents($path), true, 128, JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                abort(422, 'Invalid GeoJSON file');
            }
            $remaining = 200000;
            $data = $this->normalize($input, $remaining);
            return response()->json($data)->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }
        return response()->view('geojson.viewer', [
            'fileName' => $source->file_name, 'backUrl' => $backUrl,
            'downloadUrl' => $downloadUrl, 'dataUrl' => $request->url().'?format=geojson',
        ])->header('Cache-Control', 'no-store, private')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    private function normalize($input, int &$remaining, int $depth = 0): array
    {
        abort_unless(is_array($input) && $depth < 32, 422, 'Invalid GeoJSON');
        $type = $input['type'] ?? '';
        if ($type === 'FeatureCollection') {
            abort_unless(isset($input['features']) && is_array($input['features']) && count($input['features']) <= 10000, 422, 'Invalid or oversized FeatureCollection');
            $features = [];
            foreach ($input['features'] as $feature) {
                abort_unless(is_array($feature) && ($feature['type'] ?? '') === 'Feature', 422, 'Invalid feature');
                $features[] = $this->normalize($feature, $remaining, $depth + 1);
            }
            return ['type' => $type, 'features' => $features];
        }
        if ($type === 'Feature') {
            abort_if(isset($input['geometry']) && (!is_array($input['geometry']) ||
                in_array($input['geometry']['type'] ?? '', ['Feature', 'FeatureCollection'], true)), 422, 'Invalid feature geometry');
            $properties = [];
            // Explicit display fields. Do not publish farmer names, addresses or land-title numbers.
            foreach (['plot_id', 'Plot ID', 'plot_code', 'fid', 'Producer Country', 'Material Type', 'Area (ha)', 'area_ha', 'area', 'latitude', 'longitude'] as $key) {
                $value = $input['properties'][$key] ?? null;
                if (is_scalar($value)) {
                    $properties[$key] = mb_substr((string) $value, 0, 500);
                }
            }
            return ['type' => 'Feature', 'properties' => (object) $properties,
                'geometry' => isset($input['geometry']) ? $this->normalize($input['geometry'], $remaining, $depth + 1) : null];
        }
        if ($type === 'GeometryCollection') {
            abort_unless(isset($input['geometries']) && is_array($input['geometries']), 422, 'Invalid geometry collection');
            $geometries = [];
            foreach ($input['geometries'] as $geometry) {
                abort_unless(!in_array($geometry['type'] ?? '', ['Feature', 'FeatureCollection'], true), 422);
                $geometries[] = $this->normalize($geometry, $remaining, $depth + 1);
            }
            return ['type' => $type, 'geometries' => $geometries];
        }
        $levels = ['Point' => 0, 'MultiPoint' => 1, 'LineString' => 1, 'MultiLineString' => 2, 'Polygon' => 2, 'MultiPolygon' => 3];
        abort_unless(array_key_exists($type, $levels), 422, 'Unsupported GeoJSON geometry');
        return ['type' => $type, 'coordinates' => $this->coordinates($input['coordinates'] ?? null, $levels[$type], $remaining)];
    }

    private function coordinates($coordinates, int $level, int &$remaining): array
    {
        abort_unless(is_array($coordinates) && array_is_list($coordinates), 422, 'Invalid coordinates');
        if ($level === 0) {
            abort_unless(--$remaining >= 0 && count($coordinates) >= 2 &&
                is_numeric($coordinates[0]) && is_numeric($coordinates[1]) &&
                is_finite((float) $coordinates[0]) && is_finite((float) $coordinates[1]) &&
                abs((float) $coordinates[0]) <= 180 && abs((float) $coordinates[1]) <= 90, 422,
                'Coordinates must use longitude/latitude (WGS84)');
            return [(float) $coordinates[0], (float) $coordinates[1]];
        }
        $result = [];
        foreach ($coordinates as $part) {
            $result[] = $this->coordinates($part, $level - 1, $remaining);
        }
        return $result;
    }
}
