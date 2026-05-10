<?php
// ---------------------------------
//  LÓGICA PHP (VER POSTULACIONES)
// ---------------------------------
session_start();

if (!isset($_SESSION['id_empresa'])) {
    header("Location: ../login.php"); 
    exit();
}

include '../config/conexion.php'; 

$id_empresa = $_SESSION['id_empresa'];
$id_vacante = $_GET['id_vacante'] ?? null;
$postulantes = [];
$vacante_titulo = '';
$error_db = null;

// Manejo de mensajes POST (cambio de estado)
$mensaje = '';
$tipo_mensaje = '';

// 1. VALIDACIÓN: Asegurar que el ID sea válido y pertenezca a esta empresa
if (empty($id_vacante) || !is_numeric($id_vacante)) {
    $error_db = "ID de vacante no válido.";
    header("Location: gestion_vacantes.php?error=invalid_id");
    exit();
}

try {
    // 2. VERIFICACIÓN DE PROPIEDAD Y OBTENCIÓN DEL TÍTULO
    $sql_check = "SELECT titulo FROM Vacantes WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt_check->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
    $stmt_check->execute();
    
    $vacante_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$vacante_data) {
        // Redirige si la vacante no existe o no pertenece a la empresa logueada
        header("Location: gestion_vacantes.php?error=unauthorized");
        exit();
    }
    $vacante_titulo = $vacante_data['titulo'];

    // 3. LÓGICA POST: CAMBIAR ESTADO DE POSTULACIÓN
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $id_postulacion = $_POST['id_postulacion'] ?? null;
        $new_status = $_POST['new_status'] ?? null;
        
        if ($id_postulacion && $new_status) {
            $sql_update = "UPDATE Postulaciones SET estado_postulacion = :new_status 
                          WHERE id_postulacion = :id_postulacion";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':new_status', $new_status, PDO::PARAM_STR);
            $stmt_update->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $mensaje = "Estado actualizado correctamente";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar el estado";
                $tipo_mensaje = "error";
            }
        }
    }

    // 4. CONSULTA PRINCIPAL: Obtener la lista de alumnos postulados
    $sql = "SELECT 
                p.id_postulacion,
                p.fecha_postulacion,
                p.estado_postulacion,
                a.id_alumno,
                a.nombre,
                a.apellidos,
                a.email,
                a.matricula,
                a.carrera,
                a.cv_url
            FROM 
                Postulaciones AS p
            JOIN 
                Alumnos AS a ON p.id_alumno = a.id_alumno
            WHERE 
                p.id_vacante = :id_vacante
            ORDER BY 
                p.fecha_postulacion DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt->execute();
    $postulantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar los postulantes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulantes: <?php echo htmlspecialchars($vacante_titulo); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
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
        
        .table-actions-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .table-action-link {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background-color 0.3s;
        }
        
        .action-download {
            background-color: #3498db;
            color: white;
        }
        
        .action-download:hover {
            background-color: #2980b9;
        }
        
        .applications-table select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 0.85rem;
            width: 100%;
        }
        
        .applications-table select:focus {
            outline: none;
            border-color: #3498db;
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
        
        .mensaje {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .mensaje.success {
            background-color: #eaf7ed;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        
        .mensaje.error {
            background-color: #fbeae9;
            color: #e74c3c;
            border: 1px solid #e74c3c;
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
            
            .table-actions-group {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }
            
            .applications-table select {
                width: auto;
            }
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <div class="navbar-brand">
            Portal de Reclutamiento <span class="brand-js">JS</span>
        </div>
        <div class="navbar-links">
            <a href="gestion_vacantes.php" class="btn-secondary-nav">Mis Vacantes</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <h1 style="margin-top: 30px; margin-bottom: 5px; text-align: left;">
            Postulantes para:
        </h1>
        <h2 style="margin-top: 0; margin-bottom: 30px; font-weight: 600; color: #2c3e50;">
            <?php echo htmlspecialchars($vacante_titulo); ?>
        </h2>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_db): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
        <?php elseif (empty($postulantes)): ?>
            <div class="job-card-empty">
                <h2>Aún no hay alumnos postulados a esta oferta.</h2>
                <p>La vacante está abierta. Vuelve a revisar más tarde.</p>
            </div>
        <?php else: ?>
            
            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Matrícula / Carrera</th>
                            <th>Fecha</th>
                            <th>Estado Actual</th>
                            <th>CV / Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulantes as $p): ?>
                            <tr>
                                <td data-label="Alumno">
                                    <strong><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']); ?></strong>
                                    <br><small><?php echo htmlspecialchars($p['email']); ?></small>
                                </td>
                                <td data-label="Matrícula / Carrera">
                                    <?php echo htmlspecialchars($p['matricula']); ?>
                                    <br><small><?php echo htmlspecialchars($p['carrera']); ?></small>
                                </td>
                                <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($p['fecha_postulacion'])); ?></td>
                                <td data-label="Estado">
                                    <?php
                                        $estado_raw = strtolower($p['estado_postulacion']);
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
                                <td data-label="CV / Acciones">
                                    <div class="table-actions-group">
                                        <?php if ($p['cv_url']): ?>
                                            <a href="../<?php echo htmlspecialchars($p['cv_url']); ?>" target="_blank" 
                                               class="table-action-link action-download">
                                                Descargar CV
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #e74c3c; font-size: 0.9em;">CV No Subido</span>
                                        <?php endif; ?>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de cambiar el estado de esta postulación?');">
                                            <input type="hidden" name="id_postulacion" value="<?php echo $p['id_postulacion']; ?>">
                                            <select name="new_status" onchange="if(confirm('¿Cambiar estado de la postulación?')) { this.form.submit(); }">
                                                <option value="">Cambiar Estado</option>
                                                <option value="enviada" <?php if ($p['estado_postulacion'] == 'enviada') echo 'selected'; ?>>Enviada</option>
                                                <option value="vista" <?php if ($p['estado_postulacion'] == 'vista') echo 'selected'; ?>>Vista</option>
                                                <option value="en_proceso" <?php if ($p['estado_postulacion'] == 'en_proceso') echo 'selected'; ?>>En Proceso</option>
                                                <option value="rechazada" <?php if ($p['estado_postulacion'] == 'rechazada') echo 'selected'; ?>>Rechazada</option>
                                            </select>
                                            <input type="hidden" name="action" value="cambiar_estado">
                                        </form>
                                    </div>
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