<?php
/**
 * Funciones de ayuda y la tarjeta de perfume que se repite en el catálogo.
 */
require_once __DIR__ . '/db.php';

/** Escapa texto para HTML. */
function h(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** $2,450 (sin decimales si es entero). */
function formato_precio(float $precio): string
{
    $decimales = (floor($precio) == $precio) ? 0 : 2;
    return '$' . number_format($precio, $decimales, '.', ',');
}

/** Imagen del perfume, o una botella genérica según la categoría. */
function imagen_perfume(array $p): string
{
    $ruta = trim((string) ($p['imagen'] ?? ''));
    if ($ruta !== '' && is_file(__DIR__ . '/../' . $ruta)) {
        return $ruta;
    }
    $defecto = [
        'caballero' => 'caballero-1.svg',
        'dama'      => 'dama-1.svg',
        'decant'    => 'decant-1.svg',
    ];
    return IMG_DIR_REL . ($defecto[$p['categoria']] ?? 'caballero-1.svg');
}

/** Tarjeta de un perfume con efecto liquid glass. */
function tarjeta_perfume(array $p): string
{
    $disp   = (int) $p['disponible'] === 1;
    $clase  = $disp ? 'disponible' : 'agotado';
    $estado = $disp ? 'Disponible' : 'No disponible';

    ob_start(); ?>
    <article class="card glass reveal <?= $clase ?>" data-estado="<?= $clase ?>"
             data-nombre="<?= h(mb_strtolower($p['nombre'] . ' ' . $p['marca'])) ?>">
        <div class="card-img">
            <img src="<?= h(imagen_perfume($p)) ?>" alt="<?= h($p['marca'] . ' ' . $p['nombre']) ?>" loading="lazy">
            <span class="badge <?= $clase ?>"><i></i><?= $estado ?></span>
        </div>
        <div class="card-body">
            <p class="card-marca"><?= h($p['marca']) ?></p>
            <h3 class="card-nombre"><?= h($p['nombre']) ?></h3>
            <?php if (trim((string) $p['tamano']) !== ''): ?>
                <p class="card-tamano"><?= h($p['tamano']) ?></p>
            <?php endif; ?>
            <p class="card-precio" data-precio="<?= (float) $p['precio'] ?>">
                <?= h(formato_precio((float) $p['precio'])) ?> <small><?= h(MONEDA) ?></small>
            </p>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/** Íconos de redes (SVG en línea). */
function icono_facebook(): string
{
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.2H8v3h2.4V21h3.1z"/></svg>';
}

function icono_instagram(): string
{
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2zm-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6zm9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>';
}
