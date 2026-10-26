<?php
declare(strict_types=1);

namespace App;

final class ProductRepository
{
    /** @var array<int,array<string,mixed>> */
    private array $products = [];

    public function __construct(string $jsonFile)
    {
        $data = json_decode((string) file_get_contents($jsonFile), true) ?: [];
        // Solo se cargan productos que pasan las validaciones.
        foreach ($data as $p) {
            if (Validator::validateProduct($p) === []) {
                $this->products[] = $p;
            }
        }
    }

    public function all(): array
    {
        return $this->products;
    }

    public function featured(int $n = 3): array
    {
        return array_slice(array_values(array_filter($this->products, fn($p) => $p['stock'] > 0)), 0, $n);
    }

    public function categories(): array
    {
        return array_values(array_unique(array_column($this->products, 'categoria')));
    }

    public function filter(?string $q = null, ?string $categoria = null): array
    {
        return array_values(array_filter($this->products, function ($p) use ($q, $categoria) {
            if ($categoria && $p['categoria'] !== $categoria) {
                return false;
            }
            return !$q || mb_stripos($p['nombre'], $q) !== false;
        }));
    }

    public static function money(float $amount): string
    {
        return '$' . number_format($amount, 2) . ' MXN';
    }
}
