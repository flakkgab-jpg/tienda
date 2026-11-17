<?php
/**
 * Panel para administrar el catálogo: agregar, editar, cambiar precio,
 * marcar disponible / no disponible y eliminar perfumes.
 * Los cambios se ven al instante en el sitio (solo recarga la página).
 *
 * La contraseña se cambia en includes/config.php → ADMIN_PASSWORD
 */
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);
require_once __DIR__ . '/includes/funciones.php';

/* ───────── Utilidades ───────── */
function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valido(): bool
{
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', (string) $_POST['csrf']);
}

function aviso(string $texto, string $tipo = 'ok'): void
{
    $_SESSION['aviso'] = [$texto, $tipo];
}

function volver(string $url = 'admin.php'): never
{
    header('Location: ' . $url);
    exit;
}

/** Guarda una foto subida. Devuelve ['ok' => ruta] o ['error' => mensaje]. */
function subir_imagen(array $f): array
{
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No se pudo subir la imagen (código ' . (int) $f['error'] . ').'];
    }
    if ($f['size'] > IMG_MAX_BYTES) {
        return ['error' => 'La imagen pesa más de ' . (IMG_MAX_BYTES / 1048576) . ' MB.'];
    }
    $info = @getimagesize($f['tmp_name']);
    if (!$info) {
        return ['error' => 'El archivo no es una imagen válida.'];
    }
    $permitidos = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!isset($permitidos[$info[2]])) {
        return ['error' => 'Usa una imagen JPG, PNG o WEBP.'];
    }
    $dir = __DIR__ . '/' . IMG_DIR_REL;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $archivo = 'p-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $permitidos[$info[2]];
    if (!move_uploaded_file($f['tmp_name'], $dir . $archivo)) {
        return ['error' => 'No se pudo guardar la imagen (revisa los permisos de la carpeta ' . IMG_DIR_REL . ').'];
    }
    return ['ok' => IMG_DIR_REL . $archivo];
}

$autenticado = !empty($_SESSION['admin']);

/* ───────── Acciones (POST) ───────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        aviso('La sesión expiró. Intenta de nuevo.', 'error');
        volver();
    }
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'login') {
        if (hash_equals(ADMIN_PASSWORD, (string) ($_POST['clave'] ?? ''))) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            volver();
        }
        sleep(1);
        aviso('Contraseña incorrecta.', 'error');
        volver();
    }

    if (!$autenticado) {
        volver();
    }

    $pdo = db();

    switch ($accion) {
        case 'salir':
            $_SESSION = [];
            session_destroy();
            volver();

        case 'alternar':
            $pdo->prepare('UPDATE perfumes SET disponible = 1 - disponible WHERE id = :id')
                ->execute([':id' => (int) ($_POST['id'] ?? 0)]);
            aviso('Disponibilidad actualizada.');
            volver();

        case 'precio':
            $precio = (float) str_replace(',', '', (string) ($_POST['precio'] ?? ''));
            if ($precio < 0) {
                aviso('El precio no puede ser negativo.', 'error');
                volver();
            }
            $pdo->prepare('UPDATE perfumes SET precio = :p WHERE id = :id')
                ->execute([':p' => $precio, ':id' => (int) ($_POST['id'] ?? 0)]);
            aviso('Precio actualizado.');
            volver();

        case 'eliminar':
            $pdo->prepare('DELETE FROM perfumes WHERE id = :id')
                ->execute([':id' => (int) ($_POST['id'] ?? 0)]);
            aviso('Perfume eliminado.');
            volver();

        case 'guardar':
            $id        = (int) ($_POST['id'] ?? 0);
            $nombre    = trim((string) ($_POST['nombre'] ?? ''));
            $marca     = trim((string) ($_POST['marca'] ?? ''));
            $categoria = (string) ($_POST['categoria'] ?? '');
            $tamano    = trim((string) ($_POST['tamano'] ?? ''));
            $precio    = (float) str_replace(',', '', (string) ($_POST['precio'] ?? '0'));
            $disp      = isset($_POST['disponible']) ? 1 : 0;

            $retorno = $id > 0 ? 'admin.php?editar=' . $id : 'admin.php';

            if ($nombre === '' || mb_strlen($nombre) > 150 || mb_strlen($marca) > 100 || mb_strlen($tamano) > 30) {
                aviso('Revisa el nombre (obligatorio), la marca y el tamaño.', 'error');
                volver($retorno);
            }
            if (!array_key_exists($categoria, categorias())) {
                aviso('Elige una categoría válida.', 'error');
                volver($retorno);
            }
            if ($precio < 0) {
                aviso('El precio no puede ser negativo.', 'error');
                volver($retorno);
            }

            $imagen = null; // null = conservar la actual
            if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $res = subir_imagen($_FILES['imagen']);
                if (isset($res['error'])) {
                    aviso($res['error'], 'error');
                    volver($retorno);
                }
                $imagen = $res['ok'];
            }

            if ($id > 0) {
                $pdo->prepare(
                    'UPDATE perfumes
                        SET nombre = :n, marca = :m, categoria = :c, tamano = :t,
                            precio = :p, disponible = :d, imagen = COALESCE(:i, imagen)
                      WHERE id = :id'
                )->execute([
                    ':n' => $nombre, ':m' => $marca, ':c' => $categoria, ':t' => $tamano,
                    ':p' => $precio, ':d' => $disp, ':i' => $imagen, ':id' => $id,
                ]);
                aviso('Perfume actualizado.');
            } else {
                $pdo->prepare(
                    'INSERT INTO perfumes (nombre, marca, categoria, tamano, precio, disponible, imagen)
                     VALUES (:n, :m, :c, :t, :p, :d, :i)'
                )->execute([
                    ':n' => $nombre, ':m' => $marca, ':c' => $categoria, ':t' => $tamano,
                    ':p' => $precio, ':d' => $disp, ':i' => $imagen ?? '',
                ]);
                aviso('Perfume agregado.');
            }
            volver();
    }
    volver();
}

/* ───────── Datos para mostrar ───────── */
$aviso = $_SESSION['aviso'] ?? null;
unset($_SESSION['aviso']);

