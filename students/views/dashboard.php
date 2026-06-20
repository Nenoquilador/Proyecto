<?php
// Función de utilidad (Formatear tags)
function formatear_tag($texto) {
    if (empty($texto)) { return "N/A"; }
    $formato = str_replace('_', ' ', $texto);
    return ucwords($formato);
}

// Arreglo de carreras
$carreras_ejemplo = [
    'administracion' => 'Administración',
    'derecho' => 'Derecho',
    'contaduria' => 'Contaduría',
    'sistemas' => 'Ing. en Sistemas',
    'psicologia' => 'Psicología',
    'diseno_grafico' => 'Diseño Gráfico',
    'arquitectura' => 'Arquitectura',
    'mercadotecnia' => 'Mercadotecnia'
];

// --- LÓGICA DE DISEÑO PARA EL GRID DE BÚSQUEDA ---
$show_limpiar = (!empty($termino_busqueda) || !empty($tipo_contrato_filtro) || !empty($carrera_filtro));
$col_contrato = $show_limpiar ? 'col-md-4' : 'col-md-5';
$col_carrera = $show_limpiar ? 'col-md-4' : 'col-md-5';
$col_buscar = $show_limpiar ? 'col-md-2' : 'col-md-2';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumnos | Justo Sierra</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <nav class="navbar"> 
        <div class="navbar-brand">
            Bolsa de Trabajo <span class="brand-js">Justo Sierra</span>
        </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($nombre_alumno); ?></span>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a> 
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="welcome-hero animate fadeRight">
        <div class="container">
            <h2>Bienvenido al Portal de Oportunidades</h2>
            <p>Encuentra tu práctica profesional o tu primer empleo ideal.</p>
        </div>
    </div>
    
    <div class="search-bar-container animate fadeRight" style="animation-delay: 0.1s;">
        <form action="dashboard.php" method="GET" class="search-layout container" style="max-width: 1000px;"> 
            
            <div class="js-search-row-full">
                <input type="text" name="search" class="search-input" 
                       placeholder="Buscar vacantes por título, empresa o ubicación..."
                       value="<?php echo htmlspecialchars($termino_busqueda); ?>"
                       style="width: 100%;">
            </div>
            
            <div class="js-search-grid <?php echo $show_limpiar ? 'with-clear' : 'without-clear'; ?>">
                
                <div>
                    <select name="contrato" class="filter-select" 
                            style="width: 100%; height: 48px;">
                        <option value="">Tipo de Contrato</option>
                        <?php $opciones_contrato = [
                            'tiempo_completo' => 'Tiempo Completo',
                            'medio_tiempo' => 'Medio Tiempo',
                            'practicas' => 'Prácticas / Pasantía',
                            'por_proyecto' => 'Por Proyecto'
                        ];
                        foreach ($opciones_contrato as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $tipo_contrato_filtro === $val ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <select name="carrera" class="filter-select" 
                            style="width: 100%; height: 48px;">
                        <option value="">Todas las Carreras</option>
                        <?php foreach ($carreras_ejemplo as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $carrera_filtro === $val ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="boton-principal search-button" style="width: 100%; height: 48px;">Buscar</button>
                </div>

                <?php if ($show_limpiar): ?>
                <div>
                    <a href="dashboard.php" class="btn-secundario-form" style="width: 100%; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; margin:0;">
                        Limpiar
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="dashboard-container animate fadeRight" style="animation-delay: 0.2s;">
        <h2 style="text-align: left; margin-bottom: 30px;">
            Vacantes Encontradas (<?php echo count($vacantes); ?>)
        </h2>

        <?php if ($error_busqueda): ?>
            <div class='mensaje error'><?php echo $error_busqueda; ?></div>
        <?php elseif (count($vacantes) > 0): ?>
            <div class="job-list">
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="job-card">
                        
                        <div class="job-card-header">
                            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-texto-principal);">
                                <?php echo htmlspecialchars($vacante['titulo']); ?>
                            </h2>
                            <h3 style="font-size: 1rem; color: var(--color-js-rojo-principal); font-weight: 500; margin-top: 5px;">
                                <?php echo htmlspecialchars($vacante['nombre_empresa']); ?>
                            </h3>
                        </div>
                        
                        <div class="job-card-body">
                            <div class="job-card-tags" style="margin-bottom: 15px;">
                                <?php 
                                    $tags = [
                                        $vacante['tipo_contrato'],
                                        $vacante['modalidad']
                                    ];
                                    foreach ($tags as $tag_val): 
                                        $clase = ($tag_val === $vacante['tipo_contrato']) ? 'tag-contrato' : '';
                                ?>
                                    <span class="tag <?php echo $clase; ?>"><?php echo formatear_tag($tag_val); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <p style="margin-top: 15px; font-size: 0.95rem; color: var(--color-texto-secundario);">
                                <i class="fas fa-map-marker-alt" style="color: var(--color-js-rojo-secundario);"></i> Ubicación: <?php echo htmlspecialchars($vacante['ubicacion']); ?>
                            </p>
                            <p style="font-size: 0.95rem; color: var(--color-texto-secundario);">
                                <i class="far fa-calendar-alt" style="color: var(--color-js-rojo-secundario);"></i> Publicado: <?php echo date('d/M/Y', strtotime($vacante['fecha_publicacion'])); ?>
                            </p>
                        </div>
                        
                        <div class="job-card-footer">
                            <span style="color: var(--color-js-rojo-principal); font-weight: 700;">
                                ¡Aplica antes de que sea tarde!
                            </span>
                            
                            <a href="detalle_vacante.php?id=<?php echo $vacante['id_vacante']; ?>" 
                               class="boton-principal-sm">
                                <i class="fas fa-arrow-right"></i> Ver Detalle
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="job-card-empty">
                <h2>No se encontraron vacantes.</h2>
                <p>Intenta una búsqueda diferente o revisa más tarde.</p>
            </div>
        <?php endif; ?>
        
    </div>

</body>
</html>
