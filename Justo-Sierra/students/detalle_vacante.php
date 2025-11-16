<?php
// 1. SEGURIDAD Y SESIÓN
session_start();

if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php"); 
    exit();
}

$id_vacante = $_GET['id'] ?? null;
if (!$id_vacante || !is_numeric($id_vacante)) {
    header("Location: dashboard.php");
    exit();
}

// 2. INCLUSIÓN DE CONEXIÓN
try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$id_alumno = $_SESSION['id_alumno'];
$vacante = null;
$error_bd = null;

// 3. OBTENER DETALLES DE LA VACANTE
try {
    $sql = "SELECT 
                v.titulo, v.descripcion, v.ubicacion, v.modalidad, v.tipo_contrato, v.salario_ofrecido, v.fecha_publicacion,
                e.nombre_empresa, e.sitio_web,
                (SELECT COUNT(*) FROM Postulaciones p WHERE p.id_alumno = :id_alumno AND p.id_vacante = :id_vacante) AS ya_postulado
            FROM 
                Vacantes v
            JOIN 
                Empresas e ON v.id_empresa = e.id_empresa
            WHERE 
                v.id_vacante = :id_vacante AND v.estado = 'abierta'";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacante) {
        $error_bd = "Vacante no encontrada o cerrada.";
    }

} catch (PDOException $e) {
    $error_bd = "Error al cargar la vacante: " . $e->getMessage();
}

