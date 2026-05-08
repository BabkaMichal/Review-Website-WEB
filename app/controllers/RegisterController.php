<?php
namespace App\Controllers;

use App\Models\Repositories\UzivatelRepository;
use App\Models\Services\RegistraceService;

/**
 * Kontroler pro UC "Registrace nového uživatele".
 * Deleguje veškerou logiku na RegistraceService.
 */
class RegisterController extends BaseController
{
    private RegistraceService $registraceService;

    public function __construct()
    {
        parent::__construct();
        $this->registraceService = new RegistraceService(new UzivatelRepository());
    }

    public function show(string $pageTitle): string
    {
        $action         = $_POST['action'] ?? $_GET['action'] ?? null;
        $errorMessage   = null;
        $successMessage = null;

        if ($action === 'perform_registration' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $chyba = $this->registraceService->registruj(
                trim($_POST['reg_name']       ?? ''),
                trim($_POST['reg_email']      ?? ''),
                $_POST['reg_pass']            ?? '',
                $_POST['reg_pass_check']      ?? ''
            );

            if ($chyba !== null) {
                $errorMessage = $chyba;
            } else {
                $successMessage = 'Registrace proběhla úspěšně. Nyní se můžete přihlásit.';
            }
        }

        return $this->render('register.tpl.php', [
            'title'           => $pageTitle,
            'error_message'   => $errorMessage,
            'success_message' => $successMessage,
        ]);
    }
}
