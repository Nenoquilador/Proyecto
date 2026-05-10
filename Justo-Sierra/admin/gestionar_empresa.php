<?php
// --------------------------------------------------
// REVISIÓN Y GESTIÓN DE EMPRESA - REDISEÑO SAAS
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}

include '../config/conexion.php'; 

$id_empresa = $_GET['id'] ?? null;
$nombre_admin = $_SESSION['nombre_admin'] ?? 'Admin';
$mensaje = '';
$tipo_mensaje = '';

if (!$id_empresa || !is_numeric($id_empresa)) {
    header("Location: dashboard_admin.php");
    exit();
}

// --- LÓGICA DE PROCESAMIENTO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
    $es_sspp = ($accion === 'aprobar') ? 1 : 0;

    try {
        // Actualizamos estado de validación y campos de vigencia SSPP
        $sql = "UPDATE empresas SET 
                estado_validacion = :nuevo_estado, 
                es_catalogo_sspp = :es_sspp,
                vigencia_sspp = IF(:accion = 'aprobar', DATE_ADD(CURDATE(), INTERVAL 3 YEAR), NULL)
                WHERE id_empresa = :id_empresa";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nuevo_estado' => $nuevo_estado,
            ':es_sspp' => $es_sspp,
            ':accion' => $accion,
            ':id_empresa' => $id_empresa
        ]);

        header("Location: dashboard_admin.php?status=success&msg=" . urlencode("La empresa ha sido " . ($accion === 'aprobar' ? "aprobada" : "rechazada") . " con éxito."));
        exit();
    } catch (PDOException $e) {
        $mensaje = "Error de base de datos: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// --- CONSULTA DE DATOS DE LA EMPRESA ---
try {
    $stmt = $conexion->prepare("SELECT * FROM empresas WHERE id_empresa = :id");
    $stmt->execute([':id' => $id_empresa]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) { header("Location: dashboard_admin.php"); exit(); }
} catch (PDOException $e) { die("Error de conexión."); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Empresa | <?php echo htmlspecialchars($empresa['nombre_empresa']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --js-red: #E60013; --bg-light: #F8FAFC; --text-main: #1E293B; --text-muted: #64748B; --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }
        
        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
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

        .main-container { flex: 1; padding: 40px; max-width: 900px; margin: 0 auto; }
        .profile-card { background: white; border-radius: 12px; padding: 40px; box-shadow: var(--shadow); }
        
        .profile-header { border-bottom: 1px solid #F1F5F9; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
        .profile-header h2 { font-family: 'Montserrat'; margin: 0; font-size: 1.8rem; color: var(--js-red); }
        
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 30px; }
        .info-item label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px; }
        .info-item span { font-size: 1rem; font-weight: 500; }

        .desc-box { background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 30px; line-height: 1.6; }

        .actions-bar { display: flex; justify-content: center; gap: 20px; padding-top: 30px; border-top: 1px solid #F1F5F9; }
        .btn-saas { padding: 12px 25px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.2s; }
        .btn-approve { background: #10B981; color: white; }
        .btn-reject { background: #FEE2E2; color: var(--js-red); }
        .btn-outline { background: white; border: 1px solid #E2E8F0; color: var(--text-muted); }
        .btn-outline:hover { background: #F1F5F9; color: var(--text-main); }
        .btn-exit { background: #FEE2E2; color: var(--js-red); padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-exit:hover { background: var(--js-red); color: white; }
    </style>
</head>
<body>

    <header class="top-header">
        <div style="display:flex; align-items:center; gap:12px; color:var(--js-red);">
            <i class="fas fa-university" style="font-size:1.8rem;"></i>
            <h1 style="margin:0; font-family:'Montserrat'; font-size:1.3rem;">JUSTO SIERRA | ADMIN</h1>
        </div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span>
            <a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff" alt="User">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--text-muted);">Administrador</small>
            </div>
            <nav>
                <div class="menu-group">
                    <p class="menu-title">General</p>
                    <a href="dashboard_admin.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                    <a href="gestionar_alumnos.php" class="nav-link"><i class="fas fa-user-graduate"></i> Alumnos</a>
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
            <div class="profile-card">
                <div class="profile-header">
                    <h2>Revisión de Registro</h2>
                    <p style="color:var(--text-muted); margin-top:5px;">Verifica la autenticidad de la empresa antes de activarla.</p>
                </div>

                <div class="info-grid">
                    <div class="info-item"><label>Nombre Comercial</label><span><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></span></div>
                    <div class="info-item"><label>Email de Contacto</label><span><?php echo htmlspecialchars($empresa['email_contacto']); ?></span></div>
                    <div class="info-item"><label>RFC</label><span><?php echo htmlspecialchars($empresa['rfc'] ?? 'No registrado'); ?></span></div>
                    <div class="info-item"><label>Sitio Web</label>
                        <span>
                            <?php if (!empty($empresa['sitio_web'])): ?>
                                <a href="<?php echo htmlspecialchars($empresa['sitio_web']); ?>" target="_blank" style="color:var(--js-red);">
                                    <?php echo htmlspecialchars($empresa['sitio_web']); ?> <i class="fas fa-external-link-alt" style="font-size:0.8rem; margin-left: 3px;"></i>
                                </a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <label style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Descripción de la Empresa</label>
                <div class="desc-box"><?php echo nl2br(htmlspecialchars($empresa['descripcion'])); ?></div>

                <div class="actions-bar">
                    <a href="gestionar_empresas_admin.php" class="btn-saas btn-outline"><i class="fas fa-arrow-left"></i> Cancelar / Regresar</a>
                    
                    <form method="POST" onsubmit="return confirm('¿Rechazar este registro de empresa?');">
                        <input type="hidden" name="accion" value="rechazar">
                        <button type="submit" class="btn-saas btn-reject"><i class="fas fa-times"></i> Rechazar</button>
                    </form>

                    <form method="POST" onsubmit="return confirm('¿Aprobar esta empresa? Esto le permitirá iniciar su trámite SSPP.');">
                        <input type="hidden" name="accion" value="aprobar">
                        <button type="submit" class="btn-saas btn-approve"><i class="fas fa-check"></i> Aprobar Empresa</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>