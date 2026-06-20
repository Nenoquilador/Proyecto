<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil y CV - Bolsa JS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand"> Bolsa de Trabajo <span class="brand-js">Justo Sierra</span> </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($nombre_alumno); ?></span>
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container animate fadeRight">
        <div class="form-card card profile-edit-card animate fadeRight" style="animation-delay: 0.1s;">
            <h1 style="text-align: center; margin-bottom: 30px;">Actualizar Perfil y CV</h1>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $error ? 'error' : 'exito'; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>
            <?php if ($error_bd): ?>
                 <div class="mensaje error"><?php echo htmlspecialchars($error_bd); ?></div>
            <?php endif; ?>

            <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                <?php echo Security::getCsrfInput(); ?>
                <h2>Datos Académicos</h2>
                <div class="mb-3">
                    <label for="carrera">Carrera:</label>
                    <select id="carrera" name="carrera" class="filter-select" style="width: 100%;" required>
                        <option value="">-- Selecciona tu Carrera --</option>
                        <?php foreach ($carreras_ejemplo as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo ($perfil['carrera'] ?? '') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="perfil_linkedin">Perfil LinkedIn (URL completa, opcional):</label>
                    <input type="text" id="perfil_linkedin" name="perfil_linkedin"
                           value="<?php echo htmlspecialchars($perfil['perfil_linkedin'] ?? ''); ?>"
                           placeholder="Ej: https://www.linkedin.com/in/tu-usuario">
                </div>

                <h2 style="margin-top: 30px;">Currículum Vitae (CV)</h2>
                <div class="mb-3">
                    <label for="cv_file">Subir CV (Solo archivo PDF, máx 5MB):</label>
                    <div class="custom-file-input-wrapper">
                        <button type="button" class="custom-file-input-button"> <i class="fas fa-upload"></i> Seleccionar archivo </button>
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf">
                        <span class="file-name-display">Sin archivo seleccionado</span>
                    </div>

                    <?php if ($cv_actual_url): ?>
                        <a href="<?php echo $cv_actual_url; ?>" target="_blank" class="current-cv-link"> <i class="fas fa-check-circle"></i> **CV Actual** Ver archivo subido </a>
                    <?php else: ?>
                        <p class="current-cv-link" style="color: var(--color-texto-secundario); font-weight: normal;"> <i class="fas fa-times-circle" style="color: var(--color-error);"></i> No tienes un CV subido. </p>
                    <?php endif; ?>
                    <p class="cv-upload-note">*Al subir un nuevo archivo PDF, el anterior será reemplazado automáticamente.*</p>
                </div>

                <div class="form-actions profile-edit-actions">
                    <a href="perfil_alumno.php" class="btn-secundario-form">Volver al Perfil</a>
                    <button type="submit" name="guardar_cambios" class="boton-principal"> <i class="fas fa-save"></i> Guardar Cambios </button>
                </div>
            </form>
        </div>
    </div>

   <script>
        const customButton = document.querySelector('.custom-file-input-button');
        const fileInput = document.getElementById('cv_file');
        const fileNameDisplay = document.querySelector('.file-name-display');

        customButton.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = 'Sin archivo seleccionado';
            }
        });
    </script>

</body>
</html>
