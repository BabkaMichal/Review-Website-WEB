<?php
namespace App\Models\Entities;

/**
 * Doménová entita Role.
 * Zapouzdřuje název role a stupeň pravomocí.
 */
class Role
{
    // Konstanty pro stupeň pravomocí (stupeň_pravomocí)
    public const GUEST       = 'guest';
    public const USER        = 'user';
    public const ADMIN       = 'admin';
    public const SUPERADMIN  = 'superadmin';

    private string $nazevRole;
    private int    $stupenPravomoci;

    // Mapování stupeň → název
    private static array $UROVEN = [
        self::GUEST      => 0,
        self::USER       => 1,
        self::ADMIN      => 2,
        self::SUPERADMIN => 3,
    ];

    public function __construct(string $nazevRole)
    {
        if (!array_key_exists($nazevRole, self::$UROVEN)) {
            throw new \InvalidArgumentException("Neznámá role: $nazevRole");
        }
        $this->nazevRole       = $nazevRole;
        $this->stupenPravomoci = self::$UROVEN[$nazevRole];
    }

    public function getNazevRole(): string
    {
        return $this->nazevRole;
    }

    public function getStupenPravomoci(): int
    {
        return $this->stupenPravomoci;
    }

    /** Má tato role alespoň danou úroveň pravomocí? */
    public function maOpravu(string $minimumRole): bool
    {
        $min = self::$UROVEN[$minimumRole] ?? 0;
        return $this->stupenPravomoci >= $min;
    }

    public function __toString(): string
    {
        return $this->nazevRole;
    }
}
