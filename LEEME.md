# The Fragrance Obsession by Milos — sitio web

## Páginas
| Archivo | Sección |
|---|---|
| `index.php` | Quiénes somos (principal) |
| `caballero.php` | Perfumes de caballero |
| `dama.php` | Perfumes de dama |
| `decants.php` | Decants |
| `admin.php` | Panel para administrar el catálogo |

## Lo primero que debes cambiar — `includes/config.php`
- `FACEBOOK_URL` y `INSTAGRAM_URL`: pon aquí tus links (los íconos ya están puestos).
- `ADMIN_PASSWORD`: la contraseña del panel. **Cámbiala antes de publicar.**

## Probarlo en tu computadora
Necesitas PHP 8 o superior (con la extensión `pdo_sqlite`, que XAMPP ya trae).

- Con XAMPP: copia la carpeta a `htdocs` y abre `http://localhost/tienda/`.
- Con PHP suelto: dentro de la carpeta ejecuta `php -S localhost:8000` y abre `http://localhost:8000`.

La primera vez que se abre el sitio se crea sola la base de datos (`data/tienda.sqlite`) con 8 perfumes de ejemplo por sección.

## Administrar perfumes, precios y disponibilidad
Entra a `/admin.php`. Ahí puedes:
- **Agregar** un perfume (nombre, marca, sección, tamaño, precio, foto, disponible).
- **Cambiar el precio** directo en la tabla.
- **Marcar disponible / no disponible** con un clic (en el sitio la foto se ve en gris).
- **Editar** o **eliminar** un perfume.

Los cambios se ven en el sitio con solo recargar la página.

## Fotos de los perfumes
Los perfumes de ejemplo usan botellas genéricas (`assets/img/perfumes/*.svg`). Para poner la foto real, edita el perfume en el panel y sube un JPG, PNG o WEBP (máx. 4 MB). Se ve mejor con la botella centrada y fondo transparente o claro.

## Subirlo a un hosting
1. Sube todo el contenido de la carpeta (PHP 8+ requerido).
2. Dale permisos de escritura a las carpetas `data/` y `assets/img/perfumes/`.
3. Las carpetas `data/` e `includes/` traen un `.htaccess` que bloquea el acceso directo (funciona en Apache; en Nginx hay que bloquearlas en la configuración).

## Usar MySQL en vez de SQLite (opcional)
En `includes/config.php` cambia `DB_DRIVER` a `'mysql'` y llena `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`. La tabla `perfumes` se crea sola; solo debes crear antes la base de datos vacía.

## Estructura de la tabla `perfumes`
`id`, `nombre`, `marca`, `categoria` (`caballero`, `dama` o `decant`), `tamano`, `precio`, `disponible` (1 = sí, 0 = no), `imagen`.
