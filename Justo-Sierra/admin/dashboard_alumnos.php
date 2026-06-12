<?php
// --------------------------------------------------
// DASHBOARD EXCLUSIVO PARA ADMINISTRADOR DE ALUMNOS
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: Solo entra si es admin y su tipo es 'alumnos'
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
    header("Location: ../login.php");
    exit();
}

include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Servicios Escolares';

// Estadísticas exclusivas de alumnos
try {
    $count_alumnos = $conexion->query("SELECT COUNT(*) FROM alumnos")->fetchColumn();
    $count_cvs = $conexion->query("SELECT COUNT(*) FROM alumnos WHERE cv_url IS NOT NULL AND cv_url != ''")->fetchColumn();
} catch (PDOException $e) { 
    $error_db = $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Escolares | Justo Sierra</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --js-primary: #E60013;
            --js-secondary: #FCC800;
            --js-accent: #EA0029;
            --js-primary-dark: #C40010;
            --js-primary-light: #FFF1F2;
            --js-gradient: linear-gradient(60deg, #E60013 0%, #FCC800 65%, #EA0029 100%);
            --bg-light: #F8FAFC; 
            --text-main: #0F172A; 
            --text-muted: #64748B; 
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }
        
        /* HEADER */
        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid #E2E8F0; }
        .brand-box { display: flex; align-items: center; gap: 12px; color: var(--js-primary); }
        .brand-box h1 { margin: 0; font-family: 'Montserrat', sans-serif; font-size: 1.3rem; letter-spacing: -0.5px; }
        .btn-exit { background: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .btn-exit:hover { background: #DC2626; color: white; }
        
        .admin-wrapper { display: flex; min-height: calc(100vh - 70px); }
        
        /* SIDEBAR */
        .sidebar { width: 280px; background: white; padding: 30px 20px; border-right: 1px solid #E2E8F0; display: flex; flex-direction: column; }
        .sidebar-profile { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #F1F5F9; }
        .sidebar-profile img { width: 70px; height: 70px; border-radius: 50%; background: #F1F5F9; padding: 4px; margin-bottom: 12px; box-shadow: var(--shadow-sm); border: 2px solid white; }
        .sidebar-profile h3 { margin: 0; font-size: 1rem; font-family: 'Montserrat', sans-serif; color: var(--text-main); }
        .menu-group { margin-bottom: 25px; }
        .menu-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; padding-left: 15px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: var(--text-muted); border-radius: 10px; font-weight: 500; transition: all 0.2s; margin-bottom: 5px; font-size: 0.95rem; }
        .nav-link:hover { background: var(--js-primary-light); color: var(--js-primary); transform: translateX(5px); }
        .nav-link.active { background: var(--js-gradient); color: white; box-shadow: 0 4px 6px -1px rgba(230, 0, 19, 0.2); }
        
        /* MAIN CONTENT */
        .main-container { flex: 1; padding: 40px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        /* HERO BANNER */
        .welcome-hero { background: var(--js-gradient); border-radius: 20px; padding: 40px; color: white; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; box-shadow: var(--shadow-md); position: relative; overflow: hidden; }
        .welcome-hero::after { content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; }
        .welcome-text { position: relative; z-index: 1; max-width: 600px; }
        .welcome-text h2 { margin: 0 0 10px 0; font-family: 'Montserrat', sans-serif; font-size: 2.2rem; font-weight: 800; }
        .welcome-text p { margin: 0; font-size: 1.05rem; opacity: 0.9; line-height: 1.5; }
        .welcome-icon { font-size: 5rem; opacity: 0.2; transform: rotate(-15deg); position: relative; z-index: 1; }

        /* STATS GRID */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 30px; border-radius: 16px; display: flex; align-items: center; gap: 25px; box-shadow: var(--shadow-sm); border: 1px solid #E2E8F0; transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: #CBD5E1; }
        .icon-wrap { width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
        .card-info h4 { margin: 0; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .card-info .count { font-size: 2.2rem; font-weight: 800; display: block; margin-top: 5px; color: var(--text-main); font-family: 'Montserrat', sans-serif; }
        
        .stat-card.blue .icon-wrap { background: var(--js-primary-light); color: var(--js-primary); }
        .stat-card.green .icon-wrap { background: #ECFDF5; color: #10B981; }

        /* ACCIONES RÁPIDAS */
        .section-title { font-family: 'Montserrat', sans-serif; font-size: 1.3rem; margin-bottom: 20px; color: var(--text-main); }
        .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .action-card { background: white; border: 1px solid #E2E8F0; border-radius: 16px; padding: 25px; text-decoration: none; color: var(--text-main); display: flex; flex-direction: column; gap: 15px; transition: all 0.2s ease; }
        .action-card:hover { border-color: var(--js-primary); background: var(--js-primary-light); box-shadow: var(--shadow-sm); }
        .action-card .action-icon { width: 45px; height: 45px; background: var(--bg-light); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--js-primary); }
        .action-card:hover .action-icon { background: white; }
        .action-card h4 { margin: 0; font-size: 1.05rem; font-family: 'Montserrat', sans-serif; }
        .action-card p { margin: 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.4; }

    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box">
            <i class="fas fa-university" style="font-size: 1.5rem;"></i>
            <h1>JUSTO SIERRA | ESCOLARES</h1>
        </div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px; color: var(--text-muted); font-size: 0.95rem;">
                <i class="far fa-calendar-alt"></i> <?php echo date('d M, Y'); ?>
            </span>
            <a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff&bold=true" alt="Perfil">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--js-primary); font-weight: 600; font-size: 0.8rem;">Administrador Estudiantil</small>
            </div>
            <nav>
                <div class="menu-group">
                    <p class="menu-title">Panel de Control</p>
                    <a href="dashboard_alumnos.php" class="nav-link active"><i class="fas fa-home" style="width: 20px;"></i> Inicio</a>
                    <a href="gestionar_alumnos.php" class="nav-link"><i class="fas fa-users" style="width: 20px;"></i> Directorio Alumnos</a>
                </div>
            </nav>
        </aside>

        <main class="main-container">
            
            <div class="welcome-hero">
                <div class="welcome-text">
                    <h2>¡Hola, <?php echo explode(' ', trim(htmlspecialchars($nombre_admin)))[0]; ?>! 👋</h2>
                    <p>Bienvenido al Centro de Control de Servicios Escolares. Aquí tienes una vista rápida del estado de la comunidad estudiantil.</p>
                </div>
                <div class="welcome-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="icon-wrap"><i class="fas fa-user-graduate"></i></div>
                    <div class="card-info">
                        <h4>Alumnos Registrados</h4>
                        <span class="count"><?php echo number_format($count_alumnos); ?></span>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="icon-wrap"><i class="fas fa-file-invoice"></i></div>
                    <div class="card-info">
                        <h4>Currículums en Sistema</h4>
                        <span class="count"><?php echo number_format($count_cvs); ?></span>
                    </div>
                </div>
            </div>
            
            <h3 class="section-title">Acciones Rápidas</h3>
            <div class="actions-grid">
                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-search"></i></div>
                    <div>
                        <h4>Buscar Expediente</h4>
                        <p>Encuentra a un estudiante rápidamente por su nombre o matrícula institucional.</p>
                    </div>
                </a>
                
                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-sync-alt"></i></div>
                    <div>
                        <h4>Actualizar Semestres</h4>
                        <p>Verifica y modifica el avance académico de los estudiantes dados de alta.</p>
                    </div>
                </a>

                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-file-download"></i></div>
                    <div>
                        <h4>Revisar Documentación</h4>
                        <p>Descarga y revisa los currículums vitae (CV) que los alumnos han subido.</p>
                    </div>
                </a>
            </div>

        </main>
    </div>
</body>
</html>