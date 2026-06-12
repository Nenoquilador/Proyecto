<?php
session_start();

// CANDADO ESTRICTO: SOLO ESCOLARES
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
    header("Location: ../login.php");
    exit();
}
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Servicios Escolares';

$search_term = $_GET['search'] ?? '';
$filtro_carrera = $_GET['carrera'] ?? '';

// Obtener carreras para el filtro
try {
    $lista_carreras = $conexion->query("SELECT DISTINCT carrera FROM alumnos WHERE carrera != ''")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { $lista_carreras = []; }

// Consulta de alumnos con filtros
$sql = "SELECT id_alumno, nombre, apellidos, matricula, carrera, email, cv_url FROM alumnos WHERE 1=1";
$params = [];
if (!empty($search_term)) {
    $sql .= " AND (nombre LIKE :search OR apellidos LIKE :search OR matricula LIKE :search)";
    $params[':search'] = "%$search_term%";
}
if (!empty($filtro_carrera)) {
    $sql .= " AND carrera = :carrera";
    $params[':carrera'] = $filtro_carrera;
}
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos | Justo Sierra</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --js-primary: #E60013;
            --js-secondary: #FCC800;
            --js-accent: #EA0029;
            --js-primary-hover: #C40010;
            --js-primary-light: #FFF1F2;
            --js-gradient: linear-gradient(60deg, #E60013 0%, #FCC800 65%, #EA0029 100%);
            --bg-light: #F8FAFC; 
            --text-main: #1E293B; 
            --text-muted: #64748B; 
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); 
        }
        
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }

        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); position: sticky; top: 0; z-index: 100; }
        .brand-box { display: flex; align-items: center; gap: 12px; color: var(--js-primary); }
        .brand-box h1 { margin: 0; font-family: 'Montserrat', sans-serif; font-size: 1.3rem; }
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
        .nav-link:hover { background: var(--js-primary-light); color: var(--js-primary); }
        .nav-link.active { background: var(--js-gradient); color: white; }

        .main-container { flex: 1; padding: 40px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .search-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .grid-alumnos { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .student-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 5px solid var(--js-primary); transition: 0.3s; display: flex; flex-direction: column; }
        .student-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        
        .btn-view { background: #F1F5F9; color: #1E293B; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-block; margin-top: auto; align-self: flex-start; transition: 0.2s; }
        .btn-view:hover { background: var(--js-primary); color: white; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box">
            <i class="fas fa-university"></i>
            <h1>JUSTO SIERRA | ESCOLARES</h1>
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
                <small style="color: var(--text-muted);">Servicios Escolares</small>
            </div>

            <nav>
                <div class="menu-group">
                    <p class="menu-title">General</p>
                    <a href="dashboard_alumnos.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                    <a href="gestionar_alumnos.php" class="nav-link active"><i class="fas fa-user-graduate"></i> Padrón de Alumnos</a>
                </div>
            </nav>
        </aside>

        <main class="main-container">
            <div style="margin-bottom: 30px;">
                <h2 style="margin:0; font-family:'Montserrat'; font-size: 1.8rem;">Padrón de Alumnos</h2>
                <p style="color: #64748B; margin: 5px 0 0 0;">Consulta y valida el directorio estudiantil.</p>
            </div>

            <div class="search-card">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <input type="text" name="search" placeholder="Nombre o Matrícula..." value="<?php echo htmlspecialchars($search_term); ?>" style="flex:1; min-width: 200px; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-family: 'Roboto';">
                    
                    <select name="carrera" style="padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; min-width: 250px; font-family: 'Roboto';">
                        <option value="">Todas las Carreras</option>
                        <?php foreach($lista_carreras as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $filtro_carrera == $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" style="background: var(--js-primary); color: white; border: none; padding: 0 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s;">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </div>

            <?php if (empty($alumnos)): ?>
                <div style="background: white; padding: 40px; text-align: center; border-radius: 12px; color: var(--text-muted);">
                    <i class="fas fa-users-slash" style="font-size: 2.5rem; margin-bottom: 10px; color: #CBD5E1;"></i>
                    <p>No se encontraron alumnos con esos criterios de búsqueda.</p>
                </div>
            <?php else: ?>
                <div class="grid-alumnos">
                    <?php foreach($alumnos as $a): ?>
                        <div class="student-card">
                            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-bottom: 5px;">
                                <?php echo htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']); ?>
                            </div>
                            <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 5px;">
                                <i class="fas fa-id-card" style="width: 15px;"></i> <?php echo htmlspecialchars($a['matricula']); ?>
                            </div>
                            <div style="color: var(--js-primary); font-weight: 500; font-size: 0.85rem; margin-bottom: 15px;">
                                <i class="fas fa-graduation-cap" style="width: 15px;"></i> <?php echo htmlspecialchars($a['carrera']); ?>
                            </div>
                            
                            <a href="ver_perfil_alumno.php?id=<?php echo $a['id_alumno']; ?>" class="btn-view">
                                Ver Perfil Completo
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </main>
    </div>
</body>
</html>