<?php
// 1. Iniciar la sesión y aplicar la seguridad
session_start();

if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php");
    exit;
}

// Apunta a la conexión en config/
include '../config/conexion.php';

$id_alumno = $_SESSION['id_alumno'];
$nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
$error_bd = null;
$postulaciones = [];

// Lógica para obtener todos los datos de postulación del alumno de la BD
try {
    $sql = "SELECT
                p.id_postulacion, 
                p.fecha_postulacion, 
                p.estado_postulacion,
                v.titulo AS titulo_vacante, 
                v.id_vacante,
                e.nombre_empresa,
                e.logo_url AS logo_empresa
            FROM
                Postulaciones AS p
            JOIN
                Vacantes AS v ON p.id_vacante = v.id_vacante
            JOIN
                Empresas AS e ON v.id_empresa = e.id_empresa
            WHERE
                p.id_alumno = :id_alumno
            ORDER BY
                p.fecha_postulacion DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_bd = "Error al cargar tu historial de postulaciones: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Postulaciones - Bolsa JS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Estilos específicos para esta página */
        .applications-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        .applications-table th {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        }
        
        .applications-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-family: 'Roboto', sans-serif;
        }
        
        .applications-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .applications-table tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-enviada {
            background-color: #eaf3fb;
            color: #3498db;
            border: 1px solid #d0e1f0;
        }
        
        .status-vista {
            background-color: #fef9e7;
            color: #f39c12;
            border: 1px solid #fdebd0;
        }
        
        .status-en_proceso {
            background-color: #eaf7ed;
            color: #27ae60;
            border: 1px solid #d5f5e3;
        }
        
        .status-rechazada {
            background-color: #fbeae9;
            color: #e74c3c;
            border: 1px solid #fadbd8;
        }
        
        .btn-table-action {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn-table-action:hover {
            background-color: #2980b9;
        }
        
        .job-card-empty {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        .job-card-empty h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .job-card-empty a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .job-card-empty a:hover {
            text-decoration: underline;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .applications-table {
                display: block;
            }
            
            .applications-table thead {
                display: none;
            }
            
            .applications-table tbody, 
            .applications-table tr, 
            .applications-table td {
                display: block;
                width: 100%;
            }
            
            .applications-table tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
            }
            
            .applications-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid #eee;
            }
            
            .applications-table td:last-child {
                border-bottom: none;
            }
            
            .applications-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                font-weight: bold;
                text-align: left;
                color: #2c3e50;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            Bolsa de Trabajo <span class="brand-js">Justo Sierra</span>
        </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($nombre_alumno); ?></span>
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">

        <h1>Historial de Postulaciones</h1>

        <?php if ($error_bd): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error_bd); ?></div>
        <?php elseif (empty($postulaciones)): ?>
            <div class="job-card-empty" style="text-align: left;">
                <h2>No has realizado ninguna postulación.</h2>
                <p>Visita el <a href="dashboard.php">Dashboard</a> para encontrar vacantes.</p>
            </div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Vacante</th>
                            <th>Empresa</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulaciones as $p): ?>
                            <tr>
                                <td data-label="Vacante">
                                    <strong><?php echo htmlspecialchars($p['titulo_vacante']); ?></strong>
                                </td>
                                <td data-label="Empresa">
                                    <?php if (!empty($p['logo_empresa'])): ?>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img src="<?php echo htmlspecialchars($p['logo_empresa']); ?>" 
                                                 alt="Logo <?php echo htmlspecialchars($p['nombre_empresa']); ?>"
                                                 style="width: 30px; height: 30px; border-radius: 4px; object-fit: cover;">
                                            <?php echo htmlspecialchars($p['nombre_empresa']); ?>
                                        </div>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($p['nombre_empresa']); ?>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($p['fecha_postulacion'])); ?></td>
                                <td data-label="Estado">
                                    <?php
                                        $estado_raw = strtolower($p['estado_postulacion']);
                                        // Convertir guiones bajos a espacios para mostrar
                                        if ($estado_raw == 'en_proceso') {
                                            $estado_display = 'En proceso';
                                        } else {
                                            $estado_display = ucfirst($estado_raw);
                                        }
                                    ?>
                                    <span class="status-badge status-<?php echo $estado_raw; ?>">
                                        <?php echo htmlspecialchars($estado_display); ?>
                                    </span>
                                </td>
                                <td data-label="Acción">
                                    <a href="detalle_vacante.php?id=<?php echo $p['id_vacante']; ?>"
                                       class="btn-table-action">
                                        <i class="fas fa-search"></i> Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

</body>
</html>