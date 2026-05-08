<?php
namespace App\Controllers;

use App\Models\Repositories\UzivatelRepository;
use App\Models\Services\PrihlaseniService;

/**
 * Kontroler pro UC "Přihlášení uživatele" a "Odhlášení".
 * Deleguje veškerou logiku na PrihlaseniService.
 */
class LoginController extends BaseController
{
    private PrihlaseniService $prihlaseniService;

    public function __construct()
    {
        parent::__construct();
        $this->prihlaseniService = new PrihlaseniService(new UzivatelRepository());
    }

    public function show(string $pageTitle): string
    {
        $action      = $_POST['action'] ?? $_GET['action'] ?? null;
        $errorMessage = null;

        if ($action === 'perform_login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorMessage = $this->zpracujPrihlaseni();
        } elseif ($action === 'logout') {
            $this->zpracujOdhlaseni();
        }

        return $this->render('login.tpl.php', [
            'title'         => $pageTitle,
            'error_message' => $errorMessage,
        ]);
    }

    private function zpracujPrihlaseni(): ?string
    {
        $prezdivka = trim($_POST['login_name'] ?? '');
        $heslo     = $_POST['login_pass']      ?? '';

        if (empty($prezdivka) || empty($heslo)) {
            return 'Musíte vyplnit přihlašovací jméno i heslo.';
        }

        $uzivatel = $this->prihlaseniService->prihlasit($prezdivka, $heslo);

        if ($uzivatel === null) {
            return 'Nesprávné přihlašovací jméno nebo heslo.';
        }

        // Uloží přihlášeného uživatele do session
        $this->prihlaseniService->ulozDoSession($uzivatel);
        $this->presmeruj('index.php');
    }

    private function zpracujOdhlaseni(): void
    {
        $this->prihlaseniService->odhlasit();
        $this->presmeruj('index.php');
    }
}
