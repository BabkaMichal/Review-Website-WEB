<?php
namespace App\Models\Services;

use App\Models\Entities\Hodnoceni;
use App\Models\Repositories\IHodnoceniRepository;

/**
 * Service pro případ užití "Přidat hodnocení"
 *
 * Rozlišuje dva scénáře:
 *   1. Přihlášený uživatel — hodnocení svázáno s id_user
 *   2. Anonymní host       — hodnocení sledováno přes session (voted_games)
 */
class HodnoceniService
{
    private IHodnoceniRepository $hodnoceniRepo;

    public function __construct(IHodnoceniRepository $hodnoceniRepo)
    {
        $this->hodnoceniRepo = $hodnoceniRepo;
    }

    /**
     * Zpracuje hodnocení od přihlášeného uživatele.
     * Pokud již hodnotil, aktualizuje stávající hodnocení.
     *
     * @return string  Zpráva o výsledku (úspěch nebo chyba)
     */
    public function hodnotJakoPrihlaseny(int $idUzivatele, int $idHry, int $pocetHvezd): string
    {
        $existujici = $this->hodnoceniRepo->najdiPodleUzivateleAHry($idUzivatele, $idHry);

        if ($existujici !== null) {
            $ok = $this->hodnoceniRepo->aktualizujPodleUzivateleAHry(
                $idUzivatele, $idHry, $pocetHvezd
            );
            return $ok
                ? 'Vaše hodnocení bylo aktualizováno.'
                : 'Nepodařilo se aktualizovat hodnocení.';
        }

        // Nové hodnocení — entita Hodnocení (Uživatel "Napsal" Hodnocení)
        $hodnoceni = new Hodnoceni(null, $idUzivatele, $idHry, $pocetHvezd);
        $noveId    = $this->hodnoceniRepo->vloz($hodnoceni);

        return $noveId !== false
            ? 'Děkujeme za vaše hodnocení!'
            : 'Hodnocení se nepodařilo uložit.';
    }

    /**
     * Zpracuje hodnocení od anonymního hosta.
     * Stav (id záznamu) se udržuje v session pod klíčem 'voted_games'.
     *
     * @return string  Zpráva o výsledku
     */
    public function hodnotJakoHost(int $idHry, int $pocetHvezd, array &$session): string
    {
        if (!isset($session['voted_games'])) {
            $session['voted_games'] = [];
        }

        // Host již hodnotil tuto hru — aktualizujeme podle uloženého ID záznamu
        if (isset($session['voted_games'][$idHry])) {
            $ratingId = (int)$session['voted_games'][$idHry];
            $ok = $this->hodnoceniRepo->aktualizujPodleId($ratingId, $pocetHvezd);
            return $ok
                ? 'Vaše hodnocení bylo aktualizováno (anonymně).'
                : 'Nepodařilo se aktualizovat hodnocení.';
        }

        // Nové anonymní hodnocení (id_user = null)
        $hodnoceni = new Hodnoceni(null, null, $idHry, $pocetHvezd);
        $noveId    = $this->hodnoceniRepo->vloz($hodnoceni);

        if ($noveId !== false) {
            // Zapamatujeme si ID záznamu pro případnou aktualizaci
            $session['voted_games'][$idHry] = $noveId;
            return 'Děkujeme za vaše hodnocení!';
        }

        return 'Hodnocení se nepodařilo uložit.';
    }
}
