<?php
/**
 * Plantilla de catálogo (la usan caballero.php, dama.php y decants.php).
 * Variables que debe definir la página que la incluye:
 *   $categoria    'caballero' | 'dama' | 'decant'
 *   $titulo       título de la pestaña
 *   $kicker       texto pequeño sobre el título
 *   $encabezado   título grande
 *   $descripcion  frase debajo del título
 */
require_once __DIR__ . '/funciones.php';

$pagina = $categoria;
$perfumes = perfumes_por_categoria($categoria);
$total = count($perfumes);
$disponibles = count(array_filter($perfumes, fn($p) => (int) $p['disponible'] === 1));

require __DIR__ . '/header.php';
?>

<section class="hero-cat reveal">
    <p class="kicker"><?= h($kicker) ?></p>
    <h1><?= $encabezado /* contiene HTML propio */ ?></h1>
    <p class="lead"><?= h($descripcion) ?></p>
    <p class="resumen">
        <span><b data-contar="<?= $total ?>"><?= $total ?></b> en catálogo</span>
        <span class="sep"></span>
        <span><b data-contar="<?= $disponibles ?>"><?= $disponibles ?></b> disponibles</span>
    </p>
</section>

<section class="catalogo">
    <div class="herramientas glass reveal">
        <label class="buscador">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4-4"/></svg>
            <input type="search" id="buscar" placeholder="Buscar por nombre o marca…" autocomplete="off">
        </label>

        <div class="filtros" role="group" aria-label="Filtrar por disponibilidad">
            <button class="chip activo" data-filtro="todos">Todos</button>
            <button class="chip" data-filtro="disponible">Disponibles</button>
            <button class="chip" data-filtro="agotado">No disponibles</button>
        </div>
    </div>

    <?php if ($total === 0): ?>
        <p class="vacio glass">Aún no hay perfumes en esta sección. Vuelve pronto.</p>
    <?php else: ?>
        <div class="grid" id="grid">
            <?php foreach ($perfumes as $p) { echo tarjeta_perfume($p); } ?>
        </div>
        <p class="sin-resultados glass" id="sin-resultados" hidden>No encontramos perfumes con esa búsqueda.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
