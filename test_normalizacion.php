<?php
/**
 * Script de prueba para demostrar la normalización de texto
 * Este archivo es solo para demostración y no es parte del sistema
 */
function normalizeText($text)
{
    // Convertir a mayúsculas para comparación case-insensitive
    $text = mb_strtoupper($text, 'UTF-8');

    // Mapa de caracteres con acentos a sus equivalentes sin acentos
    $unwanted_array = [
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'Ñ' => 'N', 'Ç' => 'C',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ];

    return strtr($text, $unwanted_array);
}

// Ejemplos de nombres en el sistema
$nombresEnSistema = [
    'JOÃO SILVA',
    'MARÍA GARCÍA',
    'JOSÉ SANTOS',
    'ANDRÉ OLIVEIRA',
    'FRANÇOIS DUBOIS',
    'JOSÉ MARÍA LÓPEZ',
    'ANA CÉLIA SOUZA',
    'VÍTOR GONÇALVES',
];

// Búsquedas que los usuarios podrían hacer
$busquedasUsuarios = [
    'joao',
    'maria',
    'jose',
    'andre',
    'francois',
    'jose maria',
    'ana celia',
    'vitor goncalves',
];

echo "=== DEMOSTRACIÓN DE NORMALIZACIÓN DE TEXTO ===\n\n";

echo "Nombres en el sistema:\n";
foreach ($nombresEnSistema as $nombre) {
    echo "  - $nombre\n";
}

echo "\n".str_repeat('=', 60)."\n\n";

foreach ($busquedasUsuarios as $busqueda) {
    echo "Búsqueda del usuario: '$busqueda'\n";
    echo "Búsqueda normalizada: '".normalizeText($busqueda)."'\n";
    echo "Resultados encontrados:\n";

    $encontrados = 0;
    foreach ($nombresEnSistema as $nombre) {
        $nombreNormalizado = normalizeText($nombre);
        $busquedaNormalizada = normalizeText($busqueda);

        if (stripos($nombreNormalizado, $busquedaNormalizada) !== false) {
            echo "  ✓ $nombre\n";
            $encontrados++;
        }
    }

    if ($encontrados === 0) {
        echo "  (ninguno)\n";
    }

    echo "\n".str_repeat('-', 60)."\n\n";
}

echo "=== COMPARACIÓN ANTES Y DESPUÉS ===\n\n";

$ejemplos = [
    ['nombre' => 'JOÃO', 'busqueda' => 'joao'],
    ['nombre' => 'MARÍA', 'busqueda' => 'maria'],
    ['nombre' => 'JOSÉ', 'busqueda' => 'jose'],
    ['nombre' => 'ANDRÉ', 'busqueda' => 'andre'],
    ['nombre' => 'VÍTOR', 'busqueda' => 'vitor'],
];

foreach ($ejemplos as $ejemplo) {
    $nombre = $ejemplo['nombre'];
    $busqueda = $ejemplo['busqueda'];

    // Búsqueda SIN normalización (método anterior)
    $matchSinNormalizacion = stripos($nombre, $busqueda) !== false;

    // Búsqueda CON normalización (método nuevo)
    $nombreNormalizado = normalizeText($nombre);
    $busquedaNormalizada = normalizeText($busqueda);
    $matchConNormalizacion = stripos($nombreNormalizado, $busquedaNormalizada) !== false;

    echo "Nombre: $nombre | Búsqueda: $busqueda\n";
    echo '  Sin normalización: '.($matchSinNormalizacion ? '✓ Encontrado' : '✗ NO encontrado')."\n";
    echo '  Con normalización: '.($matchConNormalizacion ? '✓ Encontrado' : '✗ NO encontrado')."\n";
    echo "\n";
}

echo "=== FIN DE LA DEMOSTRACIÓN ===\n";
