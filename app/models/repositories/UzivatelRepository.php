<?php
namespace App\Models\Repositories;

use App\Models\Entities\Uzivatel;
use PDO;
use PDOException;

/**
 * Konkrétní implementace IUzivatelRepository přes PDO.
 */
class UzivatelRepository implements IUzivatelRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Databaze::getInstance();
    }

    /** {@inheritdoc} */
    public function najdiVsechny(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM " . TABLE_USER);
        $rows = $stmt->fetchAll();
        return array_map([Uzivatel::class, 'zDatabaze'], $rows);
    }

    /** {@inheritdoc} */
    public function najdiPodleId(int $id): ?Uzivatel
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM " . TABLE_USER . " WHERE id_user = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Uzivatel::zDatabaze($row) : null;
    }

    /** {@inheritdoc} */
    public function najdiPodlePrezdivky(string $prezdivka): ?Uzivatel
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM " . TABLE_USER . " WHERE login = :prezdivka"
        );
        $stmt->execute([':prezdivka' => $prezdivka]);
        $row = $stmt->fetch();
        return $row ? Uzivatel::zDatabaze($row) : null;
    }

    /** {@inheritdoc} */
    public function najdiPodleEmailu(string $email): ?Uzivatel
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM " . TABLE_USER . " WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? Uzivatel::zDatabaze($row) : null;
    }

    /** {@inheritdoc} */
    public function uloz(Uzivatel $uzivatel): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO " . TABLE_USER . " (login, email, password, role)
                VALUES (:prezdivka, :email, :heslo, :role)
            ");
            return $stmt->execute([
                ':prezdivka' => $uzivatel->getPrezdivka(),
                ':email'     => $uzivatel->getEmail(),
                ':heslo'     => $uzivatel->getHeslo(),
                ':role'      => (string)$uzivatel->getRole(),
            ]);
        } catch (PDOException $e) {
            error_log('UzivatelRepository::uloz: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function smazPodleId(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM " . TABLE_USER . " WHERE id_user = :id"
            );
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('UzivatelRepository::smazPodleId: ' . $e->getMessage());
            return false;
        }
    }

    /** {@inheritdoc} */
    public function zmenRoli(int $id, string $novaRole): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE " . TABLE_USER . "
                SET role = :role
                WHERE id_user = :id AND role = 'user'
            ");
            return $stmt->execute([':role' => $novaRole, ':id' => $id]);
        } catch (PDOException $e) {
            error_log('UzivatelRepository::zmenRoli: ' . $e->getMessage());
            return false;
        }
    }
}
