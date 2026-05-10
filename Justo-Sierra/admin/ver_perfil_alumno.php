<?php
// --------------------------------------------------
// VER/ACTUALIZAR PERFIL ALUMNO - REDISEÑO SAAS (DINÁMICO)
// --------------------------------------------------
session_start();

// Ambos administradores pueden entrar
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
    header("Location: ../login.php");
    exit();
}

$id_alumno = $_GET['id'] ?? null;
if (!$id_alumno || !is_numeric($id_alumno)) {
    header("Location: gestionar_alumnos.php?status=error&msg=" . urlencode("ID de alumno inválido."));
    exit();
}

include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Administrador';
$tipo_admin = $_SESSION['tipo_admin'] ?? 'vinculacion'; // Detectamos el rol

$mensaje_update = '';
$tipo_mensaje = '';

// --- LÓGICA DE ACTUALIZACIÓN DE SEMESTRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_semestre'])) {
    $nuevo_semestre = $_POST['semestre'] ?? null;
    if (in_array($nuevo_semestre, ['7', '8'])) {
        try {
            $stmt_upd = $conexion->prepare("UPDATE alumnos SET semestre = :semestre WHERE id_alumno = :id");
            $stmt_upd->execute([':semestre' => $nuevo_semestre, ':id' => $id_alumno]);
            $mensaje_update = "Semestre actualizado a " . $nuevo_semestre . "mo exitosamente.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje_update = "Error: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    }
}

