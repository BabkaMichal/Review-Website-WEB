<?php
namespace App\Models\Entities;

/**
 * Doménová entita Žánr.
 */
class Zanr
{
    private ?int   $id;
    private string $nazev;  // nazev
    private string $popis;  // popis

    public function __construct(?int $id, string $nazev, string $popis = '')
    {
        $this->id    = $id;
        $this->nazev = $nazev;
        $this->popis = $popis;
    }

    public function getId(): ?int     { return $this->id; }
    public function getNazev(): string { return $this->nazev; }
    public function getPopis(): string { return $this->popis; }

    public static function zDatabaze(array $row): self
    {
        return new self(
            (int)$row['id_genre'],
            $row['name'],
            $row['description'] ?? ''
        );
    }
}
