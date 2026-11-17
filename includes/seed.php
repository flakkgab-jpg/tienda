<?php
/**
 * Perfumes de ejemplo (8 caballero, 8 dama, 8 decants).
 * Se cargan UNA sola vez, cuando la tabla está vacía.
 * Después ya los administras desde admin.php (o directo en la base de datos).
 *
 * disponible: 1 = disponible, 0 = no disponible
 * imagen: ruta relativa al sitio; si la dejas vacía se usa una botella genérica.
 */
$img = 'assets/img/perfumes/';

return [
    // ───────── CABALLERO ─────────
    ['nombre' => 'Sauvage',                 'marca' => 'Dior',               'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 2450, 'disponible' => 1, 'imagen' => $img . 'caballero-1.svg'],
    ['nombre' => 'Bleu de Chanel',          'marca' => 'Chanel',             'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 2890, 'disponible' => 1, 'imagen' => $img . 'caballero-2.svg'],
    ['nombre' => '1 Million',               'marca' => 'Paco Rabanne',       'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 1890, 'disponible' => 0, 'imagen' => $img . 'caballero-1.svg'],
    ['nombre' => 'Acqua di Giò',            'marca' => 'Giorgio Armani',     'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 1950, 'disponible' => 1, 'imagen' => $img . 'caballero-2.svg'],
    ['nombre' => 'Eros',                    'marca' => 'Versace',            'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 1690, 'disponible' => 1, 'imagen' => $img . 'caballero-3.svg'],
    ['nombre' => 'Bad Boy',                 'marca' => 'Carolina Herrera',   'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 2150, 'disponible' => 0, 'imagen' => $img . 'caballero-3.svg'],
    ['nombre' => 'Le Male',                 'marca' => 'Jean Paul Gaultier', 'categoria' => 'caballero', 'tamano' => '125 ml', 'precio' => 1790, 'disponible' => 1, 'imagen' => $img . 'caballero-1.svg'],
    ['nombre' => 'Y Eau de Parfum',         'marca' => 'Yves Saint Laurent', 'categoria' => 'caballero', 'tamano' => '100 ml', 'precio' => 2390, 'disponible' => 1, 'imagen' => $img . 'caballero-2.svg'],

    // ───────── DAMA ─────────
    ['nombre' => 'Coco Mademoiselle',       'marca' => 'Chanel',             'categoria' => 'dama', 'tamano' => '100 ml', 'precio' => 3150, 'disponible' => 1, 'imagen' => $img . 'dama-1.svg'],
    ['nombre' => "J'adore",                 'marca' => 'Dior',               'categoria' => 'dama', 'tamano' => '100 ml', 'precio' => 2790, 'disponible' => 1, 'imagen' => $img . 'dama-2.svg'],
    ['nombre' => 'Good Girl',               'marca' => 'Carolina Herrera',   'categoria' => 'dama', 'tamano' => '80 ml',  'precio' => 2350, 'disponible' => 0, 'imagen' => $img . 'dama-1.svg'],
    ['nombre' => 'Libre',                   'marca' => 'Yves Saint Laurent', 'categoria' => 'dama', 'tamano' => '90 ml',  'precio' => 2690, 'disponible' => 1, 'imagen' => $img . 'dama-2.svg'],
    ['nombre' => 'La Vie Est Belle',        'marca' => 'Lancôme',            'categoria' => 'dama', 'tamano' => '100 ml', 'precio' => 2590, 'disponible' => 1, 'imagen' => $img . 'dama-3.svg'],
    ['nombre' => 'Bright Crystal',          'marca' => 'Versace',            'categoria' => 'dama', 'tamano' => '90 ml',  'precio' => 1590, 'disponible' => 1, 'imagen' => $img . 'dama-3.svg'],
    ['nombre' => 'Olympéa',                 'marca' => 'Paco Rabanne',       'categoria' => 'dama', 'tamano' => '80 ml',  'precio' => 1990, 'disponible' => 0, 'imagen' => $img . 'dama-1.svg'],
    ['nombre' => 'Born in Roma Donna',      'marca' => 'Valentino',          'categoria' => 'dama', 'tamano' => '100 ml', 'precio' => 2650, 'disponible' => 1, 'imagen' => $img . 'dama-2.svg'],

    // ───────── DECANTS ─────────
    ['nombre' => 'Aventus',                 'marca' => 'Creed',              'categoria' => 'decant', 'tamano' => '5 ml',  'precio' => 380, 'disponible' => 1, 'imagen' => $img . 'decant-1.svg'],
    ['nombre' => 'Baccarat Rouge 540',      'marca' => 'Maison Francis Kurkdjian', 'categoria' => 'decant', 'tamano' => '5 ml', 'precio' => 420, 'disponible' => 1, 'imagen' => $img . 'decant-2.svg'],
    ['nombre' => 'Tobacco Vanille',         'marca' => 'Tom Ford',           'categoria' => 'decant', 'tamano' => '5 ml',  'precio' => 360, 'disponible' => 0, 'imagen' => $img . 'decant-1.svg'],
    ['nombre' => 'Layton',                  'marca' => 'Parfums de Marly',   'categoria' => 'decant', 'tamano' => '10 ml', 'precio' => 520, 'disponible' => 1, 'imagen' => $img . 'decant-2.svg'],
    ['nombre' => 'Sauvage Elixir',          'marca' => 'Dior',               'categoria' => 'decant', 'tamano' => '5 ml',  'precio' => 290, 'disponible' => 1, 'imagen' => $img . 'decant-1.svg'],
    ['nombre' => 'Oud Wood',                'marca' => 'Tom Ford',           'categoria' => 'decant', 'tamano' => '10 ml', 'precio' => 560, 'disponible' => 1, 'imagen' => $img . 'decant-2.svg'],
    ['nombre' => 'Delina',                  'marca' => 'Parfums de Marly',   'categoria' => 'decant', 'tamano' => '5 ml',  'precio' => 340, 'disponible' => 0, 'imagen' => $img . 'decant-1.svg'],
    ['nombre' => 'Black Opium',             'marca' => 'Yves Saint Laurent', 'categoria' => 'decant', 'tamano' => '10 ml', 'precio' => 260, 'disponible' => 1, 'imagen' => $img . 'decant-2.svg'],
];
