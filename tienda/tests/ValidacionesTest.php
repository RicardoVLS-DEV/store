<?php
declare(strict_types=1);

namespace Tests;

use App\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * PRUEBAS EDITABLES PARA TODAS LAS VALIDACIONES
 *
 * Cada validación tiene dos listas:
 *   - validos():   datos que la validación DEBE aceptar
 *   - invalidos(): datos que la validación DEBE rechazar
 *
 * Agrega tus propios datos en las listas. Para provocar un fallo a propósito,
 * escribe un dato malo en la lista de válidos (o uno bueno en la de inválidos).
 */
final class ValidacionesTest extends TestCase
{
    // ───────────── EMAIL ─────────────
    public static function emailValidos(): array
    {
        return [
            'normal'     => ['ana@correo.com'],
            'subdominio' => ['ana@mail.tienda.mx'],
            // agrega más aquí
        ];
    }

    public static function emailInvalidos(): array
    {
        return [
            'sin arroba' => ['anacorreo.com'],
            'sin dominio' => ['ana@'],
            'sin usuario' => ['@correo.com'],
            'vacío'       => [''],
            // agrega más aquí
        ];
    }

    #[DataProvider('emailValidos')]
    public function testEmailValido(string $v): void
    {
        $this->assertTrue(Validator::email($v), "Se esperaba que el correo '$v' fuera válido");
    }

    #[DataProvider('emailInvalidos')]
    public function testEmailInvalido(string $v): void
    {
        $this->assertFalse(Validator::email($v), "Se esperaba que el correo '$v' fuera inválido");
    }

    // ───────────── TELÉFONO (10 dígitos, +52 opcional) ─────────────
    public static function telefonoValidos(): array
    {
        return [
            'solo dígitos' => ['8781234567'],
            'con +52'      => ['+52 878 123 4567'],
            'con guiones'  => ['878-123-4567'],
        ];
    }

    public static function telefonoInvalidos(): array
    {
        return [
            'muy corto' => ['12345'],
            'muy largo' => ['87812345678'],
            'letras'    => ['abcdefghij'],
            'vacío'     => [''],
        ];
    }

    #[DataProvider('telefonoValidos')]
    public function testTelefonoValido(string $v): void
    {
        $this->assertTrue(Validator::phone($v), "Se esperaba que el teléfono '$v' fuera válido");
    }

    #[DataProvider('telefonoInvalidos')]
    public function testTelefonoInvalido(string $v): void
    {
        $this->assertFalse(Validator::phone($v), "Se esperaba que el teléfono '$v' fuera inválido");
    }

    // ───────────── PRECIO (> 0, máx. 2 decimales) ─────────────
    public static function precioValidos(): array
    {
        return [
            'entero'     => [10],
            'decimal'    => ['99.99'],
            'un decimal' => [149.5],
        ];
    }

    public static function precioInvalidos(): array
    {
        return [
            'cero'          => [0],
            'negativo'      => [-5],
            'texto'         => ['abc'],
            'tres decimales' => ['10.999'],
            'nulo'          => [null],
        ];
    }

    #[DataProvider('precioValidos')]
    public function testPrecioValido(mixed $v): void
    {
        $this->assertTrue(Validator::price($v), 'Se esperaba que el precio ' . var_export($v, true) . ' fuera válido');
    }

    #[DataProvider('precioInvalidos')]
    public function testPrecioInvalido(mixed $v): void
    {
        $this->assertFalse(Validator::price($v), 'Se esperaba que el precio ' . var_export($v, true) . ' fuera inválido');
    }

    // ───────────── STOCK (entero >= 0) ─────────────
    public static function stockValidos(): array
    {
        return [
            'cero'        => [0],
            'positivo'    => [15],
            'texto entero' => ['7'],
        ];
    }

    public static function stockInvalidos(): array
    {
        return [
            'negativo' => [-1],
            'decimal'  => [1.5],
            'texto'    => ['x'],
        ];
    }

    #[DataProvider('stockValidos')]
    public function testStockValido(mixed $v): void
    {
        $this->assertTrue(Validator::stock($v), 'Se esperaba que el stock ' . var_export($v, true) . ' fuera válido');
    }

