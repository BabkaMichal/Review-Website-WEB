<?php
namespace App\Models\Entities;

/**
 * Doménová entita Hodnocení.
 */
class Hodnoceni
{
    private ?int    $id;
    private ?int    $idUzivatele;  // Uživatel "Napsal" (null = anonymní host)
    private int     $idHry;        // Hodnocení "Obdržela" Hra
    private int     $pocetHvezd;   // počet_hvězd (1-5)
    private string  $text;         // text (volitelný komentář)
    private ?\DateTime $datum;

    public function __construct(
        ?int       $id,
        ?int       $idUzivatele,
        int        $idHry,
        int        $pocetHvezd,
        string     $text = '',
        ?\DateTime $datum = null
    ) {
        if ($pocetHvezd < 1 || $pocetHvezd > 5) {
            throw new \InvalidArgumentException('Počet hvězd musí být 1-5.');
        }
        $this->id           = $id;
        $this->idUzivatele  = $idUzivatele;
        $this->idHry        = $idHry;
        $this->pocetHvezd   = $pocetHvezd;
        $this->text         = $text;
        $this->datum        = $datum ?? new \DateTime();
    }

    public function getId(): ?int            { return $this->id; }
    public function getIdUzivatele(): ?int   { return $this->idUzivatele; }
    public function getIdHry(): int          { return $this->idHry; }
    public function getPocetHvezd(): int     { return $this->pocetHvezd; }
    public function getText(): string        { return $this->text; }
    public function getDatum(): ?\DateTime   { return $this->datum; }

    public function jeAnonymni(): bool
    {
        return $this->idUzivatele === null;
    }

    /** Tovární metoda — vytvoří Hodnocení z DB řádku */
    public static function zDatabaze(array $row): self
    {
        return new self(
            (int)$row['id_rating'],
            isset($row['id_user']) ? (int)$row['id_user'] : null,
            (int)$row['id_game'],
            (int)$row['rating_value'],
            $row['text'] ?? '',
            isset($row['created_at']) ? new \DateTime($row['created_at']) : null
        );
    }
}
