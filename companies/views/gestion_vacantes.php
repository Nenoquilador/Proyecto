<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Vacantes — Justo Sierra</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
    <link rel="stylesheet" href="../assets/css/companies_premium.css?v=<?php echo time(); ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    
    <nav class="navbar-premium">
        <a href="dashboard.php" class="navbar-brand"><span class="brand-js">JS</span> Portal Empresas</a>
        <div class="navbar-links">
            <a href="dashboard.php" class="nav-pill"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="gestion_vacantes.php" class="nav-pill active"><i class="fa-solid fa-briefcase"></i> Vacantes</a>
            <a href="perfil_empresa.php" class="nav-pill"><i class="fa-solid fa-building"></i> Perfil</a>
            <span class="welcome-msg"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($_SESSION['nombre_empresa'] ?? ''); ?></span>
            <a href="../logout.php" class="btn-logout-premium"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </nav>

    <div class="premium-dashboard">
        
        <!-- Top bar -->
        <div class="animate-fade-in page-topbar">
            <a href="dashboard.php" class="btn-premium secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="publicar_vacante.php" class="btn-premium primary">
                <i class="fas fa-plus"></i> Nueva Vacante
            </a>
        </div>
        
        <!-- Header -->
        <div class="animate-fade-in delay-1" style="margin-bottom: 24px;">
            <h1 class="premium-title">Mis Vacantes</h1>
            <p class="premium-subtitle">
                Gestiona todas tus ofertas de prácticas profesionales y servicio social.
            </p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span class="count-badge open">
                    <i class="fas fa-circle"></i> <?php echo $count_abiertas; ?> Abiertas
                </span>
                <span class="count-badge closed">
                    <i class="fas fa-circle"></i> <?php echo $count_cerradas; ?> Cerradas
                </span>
                <span class="count-badge total">
                    <i class="fas fa-layer-group"></i> <?php echo count($vacantes); ?> Total
                </span>
            </div>
        </div>
        
        <?php if ($error_bd): ?>
            <div class='mensaje error animate-fade-in'><?php echo $error_bd; ?></div>
        <?php endif; ?>

        <?php if (count($vacantes) > 0): ?>
            <div class="job-list-premium" style="margin-top: 12px;">
                <?php foreach ($vacantes as $index => $vacante): ?>
                    <?php $delayClass = 'delay-' . min(($index % 4) + 1, 4); ?>
                    <div class="premium-job-card animate-fade-in <?php echo $delayClass; ?>">
                        
                        <div class="job-card-header">
                            <h2>
                                <?php echo htmlspecialchars($vacante['titulo']); ?>
                            </h2>
                            <p>
                                <i class="fas fa-location-dot"></i>
                                <?php echo htmlspecialchars($vacante['ubicacion']); ?>
                            </p>
                        </div>
                        
                        <div class="job-card-body">
                            <div class="job-card-tags">
                                <span class="tag tag-contrato">
                                    <?php echo formatear_tag($vacante['tipo_contrato']); ?>
                                </span>
                                <span class="tag tag-modalidad">
                                    <?php echo formatear_tag($vacante['modalidad']); ?>
                                </span>
                                <?php 
                                    $estado = htmlspecialchars($vacante['estado']);
                                    $clase_estado = ($estado === 'abierta') ? 'status-enviada' : 'status-rechazada';
                                    echo "<span class='status-badge {$clase_estado}'>{$estado}</span>";
                                ?>
                            </div>
                            
                            <?php if (!empty($vacante['salario_ofrecido'])): ?>
                            <div class="job-card-salary"><i class="fa-solid fa-money-bill-wave"></i> $<?php echo htmlspecialchars($vacante['salario_ofrecido']); ?></div>
                            <?php endif; ?>
                            
                            <div style="display:flex; align-items:center; gap:10px; margin-top:10px; flex-wrap:wrap;">
                                <span class="job-card-postulations"><i class="fa-solid fa-users"></i> <?php echo $vacante['total_postulaciones']; ?> postulaciones</span>
                                <span class="job-card-date">
                                    <i class="far fa-calendar"></i> Publicada: <strong><?php echo date('d M Y', strtotime($vacante['fecha_publicacion'])); ?></strong>
                                </span>
                            </div>
                        </div>
                        
                        <div class="job-card-footer">
                            <span>
                                <i class="fas fa-graduation-cap"></i> Prácticas / Servicio Social
                            </span>
                            
                            <div class="table-actions-group">
                                <a href="ver_postulaciones.php?id_vacante=<?php echo $vacante['id_vacante']; ?>" 
                                   class="btn-premium secondary" data-tooltip="Ver postulaciones">
                                    <i class="fas fa-users"></i>
                                </a>
                                
                                <a href="publicar_vacante.php?id=<?php echo $vacante['id_vacante']; ?>" 
                                   class="btn-premium secondary btn-action-edit" data-tooltip="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                
                                <form action="procesar_empresa.php" method="POST" style="margin: 0;" onsubmit="return confirm('¿Crear una copia de esta vacante?');">
                                    <input type="hidden" name="action" value="duplicar_vacante">
                                    <input type="hidden" name="id_vacante" value="<?php echo $vacante['id_vacante']; ?>">
                                    <button type="submit" class="btn-premium secondary btn-action-duplicate" data-tooltip="Duplicar">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                
                                <?php if ($vacante['estado'] === 'abierta'): ?>
                                    <a href="procesar_empresa.php?action=cerrar&id_vacante=<?php echo $vacante['id_vacante']; ?>" 
                                       data-tooltip="Cerrar vacante"
                                       class="btn-premium secondary btn-action-close"
                                       
                                       onclick="return confirm('¿Cerrar esta vacante?');">
                                        <i class="fas fa-xmark"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="job-card-empty animate-fade-in delay-2">
                <?php if (!$error_bd): ?>
                    <i class="fas fa-folder-open"></i>
                    <h2>No tienes vacantes publicadas</h2>
                    <p>Usa el botón "Nueva Vacante" para comenzar a recibir postulaciones.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>

</body>
</html>



