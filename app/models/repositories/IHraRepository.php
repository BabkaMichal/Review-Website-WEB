<?php
namespace App\Models\Repositories;

use App\Models\Entities\Hra;

/**
 * Rozhraní Repository pro entitu Hra.
 */
interface IHraRepository
{
    /** @return Hra[] Všechny hry s průměrným hodnocením, seřazené od nejnovějších */
    public function najdiVsechny(): array;

    public function najdiPodleId(int $id): ?Hra;

    public function uloz(Hra $hra): bool;

    public function aktualizuj(Hra $hra): bool;

    public function smazPodleId(int $id): bool;

    public function najdiPopisPodleId(int $id): ?string;
}
