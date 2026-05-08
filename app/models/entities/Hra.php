<?php
namespace App\Models\Entities;

/**
 * Doménová entita Hra.
 */
class Hra
{
    private ?int    $id;
    private string  $nazev;       // název
    private string  $vyvojar;     // vývojář
    private string  $popis;       // popis
    private string  $obrazek;     // obrázek (URL)
    private int     $idUzivatele; // FK — Uživatel "Přidal"
    private ?\DateTime $datum;

    // Vypočítané hodnoty (z JOIN s hodnoceními)
    private float $prumerneHodnoceni;
    private int   $pocetHodnoceni;

    public function __construct(
        ?int       $id,
        string     $nazev,
        string     $vyvojar,
        string     $popis,
        string     $obrazek,
        int        $idUzivatele,
        ?\DateTime $datum = null,
        float      $prumerneHodnoceni = 0.0,
        int        $pocetHodnoceni = 0
    ) {
        $this->id                 = $id;
        $this->nazev              = $nazev;
        $this->vyvojar            = $vyvojar;
        $this->popis              = $popis;
        $this->obrazek            = $obrazek;
        $this->idUzivatele        = $idUzivatele;
        $this->datum              = $datum;
        $this->prumerneHodnoceni  = $prumerneHodnoceni;
        $this->pocetHodnoceni     = $pocetHodnoceni;
    }

    public function getId(): ?int               { return $this->id; }
    public function getNazev(): string          { return $this->nazev; }
    public function getVyvojar(): string        { return $this->vyvojar; }
    public function getPopis(): string          { return $this->popis; }
    public function getObrazek(): string        { return $this->obrazek; }
    public function getIdUzivatele(): int       { return $this->idUzivatele; }
    public function getDatum(): ?\DateTime      { return $this->datum; }
    public function getPrumerneHodnoceni(): float { return $this->prumerneHodnoceni; }
    public function getPocetHodnoceni(): int    { return $this->pocetHodnoceni; }

    /** Může tento uživatel tuto hru upravovat/mazat? */
    public function muzeEditovat(Uzivatel $uzivatel): bool
    {
        return $uzivatel->getId() === $this->idUzivatele
            || $uzivatel->jeAdmin()
            || $uzivatel->jeSuperAdmin();
    }

    /** Zaformátované průměrné hodnocení pro zobrazení */
    public function formatovaneHodnoceni(): string
    {
        if ($this->pocetHodnoceni === 0) {
            return 'N/A';
        }
        return number_format($this->prumerneHodnoceni, 1) . ' (' . $this->pocetHodnoceni . ')';
    }

    /** Tovární metoda — vytvoří Hru z DB řádku */
    public static function zDatabaze(array $row): self
    {
        $datum = null;
        if (!empty($row['date'])) {
            try {
                $datum = new \DateTime($row['date']);
            } catch (\Exception $e) {
                $datum = null;
            }
        }

        return new self(
            (int)$row['id_game'],
            $row['title'],
            $row['developer'],
            $row['description'] ?? '',
            $row['image_url'] ?? '',
            (int)$row['id_user'],
            $datum,
            (float)($row['average_rating'] ?? 0),
            (int)($row['total_ratings'] ?? 0)
        );
    }
}
