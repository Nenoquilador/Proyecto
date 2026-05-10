<?php
// --------------------------------------------------
// DETALLE VACANTE (ADMIN) - REDISEÑO PROFESIONAL
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}

$id_vacante = $_GET['id'] ?? null;
if (!$id_vacante || !is_numeric($id_vacante)) {
    header("Location: gestionar_vacantes_admin.php?status=error&msg=" . urlencode("ID de vacante inválido."));
    exit();
}

include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación';
$vacante = null;
$error_db = null;

try {
    $sql = "SELECT
                v.id_vacante, v.titulo, v.descripcion, v.ubicacion, v.modalidad, v.tipo_contrato, v.salario_ofrecido, v.fecha_publicacion, v.estado AS estado_vacante,
                e.id_empresa, e.nombre_empresa, e.email_contacto, e.rfc, e.descripcion AS descripcion_empresa, e.sitio_web, e.estado_validacion AS estado_empresa
            FROM
                vacantes v
            JOIN
                empresas e ON v.id_empresa = e.id_empresa
            WHERE
                v.id_vacante = :id_vacante";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_vacante' => $id_vacante]);
    $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacante) { $error_db = "No se encontró la vacante solicitada."; }

} catch (PDOException $e) {
    $error_db = "Error al cargar los detalles: " . $e->getMessage();
}

