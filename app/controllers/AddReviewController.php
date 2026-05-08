<?php
namespace App\Controllers;

use App\Models\Repositories\HraRepository;
use App\Models\Services\HraService;

/**
 * Kontroler pro UC "Přidání nové hry k hodnocení".
 * Přístupný pouze přihlášeným uživatelům.
 * Deleguje business logiku (validace, nahrání obrázku, uložení) na HraService.
 */
class AddReviewController extends BaseController
{
    private HraService $hraService;

    public function __construct()
    {
        parent::__construct();

        // Přesměrování nepřihlášených
        $this->vyzadujPrihlaseni();

        $this->hraService = new HraService(new HraRepository());
    }

    public function show(string $pageTitle): string
    {
        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && ($_POST['action'] ?? '') === 'add_game'
        ) {
            $uzivatel = $this->prihlasenyUzivatel();
            $chyba    = $this->hraService->pridejHru(
                $_POST,
                $_FILES['image'] ?? [],
                $uzivatel->getId()
            );

            if ($chyba !== null) {
                $errorMessage = $chyba;
            } else {
                $this->presmeruj('index.php');
            }
        }

        return $this->render('add_review.tpl.php', [
            'title'         => $pageTitle,
            'error_message' => $errorMessage,
        ]);
    }
}
