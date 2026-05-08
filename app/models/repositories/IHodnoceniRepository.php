<?php
namespace App\Models\Repositories;

use App\Models\Entities\Hodnoceni;

/**
 * Rozhraní Repository pro entitu Hodnocení.
 */
interface IHodnoceniRepository
{
    /** Vloží nové hodnocení, vrátí ID nového záznamu nebo false */
    public function vloz(Hodnoceni $hodnoceni): int|false;

    public function najdiPodleUzivateleAHry(int $idUzivatele, int $idHry): ?Hodnoceni;

    public function aktualizujPodleId(int $id, int $pocetHvezd): bool;

    public function aktualizujPodleUzivateleAHry(int $idUzivatele, int $idHry, int $pocetHvezd): bool;
}
