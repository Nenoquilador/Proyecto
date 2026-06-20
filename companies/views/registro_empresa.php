
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa - Bolsa de Trabajo JS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
    <link rel="stylesheet" href="../assets/css/companies_premium.css?v=<?php echo time(); ?>"> 
</head>
<body class="registro-page">

    <div class="premium-form-card animate-fade-in" style="margin: auto;"> 
        <div class="form-header">
            <h1>Registro de Empresa</h1>
            <p>Registra tu empresa para acceder al talento de Justo Sierra.</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?> animate-fade-in'>
                <?php echo html_entity_decode($mensaje); ?>
            </div>
        <?php endif; ?>

        <form action="registro_empresa.php" method="POST">
            <?php require_once '../config/Security.php'; echo Security::getCsrfInput(); ?>
            <div class="premium-form-group">
                <label for="nombre_empresa">Nombre de la Empresa</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" class="premium-input" required placeholder="Ej: TechSolutions Innova">
            </div>
            
            <div class="premium-form-group">
                <label for="email_contacto">Email de Contacto (Será tu usuario)</label>
                <input type="email" id="email_contacto" name="email_contacto" class="premium-input" required placeholder="contacto@empresa.com">
            </div>
            
            <div class="premium-form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="premium-input" required placeholder="••••••••">
            </div>
            
            <div class="premium-form-group">
                <label for="sitio_web">Sitio Web (Opcional)</label>
                <input type="url" id="sitio_web" name="sitio_web" class="premium-input" placeholder="https://www.ejemplo.com">
            </div>

            <div class="premium-form-group">
                <label for="descripcion">Descripción de la Empresa (Máx 500 caracteres)</label>
                <textarea id="descripcion" name="descripcion" class="premium-input" rows="4" maxlength="500" required placeholder="Breve descripción de los servicios y visión de la empresa..."></textarea>
            </div>

            <!-- CARRERAS AFINES -->
            <div class="premium-form-group">
                <label>Selecciona las Carreras a las que va dirigida tu empresa</label>
                <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: -4px; margin-bottom: 10px;">Esto nos ayuda a mostrar tus vacantes a los alumnos correctos.</p>
                <?php 
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
                            <input type="checkbox" name="carreras[]" value="<?php echo htmlspecialchars($c); ?>">
                            <span><?php echo $c; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 32px;"> 
                <button type="submit" class="btn-premium primary" style="width: 100%;">
                    Solicitar Registro
                </button>
            </div>
        </form>

        <div class="registro-footer">
            <p>¿Ya tienes cuenta? <a href="../login.php">Iniciar sesión</a></p>
        </div>
    </div>

    <footer class="footer-premium" style="margin-top: 40px; border-top: none;">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>

</body>
</html>



