<?php
// --------------------------------------------------
// DIRECTORIO DE EMPRESAS - ADMIN (CON VIGENCIA)
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Admin Vinculación UJS';
$search = $_GET['search'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construir la consulta con filtros
$sql = "SELECT id_empresa, nombre_empresa, email_contacto, estado_validacion, es_catalogo_sspp, vigencia_sspp, fecha_registro 
        FROM empresas WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (nombre_empresa LIKE :s OR email_contacto LIKE :s)";
    $params[':s'] = "%$search%";
}
if (!empty($filtro_estado)) {
    $sql .= " AND estado_validacion = :e";
    $params[':e'] = $filtro_estado;
}
$sql .= " ORDER BY fecha_registro DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Empresas | Justo Sierra</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --js-red: #E60013; --bg-light: #F8FAFC; --text-main: #1E293B; --text-muted: #64748B; --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }

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

        .main-container { flex: 1; padding: 40px; max-width: 1200px; margin: 0 auto; width: 100%; }
        .search-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .grid-empresas { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; }
        .company-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 5px solid #E2E8F0; transition: 0.3s; display: flex; flex-direction: column; position: relative;}
        .company-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
        
        .status-aprobada { border-left-color: #10B981; }
        .status-pendiente { border-left-color: #F59E0B; }
        .status-rechazada { border-left-color: #EF4444; }

        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 12px; width: fit-content;}
        .bg-green { background: #DCFCE7; color: #166534; } 
        .bg-yellow { background: #FEF3C7; color: #B45309; }
        .bg-red { background: #FEE2E2; color: #991B1B; }

        .vigencia-tag { font-size: 0.75rem; font-weight: 600; margin-top: 10px; padding: 6px 12px; border-radius: 8px; display: flex; align-items: center; gap: 6px; }
        .vigencia-ok { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
        .vigencia-expired { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        .btn-view { background: #F1F5F9; color: var(--text-main); padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: block; text-align: center; margin-top: 20px; transition: 0.2s; }
        .btn-view:hover { background: var(--js-red); color: white; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box"><i class="fas fa-university"></i><h1>JUSTO SIERRA | ADMIN</h1></div>
        <div class="user-nav"><span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span><a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a></div>
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
                <h2 style="margin:0; font-family:'Montserrat'; font-size: 1.8rem;">Directorio de Empresas</h2>
                <p style="color: #64748B; margin: 5px 0 0 0;">Busca y gestiona todas las empresas registradas en la plataforma.</p>
            </div>

            <div class="search-card">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="flex: 1; position: relative; min-width: 250px;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #94A3B8;"></i>
                        <input type="text" name="search" placeholder="Nombre o correo..." value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #E2E8F0; border-radius: 8px; font-family: 'Roboto'; box-sizing: border-box;">
                    </div>
                    
                    <select name="estado" style="padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; min-width: 200px; font-family: 'Roboto';">
                        <option value="">Todos los Estados</option>
                        <option value="aprobada" <?php echo $filtro_estado == 'aprobada' ? 'selected' : ''; ?>>Aprobadas</option>
                        <option value="pendiente" <?php echo $filtro_estado == 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                        <option value="rechazada" <?php echo $filtro_estado == 'rechazada' ? 'selected' : ''; ?>>Rechazadas</option>
                    </select>
                    
                    <button type="submit" style="background: var(--js-red); color: white; border: none; padding: 0 25px; border-radius: 8px; font-weight: 700; cursor: pointer;">Buscar</button>
                    <?php if($search || $filtro_estado): ?>
                        <a href="gestionar_empresas_admin.php" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center;">Limpiar</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="grid-empresas">
                <?php foreach($empresas as $emp): 
                    $clase_estado = 'status-' . strtolower($emp['estado_validacion']);
                    $clase_badge = ($emp['estado_validacion'] == 'aprobada') ? 'bg-green' : (($emp['estado_validacion'] == 'pendiente') ? 'bg-yellow' : 'bg-red');
                ?>
                    <div class="company-card <?php echo $clase_estado; ?>">
                        <span class="badge <?php echo $clase_badge; ?>"><?php echo htmlspecialchars($emp['estado_validacion']); ?></span>
                        
                        <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-bottom: 5px;">
                            <?php echo htmlspecialchars($emp['nombre_empresa']); ?>
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 10px;">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($emp['email_contacto']); ?>
                        </div>

                        <?php if ($emp['es_catalogo_sspp'] && !empty($emp['vigencia_sspp'])): 
                            $fecha_venc = new DateTime($emp['vigencia_sspp']);
                            $hoy = new DateTime();
                            $diff = $hoy->diff($fecha_venc);
                            $vencida = $diff->invert;
                            
                            if ($vencida) {
                                $texto_v = "Vigencia expirada";
                                $clase_v = "vigencia-expired";
                                $icon_v = "fa-times-circle";
                            } else {
                                $clase_v = "vigencia-ok";
                                $icon_v = "fa-hourglass-half";
                                if ($diff->y > 0) $texto_v = "Resta: " . $diff->y . " año(s) y " . $diff->m . " mes(es)";
                                elseif ($diff->m > 0) $texto_v = "Resta: " . $diff->m . " mes(es)";
                                else $texto_v = "Resta: " . $diff->d . " día(s)";
                            }
                        ?>
                            <div class="vigencia-tag <?php echo $clase_v; ?>">
                                <i class="fas <?php echo $icon_v; ?>"></i>
                                <div>
                                    <span style="display:block; font-size:0.65rem; text-transform:uppercase; opacity:0.8;">Vence: <?php echo date('d/m/Y', strtotime($emp['vigencia_sspp'])); ?></span>
                                    <strong><?php echo $texto_v; ?></strong>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="height: 45px;"></div> <?php endif; ?>
                        
                        <a href="gestionar_empresa.php?id=<?php echo $emp['id_empresa']; ?>" class="btn-view">
                            <i class="fas fa-user-edit"></i> Gestionar Perfil
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($empresas)): ?>
                <div style="background: white; padding: 40px; text-align: center; border-radius: 12px; color: var(--text-muted);">
                    <i class="fas fa-building-slash" style="font-size: 2.5rem; margin-bottom: 10px; color: #CBD5E1;"></i>
                    <p>No se encontraron empresas con esos criterios.</p>
                </div>
            <?php endif; ?>
            
        </main>
    </div>
</body>
</html>