<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

use App\ProductRepository;
use App\Validator;

$repo = new ProductRepository(__DIR__ . '/../data/products.json');
$cats = $repo->categories();

$q = trim((string) ($_GET['q'] ?? ''));
$cat = (string) ($_GET['categoria'] ?? '');
$aviso = null;

if ($q !== '' && !Validator::maxLength($q, 50)) {
    $aviso = 'La búsqueda no puede exceder 50 caracteres.';
    $q = '';
}
if ($cat !== '' && !Validator::inList($cat, $cats)) {
    $aviso = 'Categoría no válida.';
    $cat = '';
}

$productos = $repo->filter($q ?: null, $cat ?: null);
$title = 'Catálogo';
include __DIR__ . '/partials/header.php';
?>
<section class="section">
  <h1>Catálogo</h1>
  <form class="filters" method="get">
    <input type="search" name="q" placeholder="Buscar producto…" value="<?= e($q) ?>">
    <select name="categoria">
      <option value="">Todas las categorías</option>
      <?php foreach ($cats as $c): ?>
        <option <?= $c === $cat ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrar</button>
  </form>
  <?php if ($aviso): ?><p class="alert error"><?= e($aviso) ?></p><?php endif; ?>
  <?php if (!$productos): ?><p>No se encontraron productos.</p><?php endif; ?>
  <div class="grid">
    <?php foreach ($productos as $p) { include __DIR__ . '/partials/card.php'; } ?>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