    #[DataProvider('stockInvalidos')]
    public function testStockInvalido(mixed $v): void
    {
        $this->assertFalse(Validator::stock($v), 'Se esperaba que el stock ' . var_export($v, true) . ' fuera inválido');
    }

    // ───────────── SKU (formato ABC-1234) ─────────────
    public static function skuValidos(): array
    {
        return [
            'café'  => ['CAF-0001'],
            'taza'  => ['TAZ-0002'],
        ];
    }

    public static function skuInvalidos(): array
    {
        return [
            'minúsculas'  => ['caf-0001'],
            'sin guion'   => ['CAF0001'],
            'letras de más' => ['CAFE-001'],
            'vacío'       => [''],
        ];
    }

    #[DataProvider('skuValidos')]
    public function testSkuValido(string $v): void
    {
        $this->assertTrue(Validator::sku($v), "Se esperaba que el SKU '$v' fuera válido");
    }

    #[DataProvider('skuInvalidos')]
    public function testSkuInvalido(string $v): void
    {
        $this->assertFalse(Validator::sku($v), "Se esperaba que el SKU '$v' fuera inválido");
    }

    // ───────────── CAMPO OBLIGATORIO ─────────────
    public static function requeridoValidos(): array
    {
        return [
            'texto'        => ['hola'],
            'con espacios' => ['  hola  '],
        ];
    }

    public static function requeridoInvalidos(): array
    {
        return [
            'vacío'   => [''],
            'espacios' => ['   '],
            'nulo'    => [null],
        ];
    }

    #[DataProvider('requeridoValidos')]
    public function testRequeridoValido(?string $v): void
    {
        $this->assertTrue(Validator::required($v), 'Se esperaba que ' . var_export($v, true) . ' fuera válido');
    }

    #[DataProvider('requeridoInvalidos')]
    public function testRequeridoInvalido(?string $v): void
    {
        $this->assertFalse(Validator::required($v), 'Se esperaba que ' . var_export($v, true) . ' fuera inválido');
    }

    // ───────────── LONGITUD MÍNIMA (3 caracteres) ─────────────
    public static function minLongitudValidos(): array
    {
        return [
            'exacto' => ['abc'],
            'más'    => ['abcdef'],
        ];
    }

    public static function minLongitudInvalidos(): array
    {
        return [
            'dos letras' => ['ab'],
            'vacío'      => [''],
        ];
    }

    #[DataProvider('minLongitudValidos')]
    public function testMinLongitudValido(string $v): void
    {
        $this->assertTrue(Validator::minLength($v, 3), "Se esperaba que '$v' cumpliera el mínimo de 3");
    }

    #[DataProvider('minLongitudInvalidos')]
    public function testMinLongitudInvalido(string $v): void
    {
        $this->assertFalse(Validator::minLength($v, 3), "Se esperaba que '$v' NO cumpliera el mínimo de 3");
    }

    // ───────────── LONGITUD MÁXIMA (10 caracteres) ─────────────
    public static function maxLongitudValidos(): array
    {
        return [
            'corto'     => ['hola'],
            'exacto'    => ['1234567890'],
            'acentos'   => ['áéíóú'],
        ];
    }

    public static function maxLongitudInvalidos(): array
    {
        return [
            'once caracteres' => ['12345678901'],
        ];
    }

    #[DataProvider('maxLongitudValidos')]
    public function testMaxLongitudValido(string $v): void
    {
        $this->assertTrue(Validator::maxLength($v, 10), "Se esperaba que '$v' cumpliera el máximo de 10");
    }

    #[DataProvider('maxLongitudInvalidos')]
    public function testMaxLongitudInvalido(string $v): void
    {
        $this->assertFalse(Validator::maxLength($v, 10), "Se esperaba que '$v' NO cumpliera el máximo de 10");
    }

    // ───────────── CATEGORÍA EN LISTA PERMITIDA ─────────────
    private const CATEGORIAS = ['Café', 'Accesorios', 'Tazas'];

    public static function categoriaValidos(): array
    {
        return [['Café'], ['Accesorios'], ['Tazas']];
    }

    public static function categoriaInvalidos(): array
    {
        return [
            'inexistente' => ['Electrónica'],
            'minúsculas'  => ['café'],
            'vacío'       => [''],
        ];
    }

