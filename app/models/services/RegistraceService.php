<?php
namespace App\Models\Services;

use App\Models\Entities\Role;
use App\Models\Entities\Uzivatel;
use App\Models\Repositories\IUzivatelRepository;

/**
 * Service pro případ užití "Registrace nového uživatele"

 */
class RegistraceService
{
    private IUzivatelRepository $uzivatelRepo;

    public function __construct(IUzivatelRepository $uzivatelRepo)
    {
        $this->uzivatelRepo = $uzivatelRepo;
    }

    /**
     * Provede registraci nového uživatele.
     *
     * @return string|null  Chybová zpráva, nebo null při úspěchu.
     */
    public function registruj(
        string $prezdivka,
        string $email,
        string $heslo,
        string $hesloZnovu
    ): ?string {
        // Validace prázdná pole
        if (empty($prezdivka) || empty($email) || empty($heslo) || empty($hesloZnovu)) {
            return 'Musíte vyplnit všechna pole.';
        }

        // Validace formát e-mailu
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Neplatný formát e-mailu.';
        }

        // Validace shoda hesel
        if ($heslo !== $hesloZnovu) {
            return 'Hesla se neshodují.';
        }

        // Kontrola duplicit přezdívka
        if ($this->uzivatelRepo->najdiPodlePrezdivky($prezdivka) !== null) {
            return 'Uživatel s tímto jménem již existuje.';
        }

        // Kontrola duplicit e-mail
        if ($this->uzivatelRepo->najdiPodleEmailu($email) !== null) {
            return 'Tento e-mail je již používán.';
        }

        // Nový uživatel dostane roli USER, heslo se zahashuje
        $novyUzivatel = new Uzivatel(
            null,
            $prezdivka,
            $email,
            password_hash($heslo, PASSWORD_DEFAULT),
            new Role(Role::USER)
        );

        if (!$this->uzivatelRepo->uloz($novyUzivatel)) {
            return 'Došlo k chybě při registraci. Zkuste to prosím znovu.';
        }

        return null; // úspěch
    }
}
