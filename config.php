<?php
/**
 * Centrální konfigurace aplikace GamePortál.
 *
 * Obsahuje:
 *   - Připojení k databázi
 *   - Definice konstant pro názvy tabulek
 *   - Registr stránek (WEB_PAGES) — mapuje URL klíče na třídy kontrolerů
 */

// ── Databáze ─────────────────────────────────────────────────────────────────
define('DB_SERVER', 'localhost');
define('DB_NAME',   'kivweb_2025');
define('DB_USER',   'root');
define('DB_PASS',   '');

// ── Tabulky ──────────────────────────────────────────────────────────────────
define('TABLE_INTRODUCTION', 'games');
define('TABLE_USER',         'users');
define('TABLE_RATING',       'game_ratings');

// ── Adresáře ─────────────────────────────────────────────────────────────────
define('DIRECTORY_VIEWS', __DIR__ . '/app/views');

// ── HTML Purifier (composer) ──────────────────────────────────────────────────
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// ── Registr stránek (WEB_PAGES) ───────────────────────────────────────────────
// Klíč       => [ 'title' => '...', 'controller' => 'Třída' ]
// Tento registr je jediné místo kde se definuje routing — Front Controller pattern.
define('WEB_PAGES', [
    'uvod'   => [
        'title'      => 'Vítejte na GamePortálu',
        'controller' => \App\Controllers\IntroductionController::class,
    ],
    'login'  => [
        'title'      => 'Přihlášení',
        'controller' => \App\Controllers\LoginController::class,
    ],
    'register' => [
        'title'      => 'Registrace',
        'controller' => \App\Controllers\RegisterController::class,
    ],
    'add_review' => [
        'title'      => 'Přidat novou hru',
        'controller' => \App\Controllers\AddReviewController::class,
    ],
    'update' => [
        'title'      => 'Upravit hru',
        'controller' => \App\Controllers\UpdateController::class,
    ],
    'sprava' => [
        'title'      => 'Správa uživatelů',
        'controller' => \App\Controllers\UserManagementController::class,
    ],
]);

define('DEFAULT_WEB_PAGE_KEY', 'uvod');
