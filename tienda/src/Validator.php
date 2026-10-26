<?php
declare(strict_types=1);

namespace App;

/** Validaciones reutilizables (formularios y catálogo). Probadas con PHPUnit. */
final class Validator
{
    public static function required(?string $v): bool
    {
        return $v !== null && trim($v) !== '';
    }

    public static function email(string $v): bool
    {
        return filter_var($v, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function minLength(string $v, int $min): bool
    {
        return mb_strlen(trim($v)) >= $min;
    }

    public static function maxLength(string $v, int $max): bool
    {
        return mb_strlen(trim($v)) <= $max;
    }

    /** Teléfono MX: 10 dígitos, con +52 opcional; acepta espacios y guiones. */
    public static function phone(string $v): bool
    {
        $clean = preg_replace('/[\s-]/', '', $v);
        return (bool) preg_match('/^(\+52)?\d{10}$/', (string) $clean);
    }

    /** Precio > 0 con máximo 2 decimales. */
    public static function price(mixed $v): bool
    {
        if (!is_numeric($v) || (float) $v <= 0) {
            return false;
        }
        return (bool) preg_match('/^\d+(\.\d{1,2})?$/', (string) $v);
    }

    /** Entero >= 0 (stock). */
    public static function stock(mixed $v): bool
    {
        return filter_var($v, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== false;
    }

    /** SKU con formato ABC-1234. */
    public static function sku(string $v): bool
    {
        return (bool) preg_match('/^[A-Z]{3}-\d{4}$/', $v);
    }

    public static function inList(string $v, array $allowed): bool
    {
        return in_array($v, $allowed, true);
    }

    public static function sanitize(string $v): string
    {
        return htmlspecialchars(trim($v), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** @return array<string,string> errores por campo (vacío = válido) */
    public static function validateContact(array $d): array
    {
        $e = [];
        $nombre = (string) ($d['nombre'] ?? '');
        $email = (string) ($d['email'] ?? '');
        $tel = (string) ($d['telefono'] ?? '');
        $msg = (string) ($d['mensaje'] ?? '');

        if (!self::required($nombre) || !self::minLength($nombre, 3)) {
            $e['nombre'] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif (!self::maxLength($nombre, 60)) {
            $e['nombre'] = 'El nombre no puede exceder 60 caracteres.';
        }
        if (!self::required($email) || !self::email($email)) {
            $e['email'] = 'Ingresa un correo válido.';
        }
        if (self::required($tel) && !self::phone($tel)) {
            $e['telefono'] = 'El teléfono debe tener 10 dígitos.';
        }
        if (!self::required($msg) || !self::minLength($msg, 10)) {
            $e['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
        } elseif (!self::maxLength($msg, 500)) {
            $e['mensaje'] = 'El mensaje no puede exceder 500 caracteres.';
        }
        return $e;
    }

    /** @return array<string,string> */
    public static function validateProduct(array $d): array
    {
        $e = [];
        if (!self::sku((string) ($d['sku'] ?? ''))) {
            $e['sku'] = 'SKU inválido (formato ABC-1234).';
        }
        $n = (string) ($d['nombre'] ?? '');
        if (!self::required($n) || !self::maxLength($n, 80)) {
            $e['nombre'] = 'Nombre requerido (máx. 80 caracteres).';
        }
        if (!self::price($d['precio'] ?? null)) {
            $e['precio'] = 'El precio debe ser mayor a 0 con máx. 2 decimales.';
        }
        if (!self::stock($d['stock'] ?? null)) {
            $e['stock'] = 'El stock debe ser un entero mayor o igual a 0.';
        }
        if (!self::required((string) ($d['categoria'] ?? ''))) {
            $e['categoria'] = 'La categoría es requerida.';
        }
        return $e;
    }
}
