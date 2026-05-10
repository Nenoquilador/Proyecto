<?php
session_start();
// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Admin Vinculación UJS';
$search = $_GET['search'] ?? '';

// Consulta de vacantes con filtro de búsqueda
$sql = "SELECT v.id_vacante, v.titulo, e.nombre_empresa, v.estado, v.fecha_publicacion 
        FROM vacantes v JOIN empresas e ON v.id_empresa = e.id_empresa";
if($search) {
    $sql .= " WHERE v.titulo LIKE :s OR e.nombre_empresa LIKE :s";
}
$sql .= " ORDER BY v.fecha_publicacion DESC";

$stmt = $conexion->prepare($sql);
if($search) {
    $stmt->execute([':s' => "%$search%"]); 
} else {
    $stmt->execute();
}
$vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Publicadas | Justo Sierra</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --js-red: #E60013; 
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

        /* CONTENIDO PRINCIPAL */
        .main-container { flex: 1; padding: 40px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .search-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 15px; align-items: center; }
        
        .table-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F1F5F9; padding: 18px 20px; text-align: left; font-size: 0.85rem; text-transform: uppercase; color: #64748B; font-weight: 700; }
        td { padding: 18px 20px; border-bottom: 1px solid #F1F5F9; color: var(--text-main); font-size: 0.95rem; }
        tr:hover { background-color: #F8FAFC; }
        
        .status-pill { padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; text-transform: capitalize; }
        .open { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .closed { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        
        .btn-action { color: var(--js-red); text-decoration: none; font-weight: 600; padding: 8px 12px; border-radius: 6px; transition: 0.2s; }
        .btn-action:hover { background: #FFF1F2; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box">
            <i class="fas fa-university"></i>
            <h1>JUSTO SIERRA | ADMIN</h1>
        </div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span>
            <a href="../logout.php" class="btn-exit">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
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
            <div style="margin-bottom: 30px;">
                <h2 style="margin:0; font-family:'Montserrat'; font-size: 1.8rem;">Vacantes de la Bolsa</h2>
                <p style="color: #64748B; margin: 5px 0 0 0;">Supervisa las ofertas laborales publicadas por las empresas.</p>
            </div>

            <div class="search-card">
                <form method="GET" style="display: flex; gap: 15px; width: 100%;">
                    <div style="flex: 1; position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #94A3B8;"></i>
                        <input type="text" name="search" placeholder="Buscar por puesto o empresa..." value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #E2E8F0; border-radius: 8px; font-family: 'Roboto'; box-sizing: border-box; font-size: 1rem;">
                    </div>
                    <button type="submit" style="background: var(--js-red); color: white; border: none; padding: 0 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s;">
                        Buscar
                    </button>
                    <?php if($search): ?>
                        <a href="gestionar_vacantes_admin.php" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center;">Limpiar</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-card">
                <?php if(empty($vacantes)): ?>
                    <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                        <i class="fas fa-box-open" style="font-size: 2.5rem; color: #CBD5E1; margin-bottom: 10px;"></i>
                        <p>No se encontraron vacantes con los criterios actuales.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Puesto Ofertado</th>
                                <th>Empresa Ofertante</th>
                                <th>Estado</th>
                                <th>Fecha Publicación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vacantes as $v): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($v['titulo']); ?></td>
                                    <td>
                                        <i class="far fa-building" style="color: #94A3B8; margin-right: 5px;"></i> 
                                        <?php echo htmlspecialchars($v['nombre_empresa']); ?>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo strtolower($v['estado']) == 'abierta' ? 'open' : 'closed'; ?>">
                                            <?php echo htmlspecialchars($v['estado']); ?>
                                        </span>
                                    </td>
                                    <td style="color: #64748B; font-size: 0.9rem;">
                                        <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                        <?php echo date('d M, Y', strtotime($v['fecha_publicacion'])); ?>
                                    </td>
                                    <td>
                                        <a href="detalle_vacante_admin.php?id=<?php echo $v['id_vacante']; ?>" class="btn-action">
                                            Revisar <i class="fas fa-chevron-right" style="font-size: 0.8em; margin-left: 3px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
        </main>
    </div>
</body>
</html>