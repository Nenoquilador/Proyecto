<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id_vacante ? 'Editar Vacante' : 'Publicar Nueva Vacante'; ?></title>
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
    <!-- CSS Premium -->
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
        <div class="premium-form-card animate-fade-in"> 
            <div class="form-header">
                <div class="form-header-icon">
                    <i class="fas fa-file-circle-plus"></i>
                </div>
                <h1>

                <?php echo $id_vacante ? 'Editar Vacante Existente' : 'Publicar Nueva Vacante'; ?>
            </h1>
            <p>
                Complete los detalles para atraer al mejor talento de Justo Sierra.
            </p>
            
            <?php if (!empty($mensaje)): ?>
                <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?> animate-fade-in'>
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form action="publicar_vacante.php<?php echo $id_vacante ? '?id=' . $id_vacante : ''; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="premium-form-group">
                    <label for="titulo">Título de la Vacante</label>
                    <input type="text" id="titulo" name="titulo" class="premium-input"
                        value="<?php echo htmlspecialchars($datos_vacante['titulo'] ?? ''); ?>" required placeholder="Ej: Desarrollador Web Jr.">
                </div>
                
                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="tipo_contrato">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato" class="premium-input" required>
                            <?php $selected_tc = $datos_vacante['tipo_contrato'] ?? ''; ?>
                            <option value="">-- Seleccione Tipo --</option>
                            <option value="tiempo_completo" <?php echo $selected_tc === 'tiempo_completo' ? 'selected' : ''; ?>>Tiempo Completo</option>
                            <option value="medio_tiempo" <?php echo $selected_tc === 'medio_tiempo' ? 'selected' : ''; ?>>Medio Tiempo</option>
                            <option value="practicas" <?php echo $selected_tc === 'practicas' ? 'selected' : ''; ?>>Prácticas / Pasantía</option>
                            <option value="por_proyecto" <?php echo $selected_tc === 'por_proyecto' ? 'selected' : ''; ?>>Por Proyecto</option>
                        </select>
                    </div>

                    <div class="premium-form-group">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad" class="premium-input" required>
                            <?php $selected_m = $datos_vacante['modalidad'] ?? ''; ?>
                            <option value="">-- Seleccione Modalidad --</option>
                            <option value="presencial" <?php echo $selected_m === 'presencial' ? 'selected' : ''; ?>>Presencial</option>
                            <option value="remoto" <?php echo $selected_m === 'remoto' ? 'selected' : ''; ?>>Remoto</option>
                            <option value="hibrido" <?php echo $selected_m === 'hibrido' ? 'selected' : ''; ?>>Híbrido</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="carrera_afin">Carrera Afin (Requisito)</label>
                        <select id="carrera_afin" name="carrera_afin" class="premium-input" required>
                            <?php 
                            $selected_ca = $datos_vacante['carrera_afin'] ?? ''; 
                            $carreras_ejemplo = [
                                'administracion' => 'Administracion de Empresas', 'arquitectura' => 'Arquitectura',
                                'comunicacion' => 'Ciencias de la Comunicacion', 'ciencias_politicas' => 'Ciencias Politicas',
                                'cirujano_dentista' => 'Cirujano Dentista', 'contaduria' => 'Contaduria Publica',
                                'derecho' => 'Derecho', 'diseno_grafico' => 'Diseno Grafico',
                                'enfermeria' => 'Enfermeria', 'gastronomia' => 'Gastronomia',
                                'ingenieria_civil' => 'Ingenieria Civil', 'sistemas' => 'Ing. en Sistemas Computacionales',
                                'ingenieria_industrial' => 'Ingenieria Industrial', 'mecatronica' => 'Ingenieria Mecatronica',
                                'educacion' => 'Licenciatura en Educacion', 'mercadotecnia' => 'Mercadotecnia',
                                'negocios_internacionales' => 'Negocios Internacionales', 'nutricion' => 'Nutricion',
                                'pedagogia' => 'Pedagogia', 'psicologia' => 'Psicologia',
                                'trabajo_social' => 'Trabajo Social', 'turismo' => 'Turismo'
                            ];
                            ?>
                            <option value="">-- Seleccione Carrera --</option>
                            <?php foreach ($carreras_ejemplo as $val => $label): ?>
                                <option value="<?php echo $val; ?>" <?php echo $selected_ca === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="premium-form-group">
                        <label for="ubicacion">Ubicacion</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="premium-input"
                            value="<?php echo htmlspecialchars($datos_vacante['ubicacion'] ?? ''); ?>" required placeholder="Ej: Ciudad de Mexico">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="premium-form-group">
                        <label for="salario">Salario Ofrecido (opcional)</label>
                        <input type="text" id="salario" name="salario_ofrecido" class="premium-input" placeholder="Ej: $8,000 - $12,000 MXN" value="<?php echo htmlspecialchars($datos_vacante['salario_ofrecido'] ?? ''); ?>">
                    </div>
                    <div class="premium-form-group"></div>
                </div>
                
                <div class="premium-form-group">
                    <label for="descripcion">Descripción y Requisitos</label>
                    <textarea id="descripcion" name="descripcion" class="premium-input" rows="8" required placeholder="Describe las responsabilidades, beneficios y requisitos detallados de la vacante..."><?php echo htmlspecialchars($datos_vacante['descripcion'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-footer"> 
                    <a href="gestion_vacantes.php" class="btn-premium secondary">
                        Cancelar
                    </a>
                    <button type="submit" name="publicar" class="btn-premium primary">
                        <i class="fas fa-save"></i> 
                        <?php echo $id_vacante ? 'Guardar Cambios' : 'Publicar Vacante'; ?>
                    </button>
                </div>
            </form>
    </div>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>

</body>
</html>



