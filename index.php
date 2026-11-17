<?php
require_once __DIR__ . '/includes/funciones.php';

$pagina = 'inicio';
$titulo = 'The Fragrance Obsession by Milos — Reventa de perfumes de marca';

// Cifras reales desde la base de datos
$pdo = db();
$totales = [];
foreach (categorias() as $clave => $nombre) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM perfumes WHERE categoria = :c');
    $stmt->execute([':c' => $clave]);
    $totales[$clave] = (int) $stmt->fetchColumn();
}
$disponibles = (int) $pdo->query('SELECT COUNT(*) FROM perfumes WHERE disponible = 1')->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<!-- ───────── Portada ───────── -->
<section class="hero">
    <div class="hero-texto">
        <p class="kicker reveal">Venta de perfumes de marca</p>
        <h1 class="reveal">
            <span class="linea">The Fragrance</span>
            <span class="script grande">Obsession</span>
            <span class="by">by Milos</span>
        </h1>
        <p class="lead reveal">
            Una tienda para quienes viven la fragancia como pasión: perfumes de marca para caballero,
            para dama y decants para probar antes de decidir.
        </p>
        <div class="acciones reveal">
            <a class="btn primario" href="caballero.php">Ver catálogo</a>
            <a class="btn" href="decants.php">Probar con decants</a>
        </div>
    </div>

    <div class="hero-visual reveal">
        <div class="marco glass" data-tilt>
            <img src="assets/img/banner.png" alt="Frasco negro con tapa cobre — The Fragrance Obsession by Milos">
        </div>
        <span class="orbita o1" aria-hidden="true"></span>
        <span class="orbita o2" aria-hidden="true"></span>
    </div>
</section>

<!-- ───────── Quiénes somos ───────── -->
<section class="seccion" id="quienes-somos">
    <div class="titulo-seccion reveal">
        <p class="kicker">Quiénes somos</p>
        <h2>Pasión por el <span class="script">aroma</span></h2>
    </div>

    <div class="quienes glass reveal">
        <p>
            Somos <b>The Fragrance Obsession by Milos</b>, una tienda dedicada a la reventa de perfumes de marca.
            Nuestra idea es simple: que encuentres esa fragancia que te define, ya sea en frasco completo o en
            decant para probarla con calma.
        </p>
        <p>
            Cada perfume de nuestro catálogo muestra su precio y si está disponible, para que sepas qué
            tenemos hoy. Síguenos en redes para enterarte de las novedades y escríbenos para apartar el tuyo.
        </p>
    </div>

    <div class="tres">
        <article class="glass reveal" data-tilt>
            <span class="num">01</span>
            <h3>Perfumes de marca</h3>
            <p>Fragancias reconocidas para caballero y para dama, en presentaciones completas.</p>
        </article>
        <article class="glass reveal" data-tilt>
            <span class="num">02</span>
            <h3>Decants para probar</h3>
            <p>Porciones pequeñas para conocer un perfume antes de comprar el frasco completo.</p>
        </article>
        <article class="glass reveal" data-tilt>
            <span class="num">03</span>
            <h3>Catálogo al día</h3>
            <p>Precios y disponibilidad actualizados, para que veas al instante qué hay en existencia.</p>
        </article>
    </div>
</section>

<!-- ───────── Cifras (vienen de la base de datos) ───────── -->
<section class="seccion">
    <div class="cifras glass reveal">
        <div><b data-contar="<?= $totales['caballero'] ?>"><?= $totales['caballero'] ?></b><span>Caballero</span></div>
        <div><b data-contar="<?= $totales['dama'] ?>"><?= $totales['dama'] ?></b><span>Dama</span></div>
        <div><b data-contar="<?= $totales['decant'] ?>"><?= $totales['decant'] ?></b><span>Decants</span></div>
        <div><b data-contar="<?= $disponibles ?>"><?= $disponibles ?></b><span>Disponibles hoy</span></div>
    </div>
</section>

<!-- ───────── Accesos al catálogo ───────── -->
<section class="seccion">
    <div class="titulo-seccion reveal">
        <p class="kicker">Explora</p>
        <h2>Elige tu <span class="script">colección</span></h2>
    </div>

    <div class="tres accesos">
        <a class="acceso glass reveal" href="caballero.php" data-tilt>
            <h3>Caballero</h3>
            <p>Frescos, amaderados y con carácter.</p>
            <span class="flecha">Ver perfumes →</span>
        </a>
        <a class="acceso glass reveal" href="dama.php" data-tilt>
            <h3>Dama</h3>
            <p>Florales, dulces y elegantes.</p>
            <span class="flecha">Ver perfumes →</span>
        </a>
        <a class="acceso glass reveal" href="decants.php" data-tilt>
            <h3>Decants</h3>
            <p>Prueba antes de decidir.</p>
            <span class="flecha">Ver decants →</span>
        </a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
