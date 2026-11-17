<?php
/**
 * Encabezado común. Cada página define antes:
 *   $pagina  → 'inicio' | 'caballero' | 'dama' | 'decant'
 *   $titulo  → título de la pestaña
 */
require_once __DIR__ . '/funciones.php';

$pagina = $pagina ?? 'inicio';
$titulo = $titulo ?? SITE_NOMBRE . ' ' . SITE_FIRMA;

$menu = [
    'inicio'    => ['index.php',     'Quiénes somos'],
    'caballero' => ['caballero.php', 'Caballero'],
    'dama'      => ['dama.php',      'Dama'],
    'decant'    => ['decants.php',   'Decants'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?></title>
    <meta name="description" content="<?= h(SITE_NOMBRE . ' ' . SITE_FIRMA) ?> — reventa de perfumes de marca para caballero, dama y decants.">
    <meta name="theme-color" content="#0b0b0d">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Yellowtail&display=swap" rel="stylesheet">
    <script>document.documentElement.classList.add('js');</script>
    <link rel="stylesheet" href="assets/css/style.css?v=1">
</head>
<body data-pagina="<?= h($pagina) ?>">

<!-- Fondo líquido animado (detrás del cristal) -->
<div class="fondo" aria-hidden="true">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
</div>
<div class="cursor-glow" aria-hidden="true"></div>

<header class="topbar">
    <nav class="nav glass" aria-label="Principal">
        <a class="marca" href="index.php" aria-label="<?= h(SITE_NOMBRE) ?> — inicio">
            <img src="assets/img/logo.png" alt="Logo <?= h(SITE_NOMBRE) ?>" width="44" height="44">
            <span class="marca-texto">
                <b><?= h(SITE_NOMBRE) ?></b>
                <em><?= h(SITE_FIRMA) ?></em>
            </span>
        </a>

        <button class="menu-btn" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
            <span></span><span></span><span></span>
        </button>

        <ul class="menu" id="menu">
            <?php foreach ($menu as $clave => [$url, $texto]): ?>
                <li>
                    <a href="<?= h($url) ?>" class="<?= $clave === $pagina ? 'activo' : '' ?>"
                       <?= $clave === $pagina ? 'aria-current="page"' : '' ?>><?= h($texto) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="redes">
            <a href="<?= h(FACEBOOK_URL) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icono_facebook() ?></a>
            <a href="<?= h(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icono_instagram() ?></a>
        </div>
    </nav>
</header>

<main>
