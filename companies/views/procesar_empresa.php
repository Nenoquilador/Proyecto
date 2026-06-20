<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil — <?php echo htmlspecialchars($datos_empresa['nombre_empresa'] ?? 'Empresa'); ?></title>
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
            <a href="perfil_empresa.php" class="nav-pill"><i class="fa-solid fa-building"></i> Perfil</a>
            <span class="welcome-msg"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($_SESSION['nombre_empresa'] ?? ''); ?></span>
            <a href="../logout.php" class="btn-logout-premium"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </nav>

    <div class="premium-dashboard">
        <div class="premium-form-card animate-fade-in"> 
            <div class="form-header">
                <div class="form-header-icon">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <h1>
                    Editar Perfil
                </h1>
                <p>
                    Mantén actualizada la información de tu empresa.
                </p>
            </div>
            
            <?php if (!empty($mensaje)): ?>
                <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?> animate-fade-in' style="margin-bottom: 24px;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form action="procesar_empresa.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="nombre">Nombre de la Empresa</label>
                        <input type="text" id="nombre" name="nombre" class="premium-input"
                               value="<?php echo htmlspecialchars($datos_empresa['nombre_empresa'] ?? ''); ?>" required>
                    </div>

                    <div class="premium-form-group">
                        <label for="email">Email de Contacto</label>
                        <input type="email" id="email" name="email" class="premium-input"
                               value="<?php echo htmlspecialchars($datos_empresa['email_contacto'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="rfc">RFC</label>
                        <input type="text" id="rfc" name="rfc" class="premium-input"
                               value="<?php echo htmlspecialchars($datos_empresa['rfc'] ?? ''); ?>" required>
                    </div>

                    <div class="premium-form-group">
                        <label for="sitio_web">Sitio Web</label>
                        <input type="url" id="sitio_web" name="sitio_web" class="premium-input"
                               value="<?php echo htmlspecialchars($datos_empresa['sitio_web'] ?? ''); ?>" placeholder="https://">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="logo">Logotipo (PNG/JPG)</label>
                        <input type="file" id="logo" name="logo" accept="image/png, image/jpeg" 
                               class="file-input-styled">
                        <small class="form-hint">Solo si deseas cambiar el actual.</small>
                    </div>
                    
                    <div class="premium-form-group">
                        <label for="banner">Banner Institucional</label>
                        <input type="file" id="banner" name="banner" accept="image/png, image/jpeg" 
                               class="file-input-styled">
                        <small class="form-hint">Imagen horizontal (ej. 1200×400px).</small>
                    </div>
                </div>

                <div class="premium-form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="premium-input" rows="5" required><?php echo htmlspecialchars($datos_empresa['descripcion'] ?? ''); ?></textarea>
                </div>
                
                <!-- CARRERAS AFINES -->
                <div class="premium-form-group">
                    <label>Carreras a las que va dirigida tu empresa</label>
                    <?php 
                        $carreras_guardadas = explode(', ', $datos_empresa['carreras_afines'] ?? '');
                        $todas_las_carreras = [
                            "Arquitectura", "Ciencias de la Comunicación", "Cinematografía", "Derecho", "Diseño y Comunicación Visual",
                            "Enseñanza de Inglés (Sabatina)", "Ingeniería en Sistemas Computacionales", "Pedagogía", "Pedagogía (Modalidad mixta)",
                            "Pedagogía del Deporte", "Psicología", "Relaciones Internacionales", "Administración", "Administración (Modalidad mixta)",
                            "Contaduría y Finanzas (Modalidad Semipresencial)", "Estomatología (Odontología)", "Gastronomía", 
                            "Gestión de Negocios Turísticos", "Nutrición", "Químico Farmacéutico Biólogo", "Médico Cirujano", "Propedéutico Medicina"
                        ];
                    ?>
                    <div class="checkbox-grid">
                        <?php foreach($todas_las_carreras as $c): ?>
                            <label>
                                <input type="checkbox" name="carreras[]" value="<?php echo htmlspecialchars($c); ?>" <?php if(in_array($c, $carreras_guardadas)) echo 'checked'; ?>>
                                <span><?php echo $c; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-footer"> 
                    <a href="perfil_empresa.php" class="btn-premium secondary">
                        Cancelar
                    </a>
                    <button type="submit" name="actualizar_perfil" class="btn-premium primary">
                        <i class="fas fa-check"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>

</body>
</html>



