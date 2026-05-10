<?php
// LÓGICA PHP (PERFIL DE EMPRESA)
session_start();

if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
    header('Location: ../login.php'); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$id_empresa = $_SESSION['id_empresa'];

// CONSULTAR DATOS DE LA EMPRESA
try {
    $sql = "SELECT id_empresa, nombre_empresa, email_contacto, rfc, descripcion, sitio_web 
            FROM Empresas 
            WHERE id_empresa = :id_empresa";
    
    $stmt = $conexion->prepare($sql); 
    $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
    $stmt->execute();
    
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        session_destroy();
        header('Location: ../login.php?error=Datos de empresa no encontrados');
        exit();
    }
} catch (PDOException $e) {
    die("Error en la consulta de base de datos: " . $e->getMessage()); 
}

$enlace_edicion = "procesar_empresa.php?action=editar_perfil"; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Empresa | <?php echo htmlspecialchars($empresa['nombre_empresa'] ?? 'N/A'); ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Ajustes de estilo del perfil */
        .profile-header h3 { 
            text-align: center;
        }
        .profile-header p {
            text-align: center;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .profile-section-title {
            font-family: var(--fuente-titulos);
            font-size: 0.9rem;
            color: var(--color-js-rojo-principal);
            font-weight: 700;
            margin-bottom: 5px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        .profile-data-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        .profile-data-column {
            flex: 1;
        }
        .profile-field-item {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--color-borde);
        }
        .field-label-small {
            font-size: 0.8rem;
            color: var(--color-js-rojo-secundario);
            font-weight: 600;
            display: block;
            margin-bottom: 3px;
        }
        .field-value-main {
            font-size: 1rem;
            color: var(--color-texto-principal);
            font-weight: 500;
        }
    </style>
</head>
<body>
    
    <nav class="navbar" style="background: var(--color-js-rojo-secundario);">
        <div class="navbar-brand">Portal de Reclutamiento <span class="brand-js">JS</span></div>
        <div class="navbar-links">
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a>
            <a href="gestion_vacantes.php" class="btn-secondary-nav">Gestionar Vacantes</a>
            
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="container mt-5">
        
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <div class="profile-view"> 
                    
                    <div class="profile-header">
                        <h3 class="mb-0"><i class="fas fa-building"></i> Perfil de Empresa</h3>
                        <p>Visualización y gestión de la información de tu compañía.</p>
                    </div>
                    
                    <div style="padding: 30px;">
                        
                        <div class="profile-section-title">NOMBRE DE LA EMPRESA</div>
                        <h2 style="font-size: 1.5rem; margin-top: 5px; margin-bottom: 20px; color: var(--color-texto-principal);">
                            <?php echo htmlspecialchars($empresa['nombre_empresa'] ?? 'N/A'); ?>
                        </h2>
                        
                        <div class="profile-data-row">
                            <div class="profile-data-column">
                                <div class="profile-field-item">
                                    <span class="field-label-small">EMAIL DE CONTACTO</span>
                                    <strong class="field-value-main"><?php echo htmlspecialchars($empresa['email_contacto'] ?? 'N/A'); ?></strong>
                                </div>
                                <div class="profile-field-item">
                                    <span class="field-label-small">SITIO WEB</span>
                                    <a id="sitio-web-link" href="#" target="_blank" class="field-value-main" style="display: block;">
                                        <?php echo htmlspecialchars($empresa['sitio_web'] ?? 'No registrado'); ?>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="profile-data-column">
                                <div class="profile-field-item">
                                    <span class="field-label-small">RFC</span>
                                    <strong class="field-value-main"><?php echo htmlspecialchars($empresa['rfc'] ?? 'No registrado'); ?></strong>
                                </div>
                                <div class="profile-field-item">
                                    <span class="field-label-small">ID DE REGISTRO</span>
                                    <strong class="field-value-main"><?php echo htmlspecialchars($empresa['id_empresa'] ?? 'N/A'); ?></strong>
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 20px 0;">

                        <div class="profile-section-title">DESCRIPCIÓN DE LA EMPRESA</div>
                        <p style="color: var(--color-texto-principal); font-size: 1rem; margin-top: 10px;">
                            <?php echo nl2br(htmlspecialchars($empresa['descripcion'] ?? 'Sin descripción.')); ?>
                        </p>

                        <div style="text-align: right; margin-top: 30px;">
                            <a href="<?php echo $enlace_edicion; ?>" class="boton-principal">
                                <i class="fas fa-edit"></i> Editar Perfil
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para asegurar que el enlace del sitio web sea válido
        document.addEventListener('DOMContentLoaded', function() {
            var link = document.getElementById('sitio-web-link');
            var url = link.textContent.trim();
            if (url && url !== 'No registrado') {
                if (!/^https?:\/\//i.test(url)) {
                    url = 'http://' + url;
                }
                link.href = url;
            } else {
                 link.href = '#';
            }
        });
    </script>
</body>
</html>