// --- CONSULTA DE DATOS DEL ALUMNO ---
try {
    $stmt = $conexion->prepare("SELECT * FROM alumnos WHERE id_alumno = :id");
    $stmt->execute([':id' => $id_alumno]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        header("Location: gestionar_alumnos.php?status=error&msg=No+encontrado");
        exit();
    }
    $cv_url = !empty($alumno['cv_url']) ? "../students/CVS/" . rawurlencode($alumno['cv_url']) : null;
} catch (PDOException $e) { die("Error de conexión."); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Alumno | <?php echo htmlspecialchars($alumno['nombre']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            /* Colores dinámicos dependiendo del admin */
            --js-theme: <?php echo ($tipo_admin === 'alumnos') ? '#3B82F6' : '#E60013'; ?>; 
            --js-theme-hover: <?php echo ($tipo_admin === 'alumnos') ? '#2563EB' : '#C40010'; ?>; 
            --bg-light: #F8FAFC; 
            --text-main: #1E293B; 
            --text-muted: #64748B; 
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); 
        }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }

        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .brand-box { display: flex; align-items: center; gap: 12px; color: var(--js-theme); }
        .brand-box h1 { margin: 0; font-family: 'Montserrat'; font-size: 1.3rem; }
        .btn-exit { background: #FEE2E2; color: #E60013; padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-exit:hover { background: #E60013; color: white; }

        .admin-wrapper { display: flex; min-height: calc(100vh - 70px); }
        .sidebar { width: 280px; background: white; padding: 30px 20px; border-right: 1px solid #E2E8F0; }
        .sidebar-profile { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #F1F5F9; }
        .sidebar-profile img { width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; padding: 5px; margin-bottom: 10px; }
        .sidebar-profile h3 { margin: 0; font-size: 0.95rem; font-family: 'Montserrat'; }

        .menu-group { margin-bottom: 25px; }
        .menu-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-left: 10px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: var(--text-muted); border-radius: 8px; font-weight: 500; transition: 0.2s; margin-bottom: 5px; }
        .nav-link:hover { background: <?php echo ($tipo_admin === 'alumnos') ? '#EFF6FF' : '#FFF1F2'; ?>; color: var(--js-theme); }
        .nav-link.active { background: var(--js-theme); color: white; }

        .main-container { flex: 1; padding: 40px; max-width: 1000px; margin: 0 auto; width: 100%; }
        .profile-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); }
        .profile-banner { background: var(--js-theme); height: 100px; }
        .profile-header { padding: 0 40px 30px; margin-top: -50px; text-align: center; border-bottom: 1px solid #F1F5F9; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; border: 5px solid white; background: white; margin: 0 auto 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px; padding: 40px; }
        .info-item label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
        .info-item span { font-size: 1.05rem; font-weight: 500; }

        .update-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 15px; display: flex; align-items: center; gap: 10px; margin-top: 10px; }
        select { padding: 8px; border-radius: 6px; border: 1px solid #CBD5E1; }
        .btn-saas { padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; border: none; font-size: 0.85rem; }
        .btn-primary { background: var(--js-theme); color: white; }
        .btn-outline { background: white; border: 1px solid #E2E8F0; color: var(--text-main); text-decoration: none; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px;}
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box">
            <i class="fas fa-university"></i>
            <h1>JUSTO SIERRA | <?php echo ($tipo_admin === 'alumnos') ? 'ESCOLARES' : 'ADMIN'; ?></h1>
        </div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span>
            <a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=<?php echo ($tipo_admin === 'alumnos') ? '3B82F6' : 'E60013'; ?>&color=fff" alt="User">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--text-muted);"><?php echo ($tipo_admin === 'alumnos') ? 'Servicios Escolares' : 'Administrador'; ?></small>
            </div>
            
            <nav>
                <?php if ($tipo_admin === 'alumnos'): ?>
                    <div class="menu-group">
                        <p class="menu-title">General</p>
                        <a href="dashboard_alumnos.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                        <a href="gestionar_alumnos.php" class="nav-link active"><i class="fas fa-user-graduate"></i> Padrón de Alumnos</a>
                    </div>
                <?php else: ?>
                    <div class="menu-group">
                        <p class="menu-title">General</p>
                        <a href="dashboard_admin.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                        <a href="gestionar_alumnos.php" class="nav-link active"><i class="fas fa-user-graduate"></i> Alumnos</a>
                    </div>
                    <div class="menu-group">
                        <p class="menu-title">Bolsa de Trabajo</p>
                        <a href="gestionar_empresas_admin.php" class="nav-link"><i class="fas fa-building"></i> Empresas</a>
                        <a href="gestionar_vacantes_admin.php" class="nav-link"><i class="fas fa-briefcase"></i> Vacantes</a>
                        <a href="gestionar_tramites_sspp.php" class="nav-link"><i class="fas fa-file-signature"></i> Trámites SSPP</a>
                    </div>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="main-container">
            <?php if ($mensaje_update): ?>
                <div class="alert alert-<?php echo ($tipo_mensaje === 'success') ? 'success' : 'error'; ?>">
                    <i class="fas <?php echo ($tipo_mensaje === 'success') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo $mensaje_update; ?>
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-banner"></div>
                <div class="profile-header">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($alumno['nombre']); ?>&background=random" class="profile-avatar">
                    <h2 style="margin:0; font-family:'Montserrat';"><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h2>
                    <p style="color:var(--text-muted);"><?php echo htmlspecialchars($alumno['email']); ?></p>
                </div>

                <div class="info-grid">
                    <div class="info-item"><label>Matrícula</label><span><?php echo htmlspecialchars($alumno['matricula']); ?></span></div>
                    <div class="info-item"><label>Carrera</label><span><?php echo htmlspecialchars($alumno['carrera'] ?: 'N/A'); ?></span></div>
                    <div class="info-item"><label>Registrado el</label><span><?php echo date('d/m/Y', strtotime($alumno['fecha_registro'])); ?></span></div>
                    <div class="info-item"><label>LinkedIn</label>
                        <span>
                            <?php if($alumno['perfil_linkedin']): ?>
                                <a href="<?php echo htmlspecialchars($alumno['perfil_linkedin']); ?>" target="_blank" style="color:#0077B5;"><i class="fab fa-linkedin"></i> Ver Perfil</a>
                            <?php else: ?> No disponible <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label>Semestre Actual</label>
                        <form method="POST" class="update-box">
                            <select name="semestre">
                                <option value="7" <?php if($alumno['semestre'] == 7) echo 'selected'; ?>>7mo Semestre</option>
                                <option value="8" <?php if($alumno['semestre'] == 8) echo 'selected'; ?>>8vo Semestre</option>
                            </select>
                            <button type="submit" name="actualizar_semestre" class="btn-saas btn-primary">Actualizar</button>
                        </form>
                    </div>

                    <div class="info-item">
                        <label>Documentación</label>
                        <div style="margin-top:10px;">
                            <?php if($cv_url): ?>
                                <a href="<?php echo $cv_url; ?>" target="_blank" class="btn-saas btn-outline" style="color:var(--js-theme);"><i class="fas fa-file-pdf"></i> Descargar CV</a>
                            <?php else: ?>
                                <span style="color: #991B1B; font-size:0.9rem;"><i class="fas fa-exclamation-triangle"></i> CV no subido</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="padding: 20px 40px; background: #F1F5F9; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                    <a href="gestionar_alumnos.php" class="btn-saas btn-outline"><i class="fas fa-arrow-left"></i> Volver a Gestión</a>
                    <span style="color:var(--text-muted); font-size:0.8rem;">ID Alumno: #<?php echo $alumno['id_alumno']; ?></span>
                </div>
            </div>
        </main>
    </div>
</body>
</html>