if (!function_exists('formatear_tag')) {
    function formatear_tag($texto) {
        if (empty($texto)) { return "N/A"; }
        return ucwords(str_replace('_', ' ', $texto));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Vacante | <?php echo htmlspecialchars($vacante['titulo'] ?? 'Admin'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --js-red: #E60013;
            --js-red-dark: #C40010;
            --bg-light: #F8FAFC;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }

        /* HEADER & SIDEBAR */
        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); position: sticky; top: 0; z-index: 100; }
        .brand-box { display: flex; align-items: center; gap: 12px; color: var(--js-red); }
        .brand-box h1 { margin: 0; font-family: 'Montserrat', sans-serif; font-size: 1.3rem; }
        .btn-exit { background: #FEE2E2; color: var(--js-red); padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-exit:hover { background: var(--js-red); color: white; }

        .admin-wrapper { display: flex; min-height: calc(100vh - 70px); }
        .sidebar { width: 280px; background: white; padding: 30px 20px; border-right: 1px solid #E2E8F0; }
        .sidebar-profile { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #F1F5F9; }
        .sidebar-profile img { width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; padding: 5px; margin-bottom: 10px; }
        .sidebar-profile h3 { margin: 0; font-size: 0.95rem; font-family: 'Montserrat'; }

        .menu-group { margin-bottom: 25px; }
        .menu-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-left: 10px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: var(--text-muted); border-radius: 8px; font-weight: 500; transition: 0.2s; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #FFF1F2; color: var(--js-red); }
        .nav-link.active { background: var(--js-red); color: white; }

        /* CONTENIDO DE DETALLE */
        .main-container { flex: 1; padding: 40px; max-width: 1000px; margin: 0 auto; }
        .detail-card { background: white; border-radius: 12px; padding: 35px; box-shadow: var(--shadow); }
        
        .section-header { border-bottom: 1px solid #F1F5F9; padding-bottom: 20px; margin-bottom: 25px; }
        .section-header h2 { font-family: 'Montserrat'; margin: 0; color: var(--text-main); font-size: 1.6rem; }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-bottom: 35px; }
        .info-item label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .info-item span { font-size: 1rem; font-weight: 500; color: var(--text-main); }

        .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: capitalize; }
        .status-abierta { background: #DCFCE7; color: #166534; }
        .status-cerrada { background: #FEE2E2; color: #991B1B; }

        .description-box { background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px solid #E2E8F0; line-height: 1.6; margin-bottom: 30px; font-size: 0.95rem;}

        .actions-bar { display: flex; justify-content: space-between; align-items: center; padding-top: 30px; border-top: 1px solid #F1F5F9; }
        .btn-saas { padding: 10px 22px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; border: none; cursor: pointer; }
        .btn-outline { background: white; border: 1px solid #E2E8F0; color: var(--text-muted); }
        .btn-outline:hover { background: #F1F5F9; color: var(--text-main); }
        .btn-danger { background: #FEE2E2; color: var(--js-red); }
        .btn-danger:hover { background: var(--js-red); color: white; }
        .btn-success { background: #DCFCE7; color: #166534; }
        .btn-success:hover { background: #10B981; color: white; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box"><i class="fas fa-university"></i><h1>JUSTO SIERRA | ADMIN</h1></div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span>
            <a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff" alt="Perfil">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--text-muted);">Administrador</small>
            </div>
            <nav>
                <div class="menu-group">
                    <p class="menu-title">General</p>
                    <a href="dashboard_admin.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                </div>
                <div class="menu-group">
                    <p class="menu-title">Bolsa de Trabajo</p>
                    <a href="gestionar_empresas_admin.php" class="nav-link"><i class="fas fa-building"></i> Empresas</a>
                    <a href="gestionar_vacantes_admin.php" class="nav-link"><i class="fas fa-briefcase"></i> Vacantes</a>
                    <a href="gestionar_tramites_sspp.php" class="nav-link"><i class="fas fa-file-signature"></i> Trámites SSPP</a>
                </div>
            </nav>
        </aside>

        <main class="main-container">
            <?php if ($error_db): ?>
                <div style="background: #FEE2E2; color: #991B1B; padding: 20px; border-radius: 12px; font-weight: 500;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_db); ?>
                </div>
            <?php elseif ($vacante): ?>
                
                <div class="detail-card">
                    <div class="section-header">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <h2><?php echo htmlspecialchars($vacante['titulo']); ?></h2>
                            <span class="status-pill status-<?php echo strtolower($vacante['estado_vacante']); ?>">
                                <?php echo $vacante['estado_vacante']; ?>
                            </span>
                        </div>
                        <p style="color:var(--text-muted); margin-top:10px;">
                            Publicada por: <strong style="color:var(--js-red);"><i class="far fa-building"></i> <?php echo htmlspecialchars($vacante['nombre_empresa']); ?></strong>
                        </p>
                    </div>

                    <div class="info-grid">
                        <div class="info-item"><label>Modalidad</label><span><?php echo formatear_tag($vacante['modalidad']); ?></span></div>
                        <div class="info-item"><label>Contrato</label><span><?php echo formatear_tag($vacante['tipo_contrato']); ?></span></div>
                        <div class="info-item"><label>Ubicación</label><span><?php echo htmlspecialchars($vacante['ubicacion']); ?></span></div>
                        <div class="info-item"><label>Salario</label><span><?php echo $vacante['salario_ofrecido'] ? '$' . number_format($vacante['salario_ofrecido'], 2) : 'No especificado'; ?></span></div>
                        <div class="info-item"><label>Fecha Inicio</label><span><?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?></span></div>
                        <div class="info-item"><label>ID Vacante</label><span>#<?php echo $vacante['id_vacante']; ?></span></div>
                    </div>

                    <h4 style="font-family:'Montserrat'; font-size:1rem; margin-bottom:15px; color: var(--text-main);">Descripción de la Vacante</h4>
                    <div class="description-box"><?php echo nl2br(htmlspecialchars($vacante['descripcion'])); ?></div>

                    <h4 style="font-family:'Montserrat'; font-size:1rem; margin-bottom:15px; color: var(--text-main);">Información del Reclutador</h4>
                    <div class="info-grid" style="background:#F1F5F9; padding:20px; border-radius:8px;">
                        <div class="info-item"><label>Email</label><span><?php echo htmlspecialchars($vacante['email_contacto']); ?></span></div>
                        <div class="info-item"><label>RFC Empresa</label><span><?php echo htmlspecialchars($vacante['rfc'] ?? 'N/A'); ?></span></div>
                        <div class="info-item"><label>Sitio Web</label>
                            <span>
                                <?php if($vacante['sitio_web']): ?>
                                    <a href="<?php echo htmlspecialchars($vacante['sitio_web']); ?>" target="_blank" style="color:var(--js-red); font-weight:600; text-decoration:none;">
                                        Ver sitio <i class="fas fa-external-link-alt" style="font-size:0.8rem; margin-left:3px;"></i>
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="actions-bar">
                        <a href="gestionar_vacantes_admin.php" class="btn-saas btn-outline">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>

                        <?php if ($vacante['estado_vacante'] === 'abierta'): ?>
                            <a href="procesar_admin.php?action=cerrar_vacante&id=<?php echo $vacante['id_vacante']; ?>" 
                               class="btn-saas btn-danger" onclick="return confirm('¿Seguro que deseas cerrar esta vacante de forma administrativa?');">
                                <i class="fas fa-lock"></i> Cerrar Vacante
                            </a>
                        <?php else: ?>
                            <a href="procesar_admin.php?action=abrir_vacante&id=<?php echo $vacante['id_vacante']; ?>" 
                               class="btn-saas btn-success" onclick="return confirm('¿Deseas reabrir esta vacante para que los alumnos la vean de nuevo?');">
                                <i class="fas fa-unlock"></i> Reabrir Vacante
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>
        </main>
    </div>
</body>
</html>