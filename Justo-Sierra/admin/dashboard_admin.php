<?php
// --------------------------------------------------
// DASHBOARD ADMINISTRADOR - REDISEÑO SAAS (DOS COLUMNAS)
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}

include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación Universitaria';
$error_db = null;
$status_msg = $_GET['msg'] ?? null;
$status_type = $_GET['status'] ?? null;

// 1. Estadísticas Generales
try {
    $count_alumnos = $conexion->query("SELECT COUNT(*) FROM alumnos")->fetchColumn();
    $count_vacantes = $conexion->query("SELECT COUNT(*) FROM vacantes WHERE estado = 'abierta'")->fetchColumn();
    $count_pendientes = $conexion->query("SELECT COUNT(*) FROM empresas WHERE estado_validacion = 'pendiente'")->fetchColumn();
    $count_sspp = $conexion->query("SELECT COUNT(*) FROM solicitudes_sspp WHERE estado_tramite != 'Aprobado Catálogo'")->fetchColumn();
} catch (PDOException $e) {
    $error_db = $e->getMessage();
}

// 2. Empresas Pendientes (Recién registradas)
$empresas_pendientes = [];
try {
    $stmt_p = $conexion->prepare("SELECT id_empresa, nombre_empresa, email_contacto, fecha_registro FROM empresas WHERE estado_validacion = 'pendiente' ORDER BY fecha_registro ASC LIMIT 5");
    $stmt_p->execute();
    $empresas_pendientes = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db .= " | " . $e->getMessage();
}

// 3. Trámites SSPP en Proceso
$tramites_sspp = [];
try {
    $stmt_s = $conexion->prepare("SELECT s.id_solicitud, e.nombre_empresa, s.estado_tramite, s.fecha_inicio FROM solicitudes_sspp s JOIN empresas e ON s.id_empresa = e.id_empresa WHERE s.estado_tramite != 'Aprobado Catálogo' ORDER BY s.fecha_inicio DESC LIMIT 5");
    $stmt_s->execute();
    $tramites_sspp = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db .= " | " . $e->getMessage();
}

