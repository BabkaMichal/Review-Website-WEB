<?php
namespace App\Controllers;

use App\Models\Repositories\HraRepository;
use App\Models\Services\HraService;

/**
 * Kontroler pro UC "Úprava stávajícího příspěvku".
 * Přístupný autorovi hry nebo adminovi.
 * Deleguje oprávnění a logiku na HraService (museEditovat je na entitě Hra).
 */
class UpdateController extends BaseController
{
    private HraService $hraService;

    public function __construct()
    {
        parent::__construct();
        $this->vyzadujPrihlaseni();
        $this->hraService = new HraService(new HraRepository());
    }

    public function show(string $pageTitle): string
    {
        $idHry        = (int)($_POST['id_game'] ?? $_GET['id'] ?? 0);
        $errorMessage = null;
        $successMessage = null;

        if ($idHry <= 0) {
            return $this->render('update_review.tpl.php', [
                'title'         => $pageTitle,
                'error_message' => 'Nebylo zadáno ID hry k úpravě.',
                'game'          => null,
            ]);
        }

        $uzivatel = $this->prihlasenyUzivatel();

        // POST — zpracování formuláře
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && ($_POST['action'] ?? '') === 'update_game'
        ) {
            $chyba = $this->hraService->aktualizujHru(
                $idHry,
                $_POST,
                $_FILES['image'] ?? [],
                $uzivatel
            );

            if ($chyba !== null) {
                $errorMessage = $chyba;
            } else {
                $successMessage = 'Hra byla úspěšně aktualizována.';
            }
        }

        // Načteme aktuální stav hry (po případném update)
        $hra = $this->hraService->najdiPodleId($idHry);

        if ($hra === null) {
            return $this->render('update_review.tpl.php', [
                'title'         => $pageTitle,
                'error_message' => 'Hra s tímto ID neexistuje.',
                'game'          => null,
            ]);
        }

        // Kontrola oprávnění pomocí metody entity
        if (!$hra->muzeEditovat($uzivatel)) {
            http_response_code(403);
            return $this->render('update_review.tpl.php', [
                'title'         => $pageTitle,
                'error_message' => 'Nemáte oprávnění upravovat tento příspěvek.',
                'game'          => null,
            ]);
        }

        return $this->render('update_review.tpl.php', [
            'title'           => $pageTitle,
            'error_message'   => $errorMessage,
            'success_message' => $successMessage,
            'game'            => [
                'id_game'    => $hra->getId(),
                'title'      => $hra->getNazev(),
                'developer'  => $hra->getVyvojar(),
                'description'=> $hra->getPopis(),
                'image_url'  => $hra->getObrazek(),
            ],
        ]);
    }
}
