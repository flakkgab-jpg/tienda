<?php
/**
 * CONFIGURACIÓN GENERAL — The Fragrance Obsession by Milos
 * Aquí cambias los links de redes, la contraseña del panel y la base de datos.
 */

// ───────── Datos de la tienda ─────────
define('SITE_NOMBRE', 'The Fragrance Obsession');
define('SITE_FIRMA',  'by Milos');
define('MONEDA',      'MXN');

// ───────── Redes sociales (pon aquí tus links) ─────────
define('FACEBOOK_URL',  'https://www.facebook.com/people/The-Fragrance-Obsession/61561934575344/');
define('INSTAGRAM_URL', 'https://www.instagram.com/the_fragrance_obsession_?utm_source=ig_web_button_share_sheet&rpxt=ZDNlZDc0MzIxNw==');

// ───────── Panel de administración (admin.php) ─────────
// ¡CÁMBIALA antes de subir el sitio a internet!
define('ADMIN_PASSWORD', 'RelsB');

// ───────── Base de datos ─────────
// 'sqlite' → no necesita instalar nada: se crea sola en /data/tienda.sqlite (recomendado para empezar).
// 'mysql'  → si tu hosting te da MySQL, cambia DB_DRIVER y llena los datos de abajo.
define('DB_DRIVER', 'sqlite');

define('DB_SQLITE_PATH', __DIR__ . '/../data/tienda.sqlite');

define('DB_HOST', 'localhost');
define('DB_NAME', 'fragrance_obsession');
define('DB_USER', 'root');
define('DB_PASS', '');

// ───────── Imágenes de perfumes ─────────
define('IMG_DIR_REL', 'assets/img/perfumes/');           // carpeta (relativa al sitio) donde se guardan las fotos
define('IMG_MAX_BYTES', 4 * 1024 * 1024);                // máx. 4 MB por foto

date_default_timezone_set('America/Mexico_City');