// 4. Empresas Dadas de Alta (Activas)
$empresas_activas = [];
try {
    $stmt_act = $conexion->prepare("SELECT id_empresa, nombre_empresa, email_contacto, es_catalogo_sspp, vigencia_sspp FROM empresas WHERE estado_validacion = 'aprobada' ORDER BY nombre_empresa ASC");
    $stmt_act->execute();
    $empresas_activas = $stmt_act->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db .= " | " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo | Justo Sierra</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
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

        body {
            background-color: var(--bg-light);
            font-family: 'Roboto', sans-serif;
            margin: 0;
            color: var(--text-main);
        }

        .top-header {
            background: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--js-red);
        }

        .brand-box h1 {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
        }

        .btn-exit {
            background: #FEE2E2;
            color: var(--js-red);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-exit:hover {
            background: var(--js-red);
            color: white;
        }

        .admin-wrapper {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .sidebar {
            width: 280px;
            background: white;
            padding: 30px 20px;
            border-right: 1px solid #E2E8F0;
        }

        .sidebar-profile {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #F1F5F9;
        }

        .sidebar-profile img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #F1F5F9;
            padding: 5px;
            margin-bottom: 10px;
        }

        .sidebar-profile h3 {
            margin: 0;
            font-size: 0.95rem;
            font-family: 'Montserrat';
        }

        .menu-group {
            margin-bottom: 25px;
        }

        .menu-title {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-left: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-muted);
            border-radius: 8px;
            font-weight: 500;
            transition: 0.2s;
            margin-bottom: 5px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #FFF1F2;
            color: var(--js-red);
        }

        .nav-link.active {
            background: var(--js-red);
            color: white;
        }

        .main-container {
            flex: 1;
            padding: 40px;
            max-width: 1400px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow);
            border-bottom: 4px solid transparent;
        }

        .icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .card-info h4 {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .card-info .count {
            font-size: 1.8rem;
            font-weight: 700;
            display: block;
            margin-top: 5px;
        }

        .stat-card.blue {
            border-color: #3B82F6;
        }

        .stat-card.blue .icon-wrap {
            background: #DBEAFE;
            color: #3B82F6;
        }

        .stat-card.red {
            border-color: var(--js-red);
        }

        .stat-card.red .icon-wrap {
            background: #FEE2E2;
            color: var(--js-red);
        }

        .stat-card.amber {
            border-color: #F59E0B;
        }

        .stat-card.amber .icon-wrap {
            background: #FEF3C7;
            color: #F59E0B;
        }

        /* Grid de dos columnas para el contenido inferior */
        .content-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .content-columns {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-section {
            margin-bottom: 30px;
        }

        .item-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-left: 5px solid #E2E8F0;
            transition: 0.3s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .item-card:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow);
        }

        .item-card.priority {
            border-left-color: var(--js-red);
        }

        .item-main {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .item-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-main);
        }

        .btn-action {
            background: #F1F5F9;
            color: var(--text-main);
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-action:hover {
            background: #E2E8F0;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .bg-green {
            background: #DCFCE7;
            color: #166534;
        }

        .bg-yellow {
            background: #FEF9C3;
            color: #854d0e;
        }
    </style>
</head>

<body>

    <header class="top-header">
        <div class="brand-box"><i class="fas fa-university"></i>
            <h1>JUSTO SIERRA | ADMIN</h1>
        </div>
        <div class="user-nav"><span style="font-weight: 500;"><?php echo date('d M, Y'); ?></span><a
                href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a></div>
    </header>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff"
                    alt="Perfil">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--text-muted);">Administrador</small>
            </div>
            <nav>
                <div class="menu-group">
                    <p class="menu-title">General</p>
                    <a href="dashboard_admin.php" class="nav-link active"><i class="fas fa-chart-pie"></i> Resumen</a>
                    <div class="menu-group">
                        <p class="menu-title">Bolsa de Trabajo</p>
                        <a href="gestionar_empresas_admin.php" class="nav-link"><i class="fas fa-building"></i>
                            Empresas</a>
                        <a href="gestionar_vacantes_admin.php" class="nav-link"><i class="fas fa-briefcase"></i>
                            Vacantes</a>
                        <a href="gestionar_tramites_sspp.php" class="nav-link"><i class="fas fa-file-signature"></i>
                            Trámites SSPP</a>
                    </div>
            </nav>
        </aside>

        <main class="main-container">
            <?php if ($status_msg): ?>
                <div
                    style="background: <?php echo ($status_type === 'success') ? '#DCFCE7' : '#FEE2E2'; ?>; color: <?php echo ($status_type === 'success') ? '#166534' : '#991B1B'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                    <i
                        class="fas <?php echo ($status_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars(urldecode($status_msg)); ?>
                </div>
            <?php endif; ?>

            <div class="page-title" style="margin-bottom: 30px;">
                <h2 style="margin:0; font-family:'Montserrat'; font-size: 1.8rem;">Panel de Control</h2>
                <p style="color: var(--text-muted); margin-top: 5px;">Gestiona las solicitudes de alumnos y empresas.
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="icon-wrap"><i class="fas fa-user-graduate"></i></div>
                    <div class="card-info">
                        <h4>Alumnos</h4><span class="count"><?php echo $count_alumnos; ?></span>
                    </div>
                </div>
                <div class="stat-card red">
                    <div class="icon-wrap"><i class="fas fa-briefcase"></i></div>
                    <div class="card-info">
                        <h4>Vacantes</h4><span class="count"><?php echo $count_vacantes; ?></span>
                    </div>
                </div>
                <div class="stat-card amber">
                    <div class="icon-wrap"><i class="fas fa-building"></i></div>
                    <div class="card-info">
                        <h4>Empresas</h4><span class="count"><?php echo $count_pendientes; ?></span>
                    </div>
                </div>
                <div class="stat-card blue" style="border-color: #2563eb;">
                    <div class="icon-wrap" style="background:#DBEAFE; color:#2563eb;"><i
                            class="fas fa-clipboard-check"></i></div>
                    <div class="card-info">
                        <h4>Trámites SSPP</h4><span class="count"><?php echo $count_sspp; ?></span>
                    </div>
                </div>
            </div>

            <div class="content-columns">

                <div class="column-left">
                    <div class="dashboard-section">
                        <h3
                            style="font-family: 'Montserrat'; font-size: 1.2rem; color: var(--text-main); border-bottom: 2px solid #F1F5F9; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="color: #10B981;"></i> Directorio de Empresas Activas
                        </h3>

                        <?php if (empty($empresas_activas)): ?>
                            <div
                                style="background: white; padding: 25px; text-align: center; border-radius: 12px; color: var(--text-muted); border: 1px dashed #CBD5E1;">
                                Aún no hay empresas aprobadas.</div>
                        <?php else: ?>
                            <?php foreach ($empresas_activas as $activa): ?>
                                <div class="item-card" style="border-left-color: #10B981;">
                                    <div class="item-main">
                                        <span
                                            class="item-name"><?php echo htmlspecialchars($activa['nombre_empresa']); ?></span>
                                        <span style="font-size: 0.85rem; color: var(--text-muted);"><i
                                                class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($activa['email_contacto']); ?></span>

                                        <?php
                                        // LOGICA PHP PARA CALCULAR LA VIGENCIA RESTANTE EXACTA
                                        if ($activa['es_catalogo_sspp'] && !empty($activa['vigencia_sspp'])) {
                                            $fecha_vencimiento = new DateTime($activa['vigencia_sspp']);
                                            $hoy = new DateTime();
                                            $diferencia = $hoy->diff($fecha_vencimiento);

                                            if ($diferencia->invert) {
                                                $texto_vigencia = "Expirado";
                                                $color_bg = "#FEE2E2";
                                                $color_txt = "#991B1B";
                                                $icono = "fa-times-circle";
                                            } else {
                                                if ($diferencia->y > 0) {
                                                    $texto_vigencia = "Quedan " . $diferencia->y . " año(s) y " . $diferencia->m . " mes(es)";
                                                } elseif ($diferencia->m > 0) {
                                                    $texto_vigencia = "Quedan " . $diferencia->m . " mes(es)";
                                                } else {
                                                    $texto_vigencia = "Quedan " . $diferencia->d . " día(s)";
                                                }
                                                $color_bg = "#ECFDF5";
                                                $color_txt = "#059669";
                                                $icono = "fa-hourglass-half";
                                            }

                                            echo '<span style="font-size: 0.75rem; color: ' . $color_txt . '; font-weight: 600; margin-top: 6px; background: ' . $color_bg . '; padding: 4px 10px; border-radius: 6px; display: inline-block; width: fit-content;">';
                                            echo '<i class="fas ' . $icono . '"></i> ' . $texto_vigencia;
                                            echo '</span>';
                                        }
                                        ?>
                                    </div>
                                    <a href="gestionar_empresa.php?id=<?php echo $activa['id_empresa']; ?>" class="btn-action"
                                        style="color: #10B981; background: #ECFDF5;"><i class="fas fa-building"></i></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="column-right">
                    <div class="dashboard-section">
                        <h3
                            style="font-family: 'Montserrat'; font-size: 1.2rem; color: var(--text-main); border-bottom: 2px solid #F1F5F9; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fas fa-clock" style="color: var(--js-red);"></i> Trámites SSPP en Proceso
                        </h3>
                        <?php if (empty($tramites_sspp)): ?>
                            <div
                                style="background: white; padding: 25px; text-align: center; border-radius: 12px; color: var(--text-muted); border: 1px dashed #CBD5E1;">
                                No hay trámites activos en este momento.</div>
                        <?php else: ?>
                            <?php foreach ($tramites_sspp as $tramite): ?>
                                <div class="item-card priority">
                                    <div class="item-main">
                                        <span
                                            class="item-name"><?php echo htmlspecialchars($tramite['nombre_empresa']); ?></span>
                                        <div style="display: flex; gap: 15px; align-items: center; margin-top: 5px;">
                                            <span
                                                class="badge <?php echo ($tramite['estado_tramite'] == 'Datos Recibidos') ? 'bg-green' : 'bg-yellow'; ?>"><?php echo $tramite['estado_tramite']; ?></span>
                                        </div>
                                    </div>
                                    <a href="detalle_tramite_sspp.php?id=<?php echo $tramite['id_solicitud']; ?>"
                                        class="btn-action" style="background: var(--js-red); color: white;"><i
                                            class="fas fa-arrow-right"></i></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($empresas_pendientes)): ?>
                        <div class="dashboard-section">
                            <h3
                                style="font-family: 'Montserrat'; font-size: 1.2rem; color: var(--text-main); border-bottom: 2px solid #F1F5F9; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fas fa-user-clock" style="color: #F59E0B;"></i> Registros por Validar
                            </h3>
                            <?php foreach ($empresas_pendientes as $empresa): ?>
                                <div class="item-card" style="border-left-color: #F59E0B;">
                                    <div class="item-main">
                                        <span
                                            class="item-name"><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></span>
                                        <span style="font-size: 0.85rem; color: var(--text-muted);"><i
                                                class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($empresa['email_contacto']); ?></span>
                                    </div>
                                    <a href="gestionar_empresa.php?id=<?php echo $empresa['id_empresa']; ?>"
                                        class="btn-action"><i class="fas fa-eye"></i></a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>
</body>

</html> s