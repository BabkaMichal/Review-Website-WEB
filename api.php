<?php
/**
 * REST API endpoint.
 * Volán JavaScriptovým fetch() z modálního okna pro načtení popisu hry.
 *
 * GET api.php?id=<id_game>
 * Odpověď: JSON { "description": "..." }
 */

require_once __DIR__ . '/config.php';

spl_autoload_register(function (string $trida): void {
    $mapa = [
        'App\\Models\\Repositories\\' => __DIR__ . '/app/models/repositories/',
        'App\\Models\\Entities\\'     => __DIR__ . '/app/models/entities/',
        'App\\Models\\Services\\'     => __DIR__ . '/app/models/services/',
    ];
    foreach ($mapa as $prefix => $adresar) {
        if (str_starts_with($trida, $prefix)) {
            $soubor = $adresar . str_replace('\\', '/', substr($trida, strlen($prefix))) . '.php';
            if (file_exists($soubor)) {
                require_once $soubor;
                return;
            }
        }
    }
});

header('Content-Type: application/json; charset=utf-8');

$idHry = (int)filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($idHry <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Neplatné ID hry.']);
    exit;
}

try {
    $hraRepo = new \App\Models\Repositories\HraRepository();
    $popis   = $hraRepo->najdiPopisPodleId($idHry);

    if ($popis === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Hra nenalezena.']);
        exit;
    }

    echo json_encode(['description' => strip_tags($popis)]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Chyba serveru.']);
}
