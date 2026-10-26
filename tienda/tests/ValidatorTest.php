<?php
declare(strict_types=1);

namespace Tests;

use App\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public static function emails(): array
    {
        return [
            'válido' => ['ana@correo.com', true],
            'subdominio' => ['ana@mail.tienda.mx', true],
            'sin arroba' => ['anacorreo.com', false],
            'sin dominio' => ['ana@', false],
            'vacío' => ['', false],
        ];
    }

    #[DataProvider('emails')]
    public function testEmail(string $v, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::email($v));
    }

    public static function telefonos(): array
    {
        return [
            ['8781234567', true],
            ['+52 878 123 4567', true],
            ['878-123-4567', true],
            ['12345', false],
            ['87812345678', false],
            ['abcdefghij', false],
        ];
    }

    #[DataProvider('telefonos')]
    public function testTelefono(string $v, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::phone($v));
    }

    public static function precios(): array
    {
        return [[10, true], ['99.99', true], [149.5, true], [0, false], [-5, false],
                ['abc', false], ['10.999', false], [null, false]];
    }

    #[DataProvider('precios')]
    public function testPrecio(mixed $v, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::price($v));
    }

    public static function stocks(): array
    {
        return [[0, true], [15, true], ['7', true], [-1, false], [1.5, false], ['x', false]];
    }

    #[DataProvider('stocks')]
    public function testStock(mixed $v, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::stock($v));
    }

    public function testSku(): void
    {
        $this->assertTrue(Validator::sku('CAF-0001'));
        $this->assertFalse(Validator::sku('caf-0001'));
        $this->assertFalse(Validator::sku('CAF0001'));
        $this->assertFalse(Validator::sku('CAFE-001'));
    }

    public function testLongitudesYRequerido(): void
    {
        $this->assertTrue(Validator::required(' hola '));
        $this->assertFalse(Validator::required('   '));
        $this->assertFalse(Validator::required(null));
        $this->assertTrue(Validator::minLength('abc', 3));
        $this->assertFalse(Validator::minLength('ab', 3));
        $this->assertTrue(Validator::maxLength('áéí', 3)); // multibyte
        $this->assertFalse(Validator::maxLength('abcd', 3));
    }

    public function testInListEsEstricto(): void
    {
        $this->assertTrue(Validator::inList('Café', ['Café', 'Tazas']));
        $this->assertFalse(Validator::inList('Otro', ['Café', 'Tazas']));
        $this->assertFalse(Validator::inList('0', ['Café']));
    }

    public function testSanitizeEscapaHtml(): void
    {
        $this->assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', Validator::sanitize('<script>alert("x")</script>'));
    }

    public function testContactoValido(): void
    {
        $this->assertSame([], Validator::validateContact([
            'nombre' => 'Ana Pérez', 'email' => 'ana@correo.com',
            'telefono' => '8781234567', 'mensaje' => 'Quisiera info de envíos.',
        ]));
    }

    public function testContactoTelefonoEsOpcional(): void
    {
        $e = Validator::validateContact([
            'nombre' => 'Ana', 'email' => 'ana@correo.com', 'telefono' => '', 'mensaje' => 'Mensaje suficientemente largo',
        ]);
        $this->assertArrayNotHasKey('telefono', $e);
    }

    public function testContactoInvalidoDevuelveErrores(): void
    {
        $e = Validator::validateContact(['nombre' => 'A', 'email' => 'mal', 'telefono' => '123', 'mensaje' => 'corto']);
        $this->assertSame(['nombre', 'email', 'telefono', 'mensaje'], array_keys($e));
    }

    public function testContactoCamposFaltantes(): void
    {
        $this->assertCount(3, Validator::validateContact([])); // telefono opcional
    }

    public function testProductoValido(): void
    {
        $this->assertSame([], Validator::validateProduct([
            'sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => 129.0, 'stock' => 3, 'categoria' => 'Tazas',
        ]));
    }

    public function testProductoInvalido(): void
    {
        $e = Validator::validateProduct(['sku' => 'x', 'nombre' => '', 'precio' => -1, 'stock' => -2, 'categoria' => '']);
        $this->assertSame(['sku', 'nombre', 'precio', 'stock', 'categoria'], array_keys($e));
    }
}
