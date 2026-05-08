<?php
namespace App\Controllers;

use App\Models\Entities\Role;
use App\Models\Repositories\HodnoceniRepository;
use App\Models\Repositories\HraRepository;
use App\Models\Services\HodnoceniService;
use App\Models\Services\HraService;

/**
 * Kontroler pro úvodní stránku a hodnocení her.
 *
 * Zpracovává UC:
 *   - Zobrazení seznamu her (všichni)
 *   - "Přidat hodnocení" (přihlášení i hosté)
 *   - Smazání hry (admin)
 */
class IntroductionController extends BaseController
{
    private HraService       $hraService;
    private HodnoceniService $hodnoceniService;

    public function __construct()
    {
        parent::__construct();
        $this->hraService       = new HraService(new HraRepository());
        $this->hodnoceniService = new HodnoceniService(new HodnoceniRepository());
    }

    public function show(string $pageTitle): string
    {
        $pageKey = $_GET['page'] ?? DEFAULT_WEB_PAGE_KEY;

        // POST hodnocení
        if ($pageKey === 'post_review' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->zpracujHodnoceni();
            return '';
        }

        // Smazání hry — pouze admin
        if ($pageKey === 'delete_game') {
            $this->zpracujSmazani();
        }

        // Zobrazení hlavní stránky
        $hry = $this->hraService->najdiVsechny();

        return $this->render('IntroductionTemplate.tpl.php', [
            'title'    => $pageTitle,
            'games'    => array_map([$this, 'hraDoArray'], $hry),
        ]);
    }

    private function zpracujHodnoceni(): void
    {
        $idHry      = (int)($_POST['id_game'] ?? 0);
        $pocetHvezd = (int)($_POST['rating']  ?? 0);

        if ($idHry <= 0 || $pocetHvezd < 1 || $pocetHvezd > 5) {
            $_SESSION['error_message'] = 'Musíte vybrat hru a platné hodnocení.';
            $this->presmeruj('index.php');
        }

        $uzivatel = $this->prihlasenyUzivatel();

        if ($uzivatel !== null) {
            //přihlášený uživatel
            $zprava = $this->hodnoceniService->hodnotJakoPrihlaseny(
                $uzivatel->getId(), $idHry, $pocetHvezd
            );
        } else {
            //anonymní host
            $zprava = $this->hodnoceniService->hodnotJakoHost(
                $idHry, $pocetHvezd, $_SESSION
            );
        }

        // Rozlišíme úspěch od chyby podle obsahu zprávy
        if (str_contains($zprava, 'nepodařil') || str_contains($zprava, 'Nepodařil')) {
            $_SESSION['error_message'] = $zprava;
        } else {
            $_SESSION['success_message'] = $zprava;
        }

        $this->presmeruj('index.php');
    }

    private function zpracujSmazani(): void
    {
        $uzivatel = $this->vyzadujPrihlaseni();
        $this->vyzadujRoli($uzivatel, Role::ADMIN);

        $idHry = (int)($_GET['id'] ?? 0);
        if ($idHry > 0) {
            $chyba = $this->hraService->smazHru($idHry, $uzivatel);
            if ($chyba !== null) {
                $_SESSION['error_message'] = $chyba;
            } else {
                $_SESSION['success_message'] = 'Příspěvek byl úspěšně smazán.';
            }
        }
        $this->presmeruj('index.php');
    }

    /**
     * Převede entitu Hra na pole kompatibilní se stávající šablonou.
     */
    private function hraDoArray(\App\Models\Entities\Hra $hra): array
    {
        return [
            'id_game'          => $hra->getId(),
            'title'            => $hra->getNazev(),
            'developer'        => $hra->getVyvojar(),
            'description'      => $hra->getPopis(),
            'image_url'        => $hra->getObrazek(),
            'average_rating'   => $hra->getPrumerneHodnoceni(),
            'total_ratings'    => $hra->getPocetHodnoceni(),
            'id_user'          => $hra->getIdUzivatele(),
        ];
    }
}
