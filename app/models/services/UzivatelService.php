<?php
namespace App\Models\Services;

use App\Models\Entities\Role;
use App\Models\Entities\Uzivatel;
use App\Models\Repositories\IUzivatelRepository;

/**
 * Service pro případy užití "Smazání uživatele" a "Povýšení kolegy na admina"
 */
class UzivatelService
{
    private IUzivatelRepository $uzivatelRepo;

    public function __construct(IUzivatelRepository $uzivatelRepo)
    {
        $this->uzivatelRepo = $uzivatelRepo;
    }

    /** @return Uzivatel[] */
    public function vsichniUzivatele(): array
    {
        return $this->uzivatelRepo->najdiVsechny();
    }

    /**
     * UC: "Smazání uživatele" pouze superadmin.
     * Superadmin nemůže smazat sám sebe.
     */
    public function smazUzivatele(int $idMazaneho, Uzivatel $aktualniUzivatel): ?string
    {
        if (!$aktualniUzivatel->jeSuperAdmin()) {
            return 'Pouze superadmin může mazat uživatele.';
        }

        if ($idMazaneho === $aktualniUzivatel->getId()) {
            return 'Nemůžete smazat sám sebe.';
        }

        $ok = $this->uzivatelRepo->smazPodleId($idMazaneho);
        return $ok ? null : "Uživatele s ID $idMazaneho se nepodařilo smazat.";
    }

    /**
     * UC: "Povýšení kolegy na admina" pouze superadmin.
     */
    public function povysNaAdmina(int $idUzivatele, Uzivatel $aktualniUzivatel): ?string
    {
        if (!$aktualniUzivatel->jeSuperAdmin()) {
            return 'Pouze superadmin může měnit role.';
        }

        if ($idUzivatele === $aktualniUzivatel->getId()) {
            return 'Nemůžete změnit vlastní roli.';
        }

        $ok = $this->uzivatelRepo->zmenRoli($idUzivatele, Role::ADMIN);
        return $ok ? null : "Uživateli s ID $idUzivatele se nepodařilo přidělit roli admina.";
    }
}
