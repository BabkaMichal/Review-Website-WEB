<?php
namespace App\Models\Services;

use App\Models\Entities\Hra;
use App\Models\Entities\Uzivatel;
use App\Models\Repositories\IHraRepository;

/**
 * Service pro případy užití týkající se Hry:
 */
class HraService
{
    private IHraRepository $hraRepo;

    // Povolené MIME typy obrázků
    private const POVOLENE_MIME = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct(IHraRepository $hraRepo)
    {
        $this->hraRepo = $hraRepo;
    }

    /**
     * Přidá novou hru.
     *
     * @param  array  $postData   Pole z $_POST
     * @param  array  $fileData   Pole z $_FILES['image']
     * @param  int    $idAutora   ID přihlášeného uživatele
     * @return string|null  Chyba, nebo null při úspěchu
     */
    public function pridejHru(array $postData, array $fileData, int $idAutora): ?string
    {
        $nazev   = trim($postData['title']       ?? '');
        $vyvojar = trim($postData['developer']   ?? '');
        $popis   = trim($postData['description'] ?? '');

        if (empty($nazev) || empty($vyvojar) || empty($popis)) {
            return 'Všechna textová pole musí být vyplněna.';
        }

        $chybaObrazku = $this->zpracujNahravaniObrazku($fileData, $obrazekUrl);
        if ($chybaObrazku !== null) {
            return $chybaObrazku;
        }

        $hra = new Hra(null, $nazev, $vyvojar, $popis, $obrazekUrl, $idAutora);

        if (!$this->hraRepo->uloz($hra)) {
            return 'Nepodařilo se uložit hru do databáze.';
        }

        return null;
    }

    /**
     * Aktualizuje existující hru.
     *
     * @param  int        $idHry
     * @param  array      $postData
     * @param  array      $fileData        Pole z $_FILES['image'] (může být prázdné)
     * @param  Uzivatel   $aktualniUzivatel Pro kontrolu oprávnění
     * @return string|null  Chyba, nebo null při úspěchu
     */
    public function aktualizujHru(
        int      $idHry,
        array    $postData,
        array    $fileData,
        Uzivatel $aktualniUzivatel
    ): ?string {
        $hra = $this->hraRepo->najdiPodleId($idHry);
        if ($hra === null) {
            return 'Hra s tímto ID neexistuje.';
        }

        if (!$hra->muzeEditovat($aktualniUzivatel)) {
            return 'Nemáte oprávnění upravovat tento příspěvek.';
        }

        $nazev   = trim($postData['title']       ?? '');
        $vyvojar = trim($postData['developer']   ?? '');
        $popis   = trim($postData['description'] ?? '');

        if (empty($nazev) || empty($vyvojar) || empty($popis)) {
            return 'Všechna textová pole musí být vyplněna.';
        }

        // Zpracování nového obrázku (pokud byl nahrán)
        $obrazekUrl = $hra->getObrazek(); // výchozí = stávající

        if (!empty($fileData['name'])) {
            $chybaObrazku = $this->zpracujNahravaniObrazku($fileData, $novyObrazekUrl);
            if ($chybaObrazku !== null) {
                return $chybaObrazku;
            }
            // Smažeme starý obrázek
            $staryObrazek = 'app/images/' . $hra->getObrazek();
            if ($hra->getObrazek() && file_exists($staryObrazek)) {
                unlink($staryObrazek);
            }
            $obrazekUrl = $novyObrazekUrl;
        }

        $aktualizovanaHra = new Hra(
            $idHry, $nazev, $vyvojar, $popis, $obrazekUrl, $hra->getIdUzivatele()
        );

        if (!$this->hraRepo->aktualizuj($aktualizovanaHra)) {
            return 'Chyba při ukládání do databáze.';
        }

        return null;
    }

    /**
     * Smaže hru (pouze admin).
     */
    public function smazHru(int $idHry, Uzivatel $aktualniUzivatel): ?string
    {
        if (!$aktualniUzivatel->jeAdmin() && !$aktualniUzivatel->jeSuperAdmin()) {
            return 'Pouze admin může mazat příspěvky.';
        }

        if (!$this->hraRepo->smazPodleId($idHry)) {
            return 'Nepodařilo se smazat příspěvek.';
        }

        return null;
    }

    public function najdiVsechny(): array
    {
        return $this->hraRepo->najdiVsechny();
    }

    public function najdiPodleId(int $id): ?Hra
    {
        return $this->hraRepo->najdiPodleId($id);
    }

    public function najdiPopis(int $id): ?string
    {
        return $this->hraRepo->najdiPopisPodleId($id);
    }

    /**
     * Zpracuje nahrávání obrázku, nastaví $obrazekUrl výstupní proměnnou.
     * Vrátí chybovou zprávu nebo null při úspěchu.
     */
    private function zpracujNahravaniObrazku(array $fileData, ?string &$obrazekUrl): ?string
    {
        if (!isset($fileData['error']) || $fileData['error'] !== UPLOAD_ERR_OK) {
            return 'Prosím nahrajte obrázek.';
        }

        // Bezpečnostní kontrola MIME typu
        $mime = mime_content_type($fileData['tmp_name']);
        if (!in_array($mime, self::POVOLENE_MIME, true)) {
            return 'Povolené formáty: JPEG, PNG, GIF, WebP.';
        }

        $uploadDir = 'app/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $unikatniJmeno = uniqid('img_', true) . '_' . basename($fileData['name']);
        $cilPath        = $uploadDir . $unikatniJmeno;

        if (!move_uploaded_file($fileData['tmp_name'], $cilPath)) {
            return 'Nepodařilo se nahrát obrázek.';
        }

        $obrazekUrl = $unikatniJmeno;
        return null;
    }
}
