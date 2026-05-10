<?php
// LÓGICA PHP (DASHBOARD DE ALUMNOS)
session_start();

if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php"); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
$vacantes = [];
$error_busqueda = null;

// LÓGICA DE BÚSQUEDA (GET)
$termino_busqueda = $_GET['search'] ?? '';
$tipo_contrato_filtro = $_GET['contrato'] ?? '';
$carrera_filtro = $_GET['carrera'] ?? ''; 

// --- INICIO DE LA LÓGICA SQL ---
try {
    $params = [];
    $where_conditions = ["v.estado = 'abierta'"]; 

    if (!empty($termino_busqueda)) {
        $where_conditions[] = "(v.titulo LIKE :search OR e.nombre_empresa LIKE :search OR v.ubicacion LIKE :search)";
        $params[':search'] = '%' . $termino_busqueda . '%';
    }

    if (!empty($tipo_contrato_filtro)) {
        $where_conditions[] = "v.tipo_contrato = :contrato";
        $params[':contrato'] = $tipo_contrato_filtro;
    }
    
    if (!empty($carrera_filtro)) {
        $where_conditions[] = "v.carrera_afin = :carrera"; 
        $params[':carrera'] = $carrera_filtro;
    }


    $sql = "SELECT id_vacante, titulo, ubicacion, estado, tipo_contrato, modalidad, fecha_publicacion, nombre_empresa
            FROM Vacantes v
            JOIN Empresas e ON v.id_empresa = e.id_empresa";
            
    if (count($where_conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }

    $sql .= " ORDER BY v.fecha_publicacion DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error de BD al buscar vacantes: " . $e->getMessage());
    $error_busqueda = "Error al conectar con el catálogo de vacantes. Por favor, intente más tarde.";
}
// --- FIN DE LA LÓGICA SQL ---


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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* ESTILOS DE BARRA DE NAVEGACIÓN UNIFICADOS (Se mantienen) */
        .navbar {
            background: var(--gradiente-js-principal); 
            padding: 15px 20px;
        }
        .navbar-brand {
             color: var(--color-blanco) !important;
             font-weight: 700; 
             font-size: 1.3rem; 
        }
        .navbar-links .welcome-msg {
            color: var(--color-blanco);
            margin-right: 15px;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Ajustes de espaciado de la barra de búsqueda */
        .welcome-hero {
            padding: 60px 0;
        }
        .search-bar-container {
            margin-top: 0 !important; 
            padding: 30px 0; 
            background-color: var(--color-fondo); 
            border-bottom: 1px solid var(--color-borde);
            box-shadow: none; 
            border-radius: 0; 
        }
    </style>
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
    
    <div class="welcome-hero">
        <div class="container">
            <h2>Bienvenido al Portal de Oportunidades</h2>
            <p>Encuentra tu práctica profesional o tu primer empleo ideal.</p>
        </div>
    </div>
    
    <div class="search-bar-container">
        <form action="dashboard.php" method="GET" class="search-layout container" 
              style="max-width: 1000px;"> 
            
            <div class="row mb-3">
                <div class="col-12">
                    <input type="text" name="search" class="search-input" 
                           placeholder="Buscar vacantes por título, empresa o ubicación..."
                           value="<?php echo htmlspecialchars($termino_busqueda); ?>"
                           style="width: 100%;">
                </div>
            </div>
            
            <div class="row g-2 align-items-center">
                
                <div class="<?php echo $col_contrato; ?>">
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
                
                <div class="<?php echo $col_carrera; ?>">
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
                
                <div class="<?php echo $col_buscar; ?>">
                    <button type="submit" class="boton-principal search-button w-100" style="height: 48px;">Buscar</button>
                </div>

                <?php if ($show_limpiar): ?>
                <div class="col-md-2">
                    <a href="dashboard.php" class="btn-secundario-form w-100" style="height: 48px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                        Limpiar
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="dashboard-container">
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