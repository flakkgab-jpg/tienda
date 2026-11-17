<?php
/**
 * Conexión a la base de datos (PDO) + creación automática de la tabla.
 * La primera vez que se abre el sitio crea la tabla `perfumes` y la llena
 * con los perfumes de ejemplo de includes/seed.php.
 */
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    if (DB_DRIVER === 'mysql') {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } else {
        $carpeta = dirname(DB_SQLITE_PATH);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }
        $pdo = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, $opciones);
        $pdo->exec('PRAGMA journal_mode = WAL');
    }

    crear_tabla($pdo);
    sembrar_si_vacia($pdo);
    return $pdo;
}

function crear_tabla(PDO $pdo): void
{
    if (DB_DRIVER === 'mysql') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS perfumes (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            nombre      VARCHAR(150) NOT NULL,
            marca       VARCHAR(100) NOT NULL DEFAULT '',
            categoria   VARCHAR(20)  NOT NULL,
            tamano      VARCHAR(30)  NOT NULL DEFAULT '',
            precio      DECIMAL(10,2) NOT NULL DEFAULT 0,
            disponible  TINYINT(1)   NOT NULL DEFAULT 1,
            imagen      VARCHAR(255) NOT NULL DEFAULT '',
            creado      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_categoria (categoria)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } else {
        $pdo->exec("CREATE TABLE IF NOT EXISTS perfumes (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre      TEXT NOT NULL,
            marca       TEXT NOT NULL DEFAULT '',
            categoria   TEXT NOT NULL,
            tamano      TEXT NOT NULL DEFAULT '',
            precio      REAL NOT NULL DEFAULT 0,
            disponible  INTEGER NOT NULL DEFAULT 1,
            imagen      TEXT NOT NULL DEFAULT '',
            creado      TEXT DEFAULT CURRENT_TIMESTAMP
        )");
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_categoria ON perfumes (categoria)');
    }
}

function sembrar_si_vacia(PDO $pdo): void
{
    $total = (int) $pdo->query('SELECT COUNT(*) FROM perfumes')->fetchColumn();
    if ($total > 0) {
        return;
    }
    $datos = require __DIR__ . '/seed.php';
    $stmt = $pdo->prepare(
        'INSERT INTO perfumes (nombre, marca, categoria, tamano, precio, disponible, imagen)
         VALUES (:nombre, :marca, :categoria, :tamano, :precio, :disponible, :imagen)'
    );
    $pdo->beginTransaction();
    foreach ($datos as $p) {
        $stmt->execute($p);
    }
    $pdo->commit();
}

/** Categorías válidas del catálogo. */
function categorias(): array
{
    return [
        'caballero' => 'Caballero',
        'dama'      => 'Dama',
        'decant'    => 'Decants',
    ];
}

/** Perfumes de una categoría, en el orden en que se agregaron. */
function perfumes_por_categoria(string $categoria): array
{
    $stmt = db()->prepare('SELECT * FROM perfumes WHERE categoria = :c ORDER BY id ASC');
    $stmt->execute([':c' => $categoria]);
    return $stmt->fetchAll();
}
