<?php
namespace App\Controllers;

use App\Models\Entities\Role;
use App\Models\Entities\Uzivatel;

/**
 * Abstraktní základní kontroler.
 * Poskytuje sdílené pomocné metody:
 *   - přístup k přihlášenému uživateli ze session
 *   - kontrolu oprávnění (vyzadujPrihlaseni, vyzadujRoli)
 *   - přesměrování
 *   - renderování šablon přes output buffer
 */
abstract class BaseController implements IController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Vrátí přihlášeného uživatele jako entitu Uzivatel, nebo null.
     */
    protected function prihlasenyUzivatel(): ?Uzivatel
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }
        $data = $_SESSION['user'];
        return new Uzivatel(
            (int)$data['id'],
            $data['login'],
            '', '',
            new Role($data['role'] ?? Role::USER)
        );
    }

    /**
     * Vyžaduje přihlášení, jinak přesměruje na login stránku.
     */
    protected function vyzadujPrihlaseni(): Uzivatel
    {
        $uzivatel = $this->prihlasenyUzivatel();
        if ($uzivatel === null) {
            $this->presmeruj('index.php?page=login');
        }
        return $uzivatel;
    }

    /**
     * Vyžaduje minimální roli, jinak vrátí HTTP 403.
     */
    protected function vyzadujRoli(Uzivatel $uzivatel, string $minimumRole): void
    {
        if (!$uzivatel->maOpravu($minimumRole)) {
            http_response_code(403);
            echo '<h1>403 — Přístup odepřen</h1><p>Nemáte dostatečná oprávnění.</p>';
            exit;
        }
    }

    /**
     * Přesměruje na danou URL a ukončí skript.
     */
    protected function presmeruj(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Vyrendruje PHP šablonu přes output buffer.
     * Data jsou předána přes globální $tplData
     */
    protected function render(string $sablona, array $tplData = []): string
    {
        $GLOBALS['tplData'] = $tplData;
        ob_start();
        require DIRECTORY_VIEWS . '/' . $sablona;
        return ob_get_clean();
    }
}
