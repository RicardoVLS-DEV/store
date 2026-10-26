<?php /** @var array $p */ ?>
<article class="card">
  <div class="thumb"><?= e($p['emoji'] ?? '📦') ?></div>
  <span class="tag"><?= e($p['categoria']) ?></span>
  <h3><?= e($p['nombre']) ?></h3>
  <p class="price"><?= e(App\ProductRepository::money((float) $p['precio'])) ?></p>
  <p class="stock <?= $p['stock'] > 0 ? 'ok' : 'out' ?>"><?= $p['stock'] > 0 ? 'En stock (' . (int) $p['stock'] . ')' : 'Agotado' ?></p>
  <small>SKU <?= e($p['sku']) ?></small>
</article>
