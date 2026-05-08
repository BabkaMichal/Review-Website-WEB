<?php
namespace App\Models\Repositories;

use App\Models\Entities\Uzivatel;

/**
 * Rozhraní Repository pro entitu Uživatel.
 */
interface IUzivatelRepository
{
    /** @return Uzivatel[] */
    public function najdiVsechny(): array;

    public function najdiPodleId(int $id): ?Uzivatel;

    public function najdiPodlePrezdivky(string $prezdivka): ?Uzivatel;

    public function najdiPodleEmailu(string $email): ?Uzivatel;

    public function uloz(Uzivatel $uzivatel): bool;

    public function smazPodleId(int $id): bool;

    public function zmenRoli(int $id, string $novaRole): bool;
}
