<?php
declare(strict_types=1);

/**
 * Front Controller — jediný vstupní bod aplikace.
 *
 * Odpovědnosti:
 *   1. Načtení konfigurace a autoloaderu
 *   2. Překlad URL parametru ?page= na třídu kontroleru
 *   3. Instanciace kontroleru (přes rozhraní IController)
 *   4. Zavolání show() a výpis výsledku
 *
 * Návrhový vzor: Front Controller + Registry (WEB_PAGES konstanta)
 */

// ── Konfigurace ─────────────────────────────────────────────────────────────
require_once __DIR__ . '/config.php';

// ── Autoloader (PSR-4 style) ─────────────────────────────────────────────────
spl_autoload_register(function (string $trida): void {
    // Mapování namespace prefix na adresář
    $mapa = [
        'App\\Controllers\\'          => __DIR__ . '/app/controllers/',
        'App\\Models\\Entities\\'     => __DIR__ . '/app/models/entities/',
        'App\\Models\\Repositories\\' => __DIR__ . '/app/models/repositories/',
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

// ── Session ──────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Routing ──────────────────────────────────────────────────────────────────
$pageKey = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS)
    ?? DEFAULT_WEB_PAGE_KEY;

// Ochrana: povolíme jen klíče definované v konfiguraci
if (!array_key_exists($pageKey, WEB_PAGES)) {
    // Speciální pseudo-stránky zpracovávané IntroductionControllerem
    if (in_array($pageKey, ['post_review', 'delete_game'], true)) {
        $pageKey = DEFAULT_WEB_PAGE_KEY;
    } else {
        http_response_code(404);
        echo '<h1>404 — Stránka nenalezena</h1>';
        exit;
    }
}

$pageInfo      = WEB_PAGES[$pageKey];
$controllerClass = $pageInfo['controller'];
$pageTitle     = $pageInfo['title'];

// ── Instanciace a spuštění kontroleru ────────────────────────────────────────
/** @var \App\Controllers\IController $controller */
$controller = new $controllerClass();
echo $controller->show($pageTitle);
