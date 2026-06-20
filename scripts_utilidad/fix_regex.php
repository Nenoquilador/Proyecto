<?php
$files = glob('C:\xampp\htdocs\Justo-Sierra\admin\views\empresas\*.php');
$controllers = glob('C:\xampp\htdocs\Justo-Sierra\admin\controllers\*.php');
$allFiles = array_merge($files, $controllers);

$replacements = [
    '/Vinculaci.n/' => 'Vinculación',
    '/administraci.n/' => 'administración',
    '/Sesi.n/' => 'Sesión',
    '/M.tricas/' => 'Métricas',
    '/Gesti.n/' => 'Gestión',
    '/Tr.mites/' => 'Trámites',
    '/Tr.mite/' => 'Trámite',
    '/Padr.n/' => 'Padrón',
    '/.xito/' => 'éxito',
    '/Pr.ximas/' => 'Próximas',
    '/gr.fica/' => 'gráfica',
    '/cat.logo/' => 'catálogo',
    '/Cat.logo/' => 'Catálogo',
    '/Aprobaci.n/' => 'Aprobación',
    '/Descripci.n/' => 'Descripción',
    '/Ubicaci.n/' => 'Ubicación',
    '/D.as/' => 'Días',
    '/d.as/' => 'días',
    '/M.s/' => 'Más',
    '/m.s/' => 'más',
    '/Validaci.n/' => 'Validación',
    '/A.o/' => 'Año',
    '/a.o/' => 'año',
    '/Pr.cticas/' => 'Prácticas',
    '/pr.cticas/' => 'prácticas',
    '/evaluaci.n/' => 'evaluación',
    '/Evaluaci.n/' => 'Evaluación',
    '/Acci.n/' => 'Acción',
    '/Informaci.n/' => 'Información',
    '/A.adir/' => 'Añadir',
    '/a.adir/' => 'añadir',
    '/Dise.o/' => 'Diseño',
    '/dise.o/' => 'diseño',
    '/Compa..as/' => 'Compañías',
    '/compa..as/' => 'compañías',
    '/compa..a/' => 'compañía',
    '/Pr.ximo/' => 'Próximo',
    '/C.digo/' => 'Código',
    '/Tel.fono/' => 'Teléfono',
    '/Direcci.n/' => 'Dirección',
    '/Revisi.n/' => 'Revisión',
    '/revisi.n/' => 'revisión',
    '/Atenci.n/' => 'Atención',
    '/atenci.n/' => 'atención'
];

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    
    // Convert to UTF-8 properly if it is ANSI
    if (mb_detect_encoding($content, 'UTF-8, ISO-8859-1') === 'ISO-8859-1') {
        $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
    }
    
    // Apply regex to catch broken bytes (using s modifier so . matches anything, and NOT u modifier since bytes might be invalid UTF8)
    foreach ($replacements as $pattern => $good) {
        // Because a multi-byte sequence like U+FFFD is 3 bytes, '.' only matches 1 byte.
        // So we use '.{1,3}' to match 1 to 3 bad bytes representing the single character.
        $pattern = str_replace('.', '.{1,3}', $pattern);
        $content = preg_replace($pattern . 's', $good, $content);
    }
    
    file_put_contents($file, $content);
}
echo "Regex fix applied.";
?>
