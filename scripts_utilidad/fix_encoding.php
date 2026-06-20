<?php
$files = glob('C:\xampp\htdocs\Justo-Sierra\admin\views\empresas\*.php');
$controllers = glob('C:\xampp\htdocs\Justo-Sierra\admin\controllers\*.php');
$allFiles = array_merge($files, $controllers);

$words = [
    'Vinculación', 'administración', 'Sesión', 'Métricas', 'Gestión', 
    'Trámites', 'Trámite', 'Padrón', 'éxito', 'Próximas', 'gráfica', 
    'catálogo', 'Catálogo', 'Aprobación', 'Descripción', 'Ubicación', 
    'Días', 'días', 'Más', 'más', 'Validación', 'Año', 'año', 
    'Prácticas', 'prácticas', 'evaluación', 'Evaluación', 'Acción', 
    'Información', 'Añadir', 'añadir', 'Diseño', 'diseño', 
    'Compañías', 'compañías', 'compañía', 'Próximo', 'Código', 
    'Teléfono', 'Dirección', 'Revisión', 'revisión', 'Atención', 'atención'
];

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    
    // Convert the broken ANSI bytes into proper UTF-8 strings
    foreach ($words as $word) {
        // Create the ANSI version of the word
        $ansiWord = mb_convert_encoding($word, 'Windows-1252', 'UTF-8');
        
        // Replace ANSI word with UTF-8 word
        $content = str_replace($ansiWord, $word, $content);
        
        // Also replace the replacement character version if it exists
        $replacementCharWord = str_replace(['á','é','í','ó','ú','ñ','Á','É','Í','Ó','Ú','Ñ'], "\xEF\xBF\xBD", $word);
        $content = str_replace($replacementCharWord, $word, $content);
    }
    
    file_put_contents($file, $content);
}
echo "ANSI and Broken UTF-8 fixed.";
?>
