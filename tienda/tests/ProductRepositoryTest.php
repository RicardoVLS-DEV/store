<?php
declare(strict_types=1);

namespace Tests;

use App\ProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductRepositoryTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = tempnam(sys_get_temp_dir(), 'prod');
        file_put_contents($this->file, json_encode([
            ['sku' => 'CAF-0001', 'nombre' => 'Café Chiapas', 'precio' => 100, 'stock' => 5, 'categoria' => 'Café'],
            ['sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => 50, 'stock' => 0, 'categoria' => 'Tazas'],
            ['sku' => 'MALO', 'nombre' => 'Inválido', 'precio' => -1, 'stock' => 1, 'categoria' => 'Café'],
        ]));
    }

    protected function tearDown(): void
    {
        unlink($this->file);
    }

    public function testDescartaProductosInvalidos(): void
    {
        $this->assertCount(2, (new ProductRepository($this->file))->all());
    }

    public function testFiltraPorCategoriaYTexto(): void
    {
        $r = new ProductRepository($this->file);
        $this->assertCount(1, $r->filter(null, 'Tazas'));
        $this->assertCount(1, $r->filter('chiapas'));
        $this->assertCount(0, $r->filter('zzz'));
    }

    public function testDestacadosOmiteAgotados(): void
    {
        $f = (new ProductRepository($this->file))->featured();
        $this->assertCount(1, $f);
        $this->assertSame('CAF-0001', $f[0]['sku']);
    }

    function testFormatoMoneda(): void
    {
        $this->assertSame('$1,234.50 MXN', ProductRepository::money(1234.5));
    }
}
