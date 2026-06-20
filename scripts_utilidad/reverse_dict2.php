<?php
$files = [
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\gestionar_empresa.php',
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\detalle_tramite.php',
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\detalle_vacante.php'
];

$replacements = [
    "empresaludombre_empresa" => "empresa['nombre_empresa",
    "? éxito'" => "? 'exito'",
    "text-trañorm" => "text-transform",
    "box-sharrow" => "box-shadow",
    "text-decoracion" => "text-decoration",
    "trámás" => "tramites",
    "Trámás" => "Tramites",
    "gestionar_tramás.php" => "gestionar_tramites.php",
    "vacColors" => "vacColors",
    "vacCounts" => "vacCounts",
    "empresaludombre" => "empresa['nombre", // just in case
    // Fix any missing ']' before ')' if it got corrupted.
    "empresaludombre_empresa']); ?>" => "empresa['nombre_empresa']); ?>",
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($replacements as $bad => $good) {
            $content = str_replace($bad, $good, $content);
        }
        file_put_contents($file, $content);
    }
}
echo "Second pass applied.";
?>
