<?php
namespace App\Controllers;

/**
 * Rozhraní pro všechny kontrolery.
 * index.php pracuje výhradně s tímto rozhraním, nezná konkrétní typy.
 */
interface IController
{
    /**
     * Zpracuje požadavek a vrátí HTML obsah stránky.
     *
     * @param  string $pageTitle  Název stránky pro šablonu.
     * @return string             Vyrendrovaný HTML obsah.
     */
    public function show(string $pageTitle): string;
}
