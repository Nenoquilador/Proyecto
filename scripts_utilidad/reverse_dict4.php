<?php
$file = 'C:\\xampp\\htdocs\\Justo-Sierra\\admin\\views\\empresas\\detalle_tramite.php';

$replacements = [
    "nombre_empresa normateaño" => "nombre_empresa_formateado",
    "fañock" => "fa-clock",
    "farrownload" => "fa-download",
    "daños" => "datos"
];

if (file_exists($file)) {
    $content = file_get_contents($file);
    foreach ($replacements as $bad => $good) {
        $content = str_replace($bad, $good, $content);
    }
    file_put_contents($file, $content);
}
echo "Fourth pass applied.";
?>
