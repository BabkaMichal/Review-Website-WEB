<?php
namespace App\Controllers;

use App\Models\Entities\Role;
use App\Models\Repositories\UzivatelRepository;
use App\Models\Services\UzivatelService;

/**
 * Kontroler pro správu uživatelů — pouze Superadmin.
 *
 * Zpracovává UC:
 *   - "Smazání uživatele"
 *   - "Povýšení kolegy na admina"
 */
class UserManagementController extends BaseController
{
    private UzivatelService $uzivatelService;

    public function __construct()
    {
        parent::__construct();
        $uzivatel = $this->vyzadujPrihlaseni();
        $this->vyzadujRoli($uzivatel, Role::SUPERADMIN);
        $this->uzivatelService = new UzivatelService(new UzivatelRepository());
    }

    public function show(string $pageTitle): string
    {
        $uzivatel    = $this->prihlasenyUzivatel();
        $message     = null;
        $deleteMsg   = null;

        $action   = $_POST['action']  ?? null;
        $idTarget = (int)($_POST['id_user'] ?? 0);

        // Smazání uživatele
        if ($action === 'delete' && $idTarget > 0) {
            $chyba = $this->uzivatelService->smazUzivatele($idTarget, $uzivatel);
            $deleteMsg = $chyba !== null
                ? 'CHYBA: ' . $chyba
                : "OK: Uživatel s ID:$idTarget byl smazán z databáze.";
        }

        //Povýšení kolegy na admina
        if ($action === 'changeRole' && $idTarget > 0) {
            $chyba = $this->uzivatelService->povysNaAdmina($idTarget, $uzivatel);
            $message = $chyba !== null
                ? 'CHYBA: ' . $chyba
                : "OK: Uživateli s ID:$idTarget byla udělena admin role!";
        }

        $vsichniUzivatele = array_map(
            fn($u) => [
                'id_user' => $u->getId(),
                'login'   => $u->getPrezdivka(),
                'email'   => $u->getEmail(),
                'role'    => (string)$u->getRole(),
            ],
            $this->uzivatelService->vsichniUzivatele()
        );

        return $this->render('UserManagementTemplate.tpl.php', [
            'title'   => $pageTitle,
            'users'   => $vsichniUzivatele,
            'delete'  => $deleteMsg,
            'message' => $message,
        ]);
    }
}
