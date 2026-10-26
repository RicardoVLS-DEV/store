<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

use App\ProductRepository;
use App\Validator;

$repo = new ProductRepository(__DIR__ . '/../data/products.json');
$errors = [];
$old = ['nombre' => '', 'email' => '', 'telefono' => '', 'mensaje' => ''];
$enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    $errors = Validator::validateContact($old);
    $enviado = $errors === [];
    if ($enviado) {
        $old = array_map(fn() => '', $old); // aquí podrías guardar en BD o enviar correo
    }
}

$title = 'Inicio';
include __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <h1>Café de especialidad, directo a tu taza</h1>
  <p>Granos tostados cada semana, accesorios y todo lo necesario para tu ritual diario.</p>
  <a class="btn" href="catalogo.php">Ver catálogo</a>
</section>

<section class="features">
  <div><span>🌱</span><h3>Origen mexicano</h3><p>Trabajamos con productores locales.</p></div>
  <div><span>🔥</span><h3>Tostado fresco</h3><p>Tostamos por lotes pequeños.</p></div>
  <div><span>🚚</span><h3>Envíos a todo el país</h3><p>Entrega en 2 a 5 días hábiles.</p></div>
</section>

<section class="section">
  <h2>Destacados</h2>
  <div class="grid">
    <?php foreach ($repo->featured(3) as $p) { include __DIR__ . '/partials/card.php'; } ?>
  </div>
</section>

<section class="section" id="contacto">
  <h2>Contáctanos</h2>
  <?php if ($enviado): ?><p class="alert success">¡Gracias! Recibimos tu mensaje.</p><?php endif; ?>
  <form method="post" class="form" novalidate action="index.php#contacto">
    <?php
    $fields = [['nombre', 'Nombre', 'text'], ['email', 'Correo', 'email'], ['telefono', 'Teléfono (opcional)', 'tel']];
    foreach ($fields as [$n, $label, $type]): ?>
      <label><?= $label ?>
        <input type="<?= $type ?>" name="<?= $n ?>" value="<?= e($old[$n]) ?>">
        <?php if (isset($errors[$n])): ?><span class="err"><?= e($errors[$n]) ?></span><?php endif; ?>
      </label>
    <?php endforeach; ?>
    <label>Mensaje
      <textarea name="mensaje" rows="4"><?= e($old['mensaje']) ?></textarea>
      <?php if (isset($errors['mensaje'])): ?><span class="err"><?= e($errors['mensaje']) ?></span><?php endif; ?>
    </label>
    <button class="btn" type="submit">Enviar</button>
  </form>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
