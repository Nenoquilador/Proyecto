<?php
$dir = 'C:/xampp/htdocs/Justo-Sierra/admin/views/empresas/';
$files = glob($dir . '*.php');

$words = [
    "vinculación", "Vinculación", "Catálogo", "SÍ", "TRÁMITES", "Métricas", "Gestión", 
    "Trámite", "Trámites", "Última", "Validación", "Acción", "título", "Revisión", "Razón", 
    "Decisión", "aquí", "llamó", "Descripción", "Sesión", "Teléfono", "Institución", 
    "Publicación", "Bitácora", "SECCIÓN"
];

foreach ($files as $f) {
    if (basename($f) == 'carga_masiva.php') continue;
    $content = file_get_contents($f);
    
    // Replace double-encoded UTF-8 exactly
    foreach ($words as $w) {
        // Double encoding simulates reading UTF-8 bytes as ISO-8859-1 and converting back to UTF-8
        $double = mb_convert_encoding($w, 'UTF-8', 'ISO-8859-1');
        $content = str_replace($double, $w, $content);
    }
    
    // Fallback for weird grep outputs
    $content = str_replace("\xC7\xAD", "á", $content);
    $content = str_replace("\xC7\xB8", "é", $content);
    
    file_put_contents($f, $content);
}

echo "Done";
?>
