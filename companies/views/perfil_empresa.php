
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil — <?php echo htmlspecialchars($empresa['nombre_empresa'] ?? 'N/A'); ?></title>
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
            <a href="gestion_vacantes.php" class="nav-pill"><i class="fa-solid fa-briefcase"></i> Vacantes</a>
            <a href="perfil_empresa.php" class="nav-pill active"><i class="fa-solid fa-building"></i> Perfil</a>
            <span class="welcome-msg"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($empresa['nombre_empresa'] ?? ''); ?></span>
            <a href="../logout.php" class="btn-logout-premium"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </nav>
    
    <div class="premium-dashboard">
        <!-- Back button -->
        <div class="animate-fade-in btn-back">
            <a href="dashboard.php" class="btn-premium secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        <div class="profile-card-premium animate-fade-in delay-1">
            <!-- Cover -->
            <div class="profile-cover" <?php if (!empty($empresa['banner_url'])) echo 'style="background-image: url(\'../' . htmlspecialchars($empresa['banner_url']) . '\'); background-size: cover; background-position: center;"'; ?>>
                <div class="profile-avatar-wrapper">
                    <?php if (!empty($empresa['logo_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($empresa['logo_url']); ?>" alt="Logo Empresa">
                    <?php else: ?>
                        <div class="profile-avatar-placeholder">
                            <i class="fas fa-building"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Edit button -->
                <div style="position: absolute; top: 16px; right: 16px;">
                    <a href="<?php echo $enlace_edicion; ?>" class="btn-premium secondary" style="background: rgba(255,255,255,.92); backdrop-filter: blur(8px); border-color: transparent;">
                        <i class="fas fa-pen"></i> Editar Perfil
                    </a>
                </div>
            </div>
            
            <!-- Details -->
            <div class="profile-details-premium">
                <h2><?php echo htmlspecialchars($empresa['nombre_empresa'] ?? 'N/A'); ?></h2>
                <?php 
                    $estado_emp = $empresa['estado_validacion'] ?? 'pendiente';
                    $badge_icon = $estado_emp === 'aprobada' ? 'circle-check' : ($estado_emp === 'rechazada' ? 'circle-xmark' : 'clock');
                ?>
                <span class="approval-badge <?php echo htmlspecialchars($estado_emp); ?>">
                    <i class="fa-solid fa-<?php echo $badge_icon; ?>"></i>
                    <?php echo ucfirst(htmlspecialchars($estado_emp)); ?>
                </span>
                <p style="color: var(--text-muted); font-size: 0.8125rem; display:flex; align-items:center; gap:6px; margin-top: 8px;">
                    <i class="fas fa-id-card"></i> 
                    RFC: <?php echo htmlspecialchars($empresa['rfc'] ?? 'No registrado'); ?>
                </p>

                <div class="profile-grid-premium">
                    <div class="info-badge">
                        <span class="info-label"><i class="fas fa-envelope"></i> Email de Contacto</span>
                        <div class="info-value"><?php echo htmlspecialchars($empresa['email_contacto'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="info-badge">
                        <span class="info-label"><i class="fas fa-globe"></i> Sitio Web</span>
                        <a id="sitio-web-link" href="#" target="_blank" class="info-value link">
                            <?php echo htmlspecialchars($empresa['sitio_web'] ?? 'No registrado'); ?>
                        </a>
                    </div>
                    
                    <div class="info-badge" style="grid-column: 1 / -1;">
                        <span class="info-label"><i class="fas fa-graduation-cap"></i> Carreras a las que se dirige</span>
                        <div class="info-value">
                            <?php 
                                if (!empty($empresa['carreras_afines'])) {
                                    $carreras_array = explode(', ', $empresa['carreras_afines']);
                                    foreach ($carreras_array as $carrera) {
                                        echo "<span class='career-pill'>" . htmlspecialchars($carrera) . "</span>";
                                    }
                                } else {
                                    echo "<span style='color: var(--text-muted);'>No se han especificado carreras</span>";
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-badge" style="grid-column: 1 / -1;">
                        <span class="info-label"><i class="fas fa-align-left"></i> Descripción Corporativa</span>
                        <div class="info-value" style="font-weight: 400; line-height: 1.7;">
                            <?php echo nl2br(htmlspecialchars($empresa['descripcion'] ?? 'Sin descripción.')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var link = document.getElementById('sitio-web-link');
            var url = link.textContent.trim();
            if (url && url !== 'No registrado') {
                if (!/^https?:\/\//i.test(url)) {
                    url = 'http://' + url;
                }
                link.href = url;
            } else {
                 link.href = '#';
                 link.style.pointerEvents = 'none';
                 link.style.color = 'var(--text-muted)';
            }
        });
    </script>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>
</body>
</html>



