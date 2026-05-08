<?php
namespace App\Models\Services;

use App\Models\Entities\Uzivatel;
use App\Models\Repositories\IUzivatelRepository;

/**
 * Service pro případ užití "Přihlášení uživatele" a "Odhlášení".
 */
class PrihlaseniService
{
    private IUzivatelRepository $uzivatelRepo;

    public function __construct(IUzivatelRepository $uzivatelRepo)
    {
        $this->uzivatelRepo = $uzivatelRepo;
    }

    /**
     * Ověří přihlašovací údaje.
     *
     * @return Uzivatel|null  Entita uživatele při úspěchu, null při chybě.
     */
    public function prihlasit(string $prezdivka, string $heslo): ?Uzivatel
    {
        if (empty($prezdivka) || empty($heslo)) {
            return null;
        }

        $uzivatel = $this->uzivatelRepo->najdiPodlePrezdivky($prezdivka);

        if ($uzivatel === null) {
            return null;
        }

        // Delegujeme ověření na doménovou entitu
        if (!$uzivatel->overHeslo($heslo)) {
            return null;
        }

        return $uzivatel;
    }

    /**
     * Uloží přihlášeného uživatele do session.
     */
    public function ulozDoSession(Uzivatel $uzivatel): void
    {
        $_SESSION['user'] = [
            'id'    => $uzivatel->getId(),
            'login' => $uzivatel->getPrezdivka(),
            'role'  => (string)$uzivatel->getRole(),
        ];
    }

    /**
     * Zničí session
     */
    public function odhlasit(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
    }
}
