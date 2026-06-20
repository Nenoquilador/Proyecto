<?php
$page_title = $page_title ?? 'Panel de Escolares | Justo Sierra';
$current_page = $current_page ?? 'dashboard';
$breadcrumb = $breadcrumb ?? 'Resumen';
$nombre_admin_show = $_SESSION['nombre_admin'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_alumnos.css">
</head>
<body>

    <!-- ─── SIDEBAR ─────────────────────────────── -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-university"></i></div>
            <div class="brand-text">
                <span>Justo Sierra</span>
                <span>Servicios Escolares</span>
            </div>
        </div>

        <div class="sidebar-profile">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin_show); ?>&background=E60013&color=fff&bold=true&size=150" alt="Perfil">
            <div class="profile-info">
                <span><?php echo htmlspecialchars($nombre_admin_show); ?></span>
                <span>Escolares</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-label">Panel de Control</p>
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span> Resumen
            </a>
            <a href="gestionar_alumnos.php" class="nav-link <?php echo ($current_page == 'gestionar_alumnos') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-user-graduate"></i></span> Directorio Alumnos
            </a>
            <a href="carga_masiva.php" class="nav-link <?php echo ($current_page == 'carga_masiva') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-upload"></i></span> Carga Masiva
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../../logout.php" class="btn-exit">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Cerrar sesión
            </a>
        </div>
    </aside>

    <!-- ─── MAIN ───────────────────────────────── -->
    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-left">
                <div class="breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <span class="current"><?php echo htmlspecialchars($breadcrumb); ?></span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="today-date"></span>
                </div>
            </div>
        </header>

        <main class="page-content">
