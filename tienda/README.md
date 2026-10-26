# Tostadero Norte – Tienda demo en PHP

Landing page + catálogo con backend PHP (sin framework) y CI con GitHub Actions.

## Estructura
- `public/` → `index.php` (landing + formulario de contacto), `catalogo.php` (búsqueda y filtros)
- `src/Validator.php` → validaciones (email, teléfono, precio, stock, SKU, etc.)
- `src/ProductRepository.php` → lectura de `data/products.json`
- `tests/` → pruebas unitarias PHPUnit sobre las validaciones
- `.github/workflows/ci.yml` → pipeline

## Ejecutar local
    composer install
    php -S localhost:8000 -t public

## Pruebas
    composer lint   # compilación (php -l)
    composer test   # PHPUnit

## CI (GitHub Actions)
En cada push a `main` y en cada pull request:
1. **build**: `composer validate`, `php -l` sobre todos los archivos, `composer install` (PHP 8.1, 8.2, 8.3).
2. **test**: PHPUnit con `--testdox`. Si falla una validación, el pipeline falla.