    #[DataProvider('categoriaValidos')]
    public function testCategoriaValida(string $v): void
    {
        $this->assertTrue(Validator::inList($v, self::CATEGORIAS), "Se esperaba que la categoría '$v' fuera válida");
    }

    #[DataProvider('categoriaInvalidos')]
    public function testCategoriaInvalida(string $v): void
    {
        $this->assertFalse(Validator::inList($v, self::CATEGORIAS), "Se esperaba que la categoría '$v' fuera inválida");
    }

    // ───────────── FORMULARIO DE CONTACTO COMPLETO ─────────────
    public static function contactoValidos(): array
    {
        return [
            'con teléfono' => [[
                'nombre' => 'Ana Pérez', 'email' => 'ana@correo.com',
                'telefono' => '8781234567', 'mensaje' => 'Quisiera info de envíos.',
            ]],
            'sin teléfono' => [[
                'nombre' => 'Luis', 'email' => 'luis@mail.com',
                'telefono' => '', 'mensaje' => 'Mensaje suficientemente largo',
            ]],
        ];
    }

    public static function contactoInvalidos(): array
    {
        return [
            'nombre corto' => [[
                'nombre' => 'A', 'email' => 'ana@correo.com',
                'telefono' => '', 'mensaje' => 'Mensaje suficientemente largo',
            ]],
            'correo malo' => [[
                'nombre' => 'Ana Pérez', 'email' => 'hola',
                'telefono' => '', 'mensaje' => 'Mensaje suficientemente largo',
            ]],
            'teléfono malo' => [[
                'nombre' => 'Ana Pérez', 'email' => 'ana@correo.com',
                'telefono' => '123', 'mensaje' => 'Mensaje suficientemente largo',
            ]],
            'mensaje corto' => [[
                'nombre' => 'Ana Pérez', 'email' => 'ana@correo.com',
                'telefono' => '', 'mensaje' => 'corto',
            ]],
            'todo vacío' => [[]],
        ];
    }

    #[DataProvider('contactoValidos')]
    public function testContactoValido(array $datos): void
    {
        $this->assertSame([], Validator::validateContact($datos), 'Se esperaba que el formulario fuera válido');
    }

    #[DataProvider('contactoInvalidos')]
    public function testContactoInvalido(array $datos): void
    {
        $this->assertNotSame([], Validator::validateContact($datos), 'Se esperaba que el formulario fuera inválido');
    }

    // ───────────── PRODUCTO COMPLETO ─────────────
    public static function productoValidos(): array
    {
        return [
            'taza' => [[
                'sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => 129.0, 'stock' => 3, 'categoria' => 'Tazas',
            ]],
            'agotado' => [[
                'sku' => 'ACC-0002', 'nombre' => 'Molino', 'precio' => 599.99, 'stock' => 0, 'categoria' => 'Accesorios',
            ]],
        ];
    }

    public static function productoInvalidos(): array
    {
        return [
            'sku malo' => [[
                'sku' => 'x', 'nombre' => 'Taza', 'precio' => 129.0, 'stock' => 3, 'categoria' => 'Tazas',
            ]],
            'precio negativo' => [[
                'sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => -1, 'stock' => 3, 'categoria' => 'Tazas',
            ]],
            'stock negativo' => [[
                'sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => 129.0, 'stock' => -2, 'categoria' => 'Tazas',
            ]],
            'sin nombre' => [[
                'sku' => 'TAZ-0001', 'nombre' => '', 'precio' => 129.0, 'stock' => 3, 'categoria' => 'Tazas',
            ]],
            'sin categoría' => [[
                'sku' => 'TAZ-0001', 'nombre' => 'Taza', 'precio' => 129.0, 'stock' => 3, 'categoria' => '',
            ]],
        ];
    }

    #[DataProvider('productoValidos')]
    public function testProductoValido(array $datos): void
    {
        $this->assertSame([], Validator::validateProduct($datos), 'Se esperaba que el producto fuera válido');
    }

    #[DataProvider('productoInvalidos')]
    public function testProductoInvalido(array $datos): void
    {
        $this->assertNotSame([], Validator::validateProduct($datos), 'Se esperaba que el producto fuera inválido');
    }
}