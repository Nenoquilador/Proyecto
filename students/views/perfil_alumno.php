<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Bolsa de Trabajo JS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container animate fadeRight">

        <h1 style="text-align: center;">Mi Perfil de Alumno</h1>

        <?php if (isset($error_bd) && $error_bd): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error_bd); ?></div>
        <?php elseif ($perfil): ?>

            <div class="profile-view card animate fadeRight" style="animation-delay: 0.1s;">

                <div class="profile-header">
                    <h3><?php echo htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellidos']); ?></h3>
                    <p><?php echo htmlspecialchars($perfil['email']); ?></p>
                </div>

                <div class="profile-content">

                    <div class="profile-grid">

                        <div class="profile-field">
                            <span class="field-label">Matrícula</span>
                            <span class="field-value"><?php echo htmlspecialchars($perfil['matricula']); ?></span>
                        </div>

                        <div class="profile-field">
                            <span class="field-label">Carrera</span>
                            <span class="field-value"><?php echo htmlspecialchars($perfil['carrera'] ?? 'No especificada'); ?></span>
                        </div>

                        <div class="profile-field">
                            <span class="field-label">Semestre</span>
                            <span class="field-value">
                                <?php
                                    $semestre_mostrar = isset($perfil['semestre']) ? $perfil['semestre'] . 'mo' : 'N/A';
                                    echo htmlspecialchars($semestre_mostrar);
                                ?>
                            </span>
                        </div>

                        <div class="profile-field">
                            <span class="field-label">Estado de Cuenta</span>
                            <span class="field-value active-status">ACTIVO</span>
                        </div>

                        <div class="profile-field full-row">
                            <span class="field-label">Perfil LinkedIn</span>
                            <span class="field-value">
                                <?php if (!empty($perfil['perfil_linkedin'])):
                                    $linkedin_url = htmlspecialchars($perfil['perfil_linkedin']);
                                    // Añadir https:// si falta para que el enlace funcione
                                    if (!preg_match("~^(?:f|ht)tps?://~i", $linkedin_url)) {
                                        $linkedin_url = "https://" . $linkedin_url;
                                    }
                                ?>
                                    <a href="<?php echo $linkedin_url; ?>" target="_blank" style="font-weight: 500;">
                                        <i class="fab fa-linkedin" style="color:#0077b5;"></i> Ver Perfil en LinkedIn
                                    </a>
                                <?php else: ?>
                                    No proporcionado
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="mis_postulaciones.php" class="btn-secundario-form">Mis Postulaciones</a>
                        <a href="dashboard.php" class="btn-secundario-form">Volver al Inicio</a>
                        <a href="actualizar_perfil.php" class="btn-secundario-form">Editar y Subir CV</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="mensaje error">No se pudo cargar la información de tu perfil.</div>
        <?php endif; ?>

    </div>

</body>
</html>
