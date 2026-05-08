<?php
namespace App\Models\Repositories;

use App\Models\Entities\Hra;
use PDO;
use PDOException;

/**
 * Konkrétní implementace IHraRepository přes PDO.
 * Obsahuje veškerou SQL logiku pro entitu Hra.
 */
class HraRepository implements IHraRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Databaze::getInstance();
    }

    /** {@inheritdoc} */
    public function najdiVsechny(): array
    {
        $sql = "
            SELECT
                g.*,
                ROUND(AVG(r.rating_value), 1) AS average_rating,
                COUNT(r.id_rating)             AS total_ratings
            FROM " . TABLE_INTRODUCTION . " AS g
            LEFT JOIN " . TABLE_RATING . " AS r ON g.id_game = r.id_game
            GROUP BY g.id_game
            ORDER BY g.date DESC
        ";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return array_map([Hra::class, 'zDatabaze'], $rows);
    }

    /** {@inheritdoc} */
    public function najdiPodleId(int $id): ?Hra
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM " . TABLE_INTRODUCTION . " WHERE id_game = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Hra::zDatabaze($row) : null;
    }

    /** {@inheritdoc} */
    public function uloz(Hra $hra): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO " . TABLE_INTRODUCTION . "
                    (title, developer, description, image_url, id_user)
                VALUES
                    (:nazev, :vyvojar, :popis, :obrazek, :idUzivatele)
            ");
            return $stmt->execute([
                ':nazev'       => $hra->getNazev(),
                ':vyvojar'     => $hra->getVyvojar(),
                ':popis'       => $hra->getPopis(),
                ':obrazek'     => $hra->getObrazek(),
                ':idUzivatele' => $hra->getIdUzivatele(),
            ]);
        } catch (PDOException $e) {
            error_log('HraRepository::uloz: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function aktualizuj(Hra $hra): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . TABLE_INTRODUCTION . "
                SET title       = :nazev,
                    developer   = :vyvojar,
                    description = :popis,
                    image_url   = :obrazek
                WHERE id_game   = :id
            ");
            return $stmt->execute([
                ':nazev'   => $hra->getNazev(),
                ':vyvojar' => $hra->getVyvojar(),
                ':popis'   => $hra->getPopis(),
                ':obrazek' => $hra->getObrazek(),
                ':id'      => $hra->getId(),
            ]);
        } catch (PDOException $e) {
            error_log('HraRepository::aktualizuj: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function smazPodleId(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM " . TABLE_INTRODUCTION . " WHERE id_game = :id"
            );
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('HraRepository::smazPodleId: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function najdiPopisPodleId(int $id): ?string
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT description FROM " . TABLE_INTRODUCTION . " WHERE id_game = :id"
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ? $row['description'] : null;
        } catch (PDOException $e) {
            error_log('HraRepository::najdiPopisPodleId: ' . $e->getMessage());
            return null;
        }
    }
}