// Función de utilidad (Formatear tags)
function formatear_tag($texto) {
    if (empty($texto)) { return "N/A"; }
    $formato = str_replace('_', ' ', $texto);
    return ucwords($formato);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle: <?php echo htmlspecialchars($vacante['titulo'] ?? 'Vacante'); ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* ✅ ESTILOS DE BARRA DE NAVEGACIÓN UNIFICADOS (COMPLETOS) */
        .navbar { 
            background: var(--gradiente-js-principal); 
            padding: 15px 30px; /* Añadido padding */
        }
        .navbar-brand { 
            color: var(--color-blanco) !important; 
            font-weight: 700; 
            font-size: 1.3rem; 
            font-family: var(--fuente-titulos);
        }
        /* ✅ REGLA AÑADIDA: Estilo para el span 'brand-js' */
        .navbar-brand .brand-js {
            background: linear-gradient(60deg, var(--color-blanco), var(--color-js-amarillo));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            display: inline-block;
            font-size: 1.5rem;
            margin-left: 5px;
        }
        /* ✅ REGLA AÑADIDA: Estilo para el saludo */
        .navbar-links .welcome-msg {
            color: var(--color-blanco);
            margin-right: 15px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Contenedor Flex para las dos columnas */
        .detail-card-container { 
            display: flex; 
            gap: 30px; 
            margin-top: 30px;
        }
        .detail-main { 
            flex: 3; 
            padding: 30px;
            background-color: var(--color-blanco);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .detail-sidebar { 
            flex: 1; 
            padding: 20px; 
            background-color: var(--color-fondo); 
            border-radius: 8px; 
            height: fit-content;
            border: 1px solid var(--color-borde);
        }
        
        /* Metadatos dentro del sidebar */
        .sidebar-item {
            padding: 10px 0;
            border-bottom: 1px dashed var(--color-borde);
            font-size: 0.95rem;
        }
        .sidebar-item:last-child { border-bottom: none; }
        .sidebar-item strong { 
            color: var(--color-js-rojo-secundario); 
            display: block; 
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        .sidebar-item-value { 
            font-weight: 500; 
            color: var(--color-texto-principal);
        }

        /* Responsive para móvil */
        @media (max-width: 992px) {
            .detail-card-container {
                flex-direction: column;
            }
            .detail-main { padding: 20px; }
        }
    </style>
</head>
<body>
    
    <nav class="navbar"> 
        <div class="navbar-brand">
            Bolsa de Trabajo <span class="brand-js">Justo Sierra</span>
        </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($_SESSION['nombre_alumno'] ?? 'Alumno'); ?></span>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a> 
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-link" style="font-weight: 600; margin: 20px 0; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Volver al Catálogo
        </a>

        <?php if ($error_bd): ?>
            <div class='mensaje error'><?php echo htmlspecialchars($error_bd); ?></div>
        <?php elseif ($vacante): ?>

            <div class="detail-card-container">
                
                <div class="detail-main">
                    <h1 style="color: var(--color-js-rojo-principal); margin-bottom: 5px; font-size: 2.5rem;"><?php echo htmlspecialchars($vacante['titulo']); ?></h1>
                    <h2 style="font-size: 1.25rem; color: var(--color-texto-secundario); margin-bottom: 25px;"><?php echo htmlspecialchars($vacante['nombre_empresa']); ?></h2>
                    
                    <h3 style="font-size: 1.2rem; color: var(--color-js-rojo-secundario); margin-top: 30px;">Descripción de la Vacante</h3>
                    <p style="white-space: pre-wrap; font-size: 1rem; color: var(--color-texto-principal); margin-top: 10px;">
                        <?php echo htmlspecialchars($vacante['descripcion']); ?>
                    </p>
                    
                    <div style="margin-top: 40px; text-align: center;">
                        <?php if ($vacante['ya_postulado'] > 0): ?>
                            <button class="boton-principal" disabled style="background-color: var(--color-exito); opacity: 0.8; cursor: default; padding: 12px 30px;">
                                <i class="fas fa-check"></i> Ya Postulado
                            </button>
                            <p style="color: var(--color-exito); margin-top: 10px; font-weight: 600;">Ya enviaste tu postulación a esta vacante.</p>
                        <?php else: ?>
                            <a href="procesar_postulacion.php?id=<?php echo $id_vacante; ?>" class="boton-principal" style="padding: 12px 30px;">
                                <i class="fas fa-paper-plane"></i> Postular Ahora
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-sidebar">
                    <h3 style="font-size: 1.1rem; color: var(--color-js-rojo-secundario); border-bottom: 1px solid var(--color-borde); padding-bottom: 10px; margin-bottom: 15px;">
                        Detalles Clave
                    </h3>
                    
                    <div class="sidebar-item">
                        <strong><i class="fas fa-map-marker-alt"></i> UBICACIÓN</strong>
                        <span class="sidebar-item-value"><?php echo htmlspecialchars($vacante['ubicacion']); ?></span>
                    </div>
                    
                    <div class="sidebar-item">
                        <strong><i class="fas fa-dollar-sign"></i> SALARIO</strong>
                        <span class="sidebar-item-value"><?php echo $vacante['salario_ofrecido'] ? '$' . number_format($vacante['salario_ofrecido'], 2) : 'No especificado'; ?></span>
                    </div>
                    
                    <div class="sidebar-item">
                        <strong><i class="far fa-calendar-alt"></i> PUBLICADO</strong>
                        <span class="sidebar-item-value"><?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?></span>
                    </div>

                    <div style="margin-top: 15px;">
                        <span class="key-tag" style="background-color: #fbe6c2; color: #e65100;"><?php echo formatear_tag($vacante['tipo_contrato']); ?></span>
                        <span class="key-tag"><?php echo formatear_tag($vacante['modalidad']); ?></span>
                    </div>

                    <h3 style="font-size: 1.1rem; margin-top: 30px; color: var(--color-js-rojo-secundario); border-bottom: 1px solid var(--color-borde); padding-bottom: 10px; margin-bottom: 15px;">
                        Acerca de la Empresa
                    </h3>
                    <p style="font-size: 1rem;">
                        <a href="<?php echo htmlspecialchars($vacante['sitio_web']); ?>" target="_blank" style="font-weight: 600; color: var(--color-texto-principal);">
                            <?php echo htmlspecialchars($vacante['nombre_empresa']); ?>
                        </a>
                    </p>
                    <p style="font-size: 0.9em; color: var(--color-texto-secundario);">
                        <i class="fas fa-globe"></i> Visitar Sitio Web
                    </p>
                </div>

            </div>

        <?php endif; ?>
    </div>

</body>
</html>