$porCategoria = [];
$editar = null;
if ($autenticado) {
    $pdo = db();
    foreach ($pdo->query('SELECT * FROM perfumes ORDER BY id ASC')->fetchAll() as $p) {
        $porCategoria[$p['categoria']][] = $p;
    }
    if (isset($_GET['editar'])) {
        $stmt = $pdo->prepare('SELECT * FROM perfumes WHERE id = :id');
        $stmt->execute([':id' => (int) $_GET['editar']]);
        $editar = $stmt->fetch() ?: null;
    }
}
$claveSinCambiar = ADMIN_PASSWORD === 'cambia-esta-clave';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Panel — <?= h(SITE_NOMBRE) ?></title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Yellowtail&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
    <style>
        .adm { max-width: 1100px; margin: 0 auto; padding: 24px 16px 60px; }
        .adm-top { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 12px 18px; margin-bottom: 22px; border-radius: 999px; }
        .adm-top .marca-texto em { font-size: 1.1rem; }
        .adm-top .acc { display: flex; gap: 10px; align-items: center; }
        .adm h2 { font-weight: 400; font-size: 1.5rem; margin-bottom: 14px; }
        .adm h2 .script { font-size: 1.3em; }
        .panel { padding: 24px; margin-bottom: 24px; }
        .aviso { padding: 12px 18px; border-radius: 16px; margin-bottom: 18px; border: 1px solid rgba(255,255,255,.2); }
        .aviso.ok { background: rgba(91,227,160,.14); border-color: rgba(91,227,160,.4); }
        .aviso.error { background: rgba(255,107,107,.14); border-color: rgba(255,107,107,.45); }
        .aviso.warn { background: rgba(224,166,121,.14); border-color: rgba(224,166,121,.45); }
        .campos { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; }
        .campos label { display: flex; flex-direction: column; gap: 6px; font-size: .82rem; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .campos input[type=text], .campos input[type=number], .campos input[type=password], .campos select, .campos input[type=file] {
            padding: 11px 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,.18);
            background: rgba(0,0,0,.28); color: var(--fg); font: inherit; font-size: .95rem; text-transform: none; letter-spacing: 0; outline: none;
        }
        .campos select option { background: #1a1a1e; }
        .campos input:focus, .campos select:focus { border-color: var(--cobre); box-shadow: 0 0 0 4px rgba(224,166,121,.18); }
        .check { flex-direction: row !important; align-items: center; gap: 10px !important; text-transform: none !important; letter-spacing: 0 !important; font-size: .95rem !important; color: var(--fg) !important; padding-top: 24px; }
        .check input { width: 20px; height: 20px; accent-color: var(--cobre); }
        .fila-btns { display: flex; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
        .btn { cursor: pointer; font: inherit; color: var(--fg); }
        .btn.primario { color: #1a1109; }
        .tabla-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th { text-align: left; font-weight: 500; font-size: .74rem; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); padding: 8px 10px; }
        td { padding: 10px; border-top: 1px solid rgba(255,255,255,.09); vertical-align: middle; }
        td img { width: 52px; height: 52px; object-fit: contain; border-radius: 10px; background: rgba(255,255,255,.08); }
        tr.agotado td img { filter: grayscale(1); opacity: .5; }
        td small { color: var(--muted); display: block; }
        .precio-form { display: flex; gap: 6px; align-items: center; }
        .precio-form input { width: 96px; padding: 7px 10px; border-radius: 10px; border: 1px solid rgba(255,255,255,.18); background: rgba(0,0,0,.28); color: var(--fg); font: inherit; }
        .mini { padding: 7px 14px; border-radius: 999px; border: 1px solid rgba(255,255,255,.2); background: rgba(255,255,255,.08); font-size: .82rem; cursor: pointer; display: inline-block; color: var(--fg); transition: background .2s; }
        .mini:hover { background: rgba(255,255,255,.16); }
        .mini.peligro:hover { background: rgba(255,107,107,.35); }
        .estado { font-weight: 500; }
        .estado.on { background: rgba(91,227,160,.18); border-color: rgba(91,227,160,.5); }
        .estado.off { background: rgba(139,139,146,.2); color: #cfcfd4; }
        .acciones-td { display: flex; gap: 6px; flex-wrap: wrap; }
        .login { max-width: 420px; margin: 12vh auto 0; text-align: center; padding: 34px 28px; }
        .login img { width: 90px; height: 90px; border-radius: 50%; margin: 0 auto 16px; }
        .login .campos { margin-top: 18px; }
    </style>
</head>
<body>
<div class="fondo" aria-hidden="true"><span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span></div>

<div class="adm">

<?php if (!$autenticado): ?>

    <form class="login glass" method="post" action="admin.php">
        <img src="assets/img/logo.png" alt="">
        <h2>Panel de <span class="script">administración</span></h2>
        <?php if ($aviso): ?><p class="aviso <?= h($aviso[1]) ?>"><?= h($aviso[0]) ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
        <input type="hidden" name="accion" value="login">
        <div class="campos">
            <label>Contraseña
                <input type="password" name="clave" autocomplete="current-password" autofocus required>
            </label>
        </div>
        <div class="fila-btns" style="justify-content:center">
            <button class="btn primario" type="submit">Entrar</button>
            <a class="btn" href="index.php">Ver sitio</a>
        </div>
    </form>

<?php else: ?>

    <div class="adm-top glass">
        <div class="marca">
            <img src="assets/img/logo.png" alt="" width="44" height="44">
            <span class="marca-texto"><b>Panel de catálogo</b><em><?= h(SITE_NOMBRE) ?></em></span>
        </div>
        <div class="acc">
            <a class="mini" href="index.php" target="_blank" rel="noopener">Ver sitio ↗</a>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
                <input type="hidden" name="accion" value="salir">
                <button class="mini" type="submit">Salir</button>
            </form>
        </div>
    </div>

    <?php if ($claveSinCambiar): ?>
        <p class="aviso warn">Sigues usando la contraseña de ejemplo. Cámbiala en <b>includes/config.php</b> (ADMIN_PASSWORD) antes de publicar el sitio.</p>
    <?php endif; ?>
    <?php if ($aviso): ?><p class="aviso <?= h($aviso[1]) ?>"><?= h($aviso[0]) ?></p><?php endif; ?>

    <!-- ───────── Formulario agregar / editar ───────── -->
    <form class="panel glass" method="post" enctype="multipart/form-data" id="form-perfume">
        <h2><?= $editar ? 'Editar <span class="script">perfume</span>' : 'Agregar <span class="script">perfume</span>' ?></h2>
        <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
        <input type="hidden" name="accion" value="guardar">
        <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

        <div class="campos">
            <label>Nombre
                <input type="text" name="nombre" maxlength="150" required value="<?= h($editar['nombre'] ?? '') ?>" placeholder="Ej. Sauvage">
            </label>
            <label>Marca
                <input type="text" name="marca" maxlength="100" value="<?= h($editar['marca'] ?? '') ?>" placeholder="Ej. Dior">
            </label>
            <label>Sección
                <select name="categoria" required>
                    <?php foreach (categorias() as $clave => $nombreCat): ?>
                        <option value="<?= h($clave) ?>" <?= (($editar['categoria'] ?? 'caballero') === $clave) ? 'selected' : '' ?>><?= h($nombreCat) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Tamaño
                <input type="text" name="tamano" maxlength="30" value="<?= h($editar['tamano'] ?? '') ?>" placeholder="Ej. 100 ml">
            </label>
            <label>Precio (<?= h(MONEDA) ?>)
                <input type="number" name="precio" min="0" step="0.01" required value="<?= h((string) ($editar['precio'] ?? '')) ?>" placeholder="0">
            </label>
            <label>Foto (JPG, PNG o WEBP)
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
            </label>
            <label class="check">
                <input type="checkbox" name="disponible" value="1" <?= (!$editar || (int) $editar['disponible'] === 1) ? 'checked' : '' ?>>
                Disponible
            </label>
        </div>

        <?php if ($editar): ?>
            <p style="margin-top:14px;color:var(--muted);font-size:.88rem">Si no eliges una foto nueva se conserva la actual.</p>
        <?php endif; ?>

        <div class="fila-btns">
            <button class="btn primario" type="submit"><?= $editar ? 'Guardar cambios' : 'Agregar perfume' ?></button>
            <?php if ($editar): ?><a class="btn" href="admin.php">Cancelar</a><?php endif; ?>
        </div>
    </form>

    <!-- ───────── Listado por sección ───────── -->
    <?php foreach (categorias() as $clave => $nombreCat): $lista = $porCategoria[$clave] ?? []; ?>
        <section class="panel glass">
            <h2><?= h($nombreCat) ?> <span class="script"><?= count($lista) ?></span></h2>

            <?php if (!$lista): ?>
                <p style="color:var(--muted)">No hay perfumes en esta sección todavía.</p>
            <?php else: ?>
            <div class="tabla-wrap">
                <table>
                    <thead>
                        <tr><th></th><th>Perfume</th><th>Tamaño</th><th>Precio</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lista as $p): $on = (int) $p['disponible'] === 1; ?>
                        <tr class="<?= $on ? 'disponible' : 'agotado' ?>">
                            <td><img src="<?= h(imagen_perfume($p)) ?>" alt=""></td>
                            <td><?= h($p['nombre']) ?><small><?= h($p['marca']) ?></small></td>
                            <td><?= h($p['tamano']) ?></td>
                            <td>
                                <form class="precio-form" method="post">
                                    <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
                                    <input type="hidden" name="accion" value="precio">
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <input type="number" name="precio" min="0" step="0.01" value="<?= h((string) (float) $p['precio']) ?>" aria-label="Precio de <?= h($p['nombre']) ?>">
                                    <button class="mini" type="submit">Guardar</button>
                                </form>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
                                    <input type="hidden" name="accion" value="alternar">
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button class="mini estado <?= $on ? 'on' : 'off' ?>" type="submit" title="Clic para cambiar">
                                        <?= $on ? 'Disponible' : 'No disponible' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="acciones-td">
                                    <a class="mini" href="admin.php?editar=<?= (int) $p['id'] ?>#form-perfume">Editar</a>
                                    <form method="post" onsubmit="return confirm('¿Eliminar «<?= h(addslashes($p['nombre'])) ?>»? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <button class="mini peligro" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>

<?php endif; ?>

</div>
</body>
</html>
