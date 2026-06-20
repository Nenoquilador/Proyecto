<?php
$files = [
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\gestionar_empresa.php',
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\detalle_tramite.php',
    'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\detalle_vacante.php'
];

$replacements = [
    "empresaludombre_empresa" => "empresa['nombre_empresa",
    "empresaludombre" => "empresa['nombre",
    "empresaludotas_internas" => "empresa['notas_internas",
    "empresaludo" => "empresa no",
    "repeat(año-fill" => "repeat(auto-fill",
    "overflow-y: año" => "overflow-y: auto",
    "fañok" => "fa-book",
    "Admástrativañolo Interno)" => "Administrativa (Solo Interno)",
    "llamadíacion" => "llamadas con",
    "Daños Internos" => "Datos Internos",
    "carreras_guardías" => "carreras_guardadas",
    "trañorm" => "transform",
    "btn-premiumásecondary" => "btn-premium secondary",
    "? éxito'" => "? 'exito'",
    "box-sharrow" => "box-shadow",
    "text-decoracion" => "text-decoration",
    "trámás" => "tramites",
    "Trámás" => "Tramites",
    "gestionar_tramás.php" => "gestionar_tramites.php",
    "vacColors" => "vacColors",
    "vacCounts" => "vacCounts",
    "dañoard" => "dashboard",
    "Dañoo" => "Dashboard",
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($replacements as $bad => $good) {
            $content = str_replace($bad, $good, $content);
        }
        
        // Fix syntax error of unclosed brackets specifically for gestionar_empresa.php
        $content = str_replace("['notas_internas'] ?? ''); ?></textarea>", "['notas_internas'] ?? ''); ?></textarea>", $content);
        $content = str_replace("['nombre_empresa']); ?></span>", "['nombre_empresa']); ?></span>", $content);
        $content = str_replace("['nombre_empresa']); ?></title>", "['nombre_empresa']); ?></title>", $content);
        
        file_put_contents($file, $content);
    }
}
echo "Third pass applied.";
?>
