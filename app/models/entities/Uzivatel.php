<?php
namespace App\Models\Entities;

/**
 * Doménová entita Uživatel.
 */
class Uzivatel
{
    private ?int   $id;
    private string $prezdivka;   // přezdívka
    private string $email;       // email
    private string $heslo;       // heslo (hash)
    private Role   $role;        // Má Přidělenou Role

    public function __construct(
        ?int   $id,
        string $prezdivka,
        string $email,
        string $heslo,
        Role   $role
    ) {
        $this->id        = $id;
        $this->prezdivka = $prezdivka;
        $this->email     = $email;
        $this->heslo     = $heslo;
        $this->role      = $role;
    }

    public function getId(): ?int          { return $this->id; }
    public function getPrezdivka(): string { return $this->prezdivka; }
    public function getEmail(): string     { return $this->email; }
    public function getHeslo(): string     { return $this->heslo; }
    public function getRole(): Role        { return $this->role; }

    /** Ověří heslo oproti uloženému hashi */
    public function overHeslo(string $hesloPlaintext): bool
    {
        return password_verify($hesloPlaintext, $this->heslo);
    }

    /** Má uživatel danou roli nebo vyšší? */
    public function maOpravu(string $minimumRole): bool
    {
        return $this->role->maOpravu($minimumRole);
    }

    public function jeAdmin(): bool
    {
        return $this->role->maOpravu(Role::ADMIN);
    }

    public function jeSuperAdmin(): bool
    {
        return $this->role->maOpravu(Role::SUPERADMIN);
    }

    /** Tovární metoda — vytvoří Uživatele z DB řádku */
    public static function zDatabaze(array $row): self
    {
        return new self(
            (int)$row['id_user'],
            $row['login'],
            $row['email'],
            $row['password'],
            new Role($row['role'] ?? Role::USER)
        );
    }
}
