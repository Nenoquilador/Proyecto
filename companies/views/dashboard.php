<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — <?php echo htmlspecialchars($nombre_empresa); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/companies_premium.css?v=<?php echo time(); ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar-premium">
        <a href="dashboard.php" class="navbar-brand"><span class="brand-js">JS</span> Portal Empresas</a>
        <div class="navbar-links">
            <a href="dashboard.php" class="nav-pill active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="gestion_vacantes.php" class="nav-pill"><i class="fa-solid fa-briefcase"></i> Vacantes</a>
            <a href="perfil_empresa.php" class="nav-pill"><i class="fa-solid fa-building"></i> Perfil</a>
            <span class="welcome-msg"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($nombre_empresa); ?></span>
            <a href="../logout.php" class="btn-logout-premium"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </nav>

    <div class="bento-dashboard">
        <!-- HEADER -->
        <div class="animate-fade-in" style="margin-bottom: 32px;">
            <h1 class="premium-title">Panel de Control</h1>
            <p class="premium-subtitle">Resumen del estado de vacantes y postulaciones.</p>
        </div>

        <?php echo $mensaje_subida; ?>
        <?php if ($error_bd): echo "<div class='mensaje error'>$error_bd</div>"; endif; ?>

        <?php if ($estado_empresa === 'pendiente' || $estado_empresa === 'rechazada'): ?>
            <!-- ONBOARDING STEPPER -->
            <div class="stepper-premium animate-fade-in delay-1">
                <div class="stepper-header">
                    <div class="stepper-icon"><i class="fas fa-shield-halved"></i></div>
                    <div>
                        <h2 style="color: var(--text); margin:0; font-size: 1.25rem; font-weight: 700;">Activación de Cuenta</h2>
                        <p style="color: var(--text-secondary); margin:0; font-size: .9rem;">Completa estos pasos para poder publicar vacantes.</p>
                    </div>
                </div>
                
                <div class="step-box">
                    <div class="step-content">
                        <h3>1. Descarga el Formato Oficial</h3>
                        <p>Descarga la plantilla, complétala con los datos de tu institución y fírmala.</p>
                    </div>
                    <a href="../formatos_oficiales/FORMATO REGISTRO EMPRESA SS Y PP.docx" class="btn-premium secondary" download style="flex-shrink:0; padding: 10px 18px; font-size: .875rem;">
                        <i class="fas fa-file-word"></i> Descargar
                    </a>
                </div>

                <div class="step-box" style="border-color: #93c5fd; background: #eff6ff;">
                    <div class="step-content">
                        <h3 style="color: #1e40af;">2. Sube el Formato Completado</h3>
                        <p style="color: #3b82f6;">Sube tu archivo para revisión por Vinculación.</p>
                    </div>
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px; align-items: center; flex-shrink: 0;">
                        <input type="file" name="formato_sspp" accept=".doc,.docx,.pdf" required 
                               style="background: white; border: 1px solid #bfdbfe; border-radius: 8px; padding: 8px 10px; font-size: .8125rem; width: 180px;">
                        <button type="submit" class="btn-premium primary" style="padding: 10px 18px; font-size: .875rem;">
                            <i class="fas fa-cloud-arrow-up"></i> Enviar
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>

            <!-- STAT CARDS -->
            <div class="stats-grid-premium">
                <div class="stat-card-premium bento-card animate-fade-in delay-1">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <h3>Vacantes Activas</h3>
                    <div class="value"><?php echo $active_vacancies; ?></div>
                </div>
                <div class="stat-card-premium bento-card animate-fade-in delay-2">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <h3>Postulaciones</h3>
                    <div class="value"><?php echo $total_applications; ?></div>
                </div>
                <div class="stat-card-premium bento-card animate-fade-in delay-3">
                    <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
                    <h3>Vacantes Cerradas</h3>
                    <div class="value"><?php echo $closed_vacancies; ?></div>
                </div>
            </div>
            
            <!-- CHARTS -->
            <div class="charts-grid animate-fade-in delay-2">
                <div class="chart-card bento-card">
                    <h3><i class="fas fa-chart-line" style="margin-right:6px; color: var(--js-rojo);"></i> Tendencia de Postulaciones</h3>
                    <div style="position: relative; height:240px;">
                        <canvas id="chartPostulaciones"></canvas>
                    </div>
                </div>
                <div class="chart-card bento-card">
                    <h3><i class="fas fa-chart-pie" style="margin-right:6px; color: var(--js-amarillo);"></i> Estado de Ofertas</h3>
                    <div style="position: relative; height:240px;">
                        <canvas id="chartVacantes"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- ANALYTICS CHARTS -->
            <div class="charts-grid animate-fade-in delay-2" style="margin-top: 24px;">
                <div class="chart-card bento-card">
                    <h3><i class="fas fa-eye" style="margin-right:6px; color: #3b82f6;"></i> Vistas vs Postulaciones</h3>
                    <div style="position: relative; height:240px;">
                        <canvas id="chartVistasPostulaciones"></canvas>
                    </div>
                </div>
                <div class="chart-card bento-card">
                    <h3><i class="fas fa-percent" style="margin-right:6px; color: #10b981;"></i> Tasa de Conversión (%)</h3>
                    <div style="position: relative; height:240px;">
                        <canvas id="chartTasaConversion"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- ACTIONS -->
            <div class="section-title animate-fade-in delay-3">
                <i class="fa-solid fa-bolt"></i> Acciones Rapidas
            </div>
            
            <div class="action-grid-premium animate-fade-in delay-4">
                <a href="publicar_vacante.php" class="quick-action-card">
                    <div class="qa-icon primary"><i class="fa-solid fa-plus"></i></div>
                    <div class="qa-text"><h3>Publicar Vacante</h3><p>Crear una nueva oferta de empleo o practicas</p></div>
                </a>
                <a href="gestion_vacantes.php" class="quick-action-card">
                    <div class="qa-icon gold"><i class="fa-solid fa-list-check"></i></div>
                    <div class="qa-text"><h3>Mis Vacantes</h3><p>Administrar y dar seguimiento a tus ofertas activas</p></div>
                </a>
                <a href="perfil_empresa.php" class="quick-action-card">
                    <div class="qa-icon neutral"><i class="fa-solid fa-building"></i></div>
                    <div class="qa-text"><h3>Perfil Institucional</h3><p>Actualizar la informacion y logotipo de tu empresa</p></div>
                </a>
            </div>

            <!-- SUGGESTED PROFILES -->
            <?php if (!empty($perfiles_sugeridos)): ?>
                <div class="section-title animate-fade-in delay-3" style="margin-top: 40px;">
                    <i class="fa-solid fa-star"></i> Top Perfiles Sugeridos
                </div>
                <div class="bento-card animate-fade-in delay-4" style="background: var(--surface); padding: 24px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
                        <?php foreach($perfiles_sugeridos as $perfil): ?>
                            <div class="kanban-card" style="margin:0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-light);">
                                <div class="k-card-title"><?php echo htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellidos']); ?></div>
                                <div class="k-card-subtitle" style="margin-top: 8px;">
                                    <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($perfil['carrera']); ?><br>
                                    <i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($perfil['email']); ?>" style="color:var(--text-muted); text-decoration:none;"><?php echo htmlspecialchars($perfil['email']); ?></a>
                                </div>
                                <div class="k-card-details" style="margin-top: 12px; font-weight: 600; color: var(--js-rojo);">
                                    <i class="fas fa-briefcase"></i> Match para: <?php echo htmlspecialchars($perfil['vacante_recomendada']); ?>
                                </div>
                                <?php if (!empty($perfil['cv_url'])): ?>
                                    <div class="k-card-actions" style="margin-top: 12px;">
                                        <a href="../<?php echo htmlspecialchars($perfil['cv_url']); ?>" target="_blank" class="k-btn-action" title="Ver CV">
                                            <i class="fas fa-file-pdf"></i> Ver CV
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <script>
                // Chart.js Config
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.font.weight = '500';
                Chart.defaults.color = '#94a3b8';
                
                // Bar Chart
                const ctxPost = document.getElementById('chartPostulaciones').getContext('2d');
                const gradientBar = ctxPost.createLinearGradient(0, 0, 0, 240);
                gradientBar.addColorStop(0, 'rgba(230, 0, 19, 0.85)');
                gradientBar.addColorStop(1, 'rgba(230, 0, 19, 0.35)');

                new Chart(ctxPost, {
                    type: 'bar',
                    data: {
                        labels: <?php echo $chart_labels_json; ?>,
                        datasets: [{
                            label: 'Postulantes',
                            data: <?php echo $chart_data_json; ?>,
                            backgroundColor: gradientBar,
                            borderRadius: 8,
                            barPercentage: 0.6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: '#f1f5f9', drawBorder: false }, 
                                ticks: { stepSize: 1 },
                                border: { display: false }
                            },
                            x: { 
                                grid: { display: false },
                                border: { display: false }
                            }
                        }
                    }
                });

                // Doughnut Chart
                const ctxVac = document.getElementById('chartVacantes').getContext('2d');
                new Chart(ctxVac, {
                    type: 'doughnut',
                    data: {
                        labels: ['Abiertas', 'Cerradas'],
                        datasets: [{
                            data: [<?php echo $active_vacancies; ?>, <?php echo $closed_vacancies; ?>],
                            backgroundColor: ['#E60013', '#e2e8f0'],
                            borderWidth: 0,
                            hoverOffset: 6,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { 
                                position: 'bottom', 
                                labels: { 
                                    padding: 16, 
                                    usePointStyle: true,
                                    pointStyleWidth: 10,
                                    font: { size: 12, weight: '600' }
                                } 
                            }
                        }
                    }
                });

                // Gráfico Vistas vs Postulaciones
                const ctxVP = document.getElementById('chartVistasPostulaciones').getContext('2d');
                new Chart(ctxVP, {
                    type: 'bar',
                    data: {
                        labels: <?php echo $json_conversion_labels ?? '["Sin Datos"]'; ?>,
                        datasets: [
                            {
                                label: 'Vistas',
                                data: <?php echo $json_conversion_vistas ?? '[0]'; ?>,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderRadius: 4,
                                barPercentage: 0.6
                            },
                            {
                                label: 'Postulaciones',
                                data: <?php echo $json_conversion_postulaciones ?? '[0]'; ?>,
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderRadius: 4,
                                barPercentage: 0.6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } }, 
                            x: { grid: { display: false }, border: { display: false } } 
                        }
                    }
                });

                // Gráfico Tasa Conversión
                const ctxConv = document.getElementById('chartTasaConversion').getContext('2d');
                new Chart(ctxConv, {
                    type: 'line',
                    data: {
                        labels: <?php echo $json_conversion_labels ?? '["Sin Datos"]'; ?>,
                        datasets: [{
                            label: 'Tasa de Conversión (%)',
                            data: <?php echo $json_conversion_tasas ?? '[0]'; ?>,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, border: { display: false } }, 
                            x: { grid: { display: false }, border: { display: false } } 
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>
</body>
</html>


