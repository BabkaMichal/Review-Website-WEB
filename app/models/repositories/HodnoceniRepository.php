<?php
namespace App\Models\Repositories;

use App\Models\Entities\Hodnoceni;
use PDO;
use PDOException;

/**
 * Konkrétní implementace IHodnoceniRepository přes PDO.
 */
class HodnoceniRepository implements IHodnoceniRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Databaze::getInstance();
    }

    /** {@inheritdoc} */
    public function vloz(Hodnoceni $hodnoceni): int|false
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO " . TABLE_RATING . " (id_user, id_game, rating_value, created_at)
                VALUES (:idUzivatele, :idHry, :pocetHvezd, NOW())
            ");
            $ok = $stmt->execute([
                ':idUzivatele' => $hodnoceni->getIdUzivatele(),
                ':idHry'       => $hodnoceni->getIdHry(),
                ':pocetHvezd'  => $hodnoceni->getPocetHvezd(),
            ]);
            return $ok ? (int)$this->pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log('HodnoceniRepository::vloz: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function najdiPodleUzivateleAHry(int $idUzivatele, int $idHry): ?Hodnoceni
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM " . TABLE_RATING . "
                WHERE id_user = :idUzivatele AND id_game = :idHry
                LIMIT 1
            ");
            $stmt->execute([':idUzivatele' => $idUzivatele, ':idHry' => $idHry]);
            $row = $stmt->fetch();
            return $row ? Hodnoceni::zDatabaze($row) : null;
        } catch (PDOException $e) {
            error_log('HodnoceniRepository::najdiPodleUzivateleAHry: ' . $e->getMessage());
            return null;
        }
    }

    /** {@inheritdoc} */
    public function aktualizujPodleId(int $id, int $pocetHvezd): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . TABLE_RATING . "
                SET rating_value = :pocetHvezd, created_at = NOW()
                WHERE id_rating  = :id
            ");
            return $stmt->execute([':pocetHvezd' => $pocetHvezd, ':id' => $id]);
        } catch (PDOException $e) {
            error_log('HodnoceniRepository::aktualizujPodleId: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function aktualizujPodleUzivateleAHry(int $idUzivatele, int $idHry, int $pocetHvezd): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . TABLE_RATING . "
                SET rating_value = :pocetHvezd, created_at = NOW()
                WHERE id_user    = :idUzivatele AND id_game = :idHry
            ");
            return $stmt->execute([
                ':pocetHvezd'  => $pocetHvezd,
                ':idUzivatele' => $idUzivatele,
                ':idHry'       => $idHry,
            ]);
        } catch (PDOException $e) {
            error_log('HodnoceniRepository::aktualizujPodleUzivateleAHry: ' . $e->getMessage());
            return false;
        }
    }
}
