<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Vinculación | Justo Sierra</title>
    <link rel="stylesheet" href="../../assets/css/companies_premium.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar-premium">
        <div class="navbar-brand">Panel Admin <span style="color: var(--js-rojo); font-weight: 900;">JS</span></div>
        <div class="navbar-links">
            <span style="color: var(--text-secondary); margin-right: 8px; font-size: 0.85rem;">
                <i class="fas fa-user-shield" style="margin-right: 4px;"></i> <?php echo htmlspecialchars($nombre_admin); ?>
            </span>
            <a href="../../logout.php" class="btn-logout-premium">
                <i class="fas fa-arrow-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </nav>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff&size=144&font-size=0.4&bold=true" alt="Perfil" class="sidebar-avatar">
                <h3 class="sidebar-name"><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <p class="sidebar-role">Vinculación Empresarial</p>
            </div>
            <nav class="sidebar-nav">
                <span class="sidebar-section-label">Métricas</span>
                <a href="dashboard.php" class="nav-pill active"><i class="fas fa-chart-pie" style="width: 18px;"></i> Dashboard</a>
                <span class="sidebar-section-label">Gestión</span>
                <a href="lista_empresas.php" class="nav-pill"><i class="fas fa-building" style="width: 18px;"></i> Empresas</a>
                <a href="gestionar_vacantes.php" class="nav-pill"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
            <div class="sidebar-footer">
                <p>Â© <?php echo date('Y'); ?> Universidad Justo Sierra</p>
            </div>
        </aside>
        
        <main class="main-content">
            <?php if (!empty($status_msg)): ?>
                <div class="mensaje <?php echo ($status_type === 'success') ? 'exito' : 'error'; ?> animate-fade-in" style="margin-bottom: 24px;">
                    <i class="fas <?php echo ($status_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars(urldecode($status_msg)); ?>
                </div>
            <?php endif; ?>

            <header class="dashboard-header animate-slide-up">
                <h1><?php echo $saludo; ?>, <span><?php echo htmlspecialchars(explode(' ', $nombre_admin)[0]); ?></span></h1>
                <p>Panel de control de vinculación y seguimiento empresarial.</p>
            </header>

            <div class="kpi-grid animate-slide-up" style="animation-delay: 0.1s;">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-users"></i></div>
                    <div class="kpi-details">
                        <span class="kpi-label">Total Alumnos</span>
                        <span class="kpi-value"><?php echo $count_alumnos; ?></span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;"><i class="fas fa-briefcase"></i></div>
                    <div class="kpi-details">
                        <span class="kpi-label">Vacantes Abiertas</span>
                        <span class="kpi-value"><?php echo $count_vacantes; ?></span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;"><i class="fas fa-clock"></i></div>
                    <div class="kpi-details">
                        <span class="kpi-label">Empresas Pendientes</span>
                        <span class="kpi-value"><?php echo $count_pendientes; ?></span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366F1;"><i class="fas fa-file-contract"></i></div>
                    <div class="kpi-details">
                        <span class="kpi-label">Trámites Activos</span>
                        <span class="kpi-value"><?php echo $count_sspp; ?></span>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div class="premium-form-card">
                    <h3>Estado de Vacantes</h3>
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="vacantesChart"></canvas>
                    </div>
                </div>
                <div class="premium-form-card">
                    <h3>Crecimiento de Empresas</h3>
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="empresasChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Table Row -->
            <div class="content-columns">
                <div class="premium-form-card">
                    <h3>Directorio de Empresas Activas</h3>
                    <?php if (empty($empresas_activas)): ?>
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <p>No hay empresas en el padrÃ³n actualmente.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto; width: 100%;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-light);">Empresa</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-light);">Catálogo SSPP</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-light);">Vigencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($empresas_activas as $emp): ?>
                                    <tr>
                                        <td style="padding: 12px; border-bottom: 1px solid var(--border-light);"><?php echo htmlspecialchars($emp['nombre_empresa']); ?></td>
                                        <td style="padding: 12px; border-bottom: 1px solid var(--border-light);">
                                            <?php if ($emp['es_catalogo_sspp']): ?>
                                                <span class="status-pill aprobada" style="padding: 4px 8px; font-size: 0.75rem;">Sí</span>
                                            <?php else: ?>
                                                <span class="status-pill rechazada" style="padding: 4px 8px; font-size: 0.75rem;">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px; border-bottom: 1px solid var(--border-light);"><?php echo htmlspecialchars($emp['vigencia_sspp'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        const vacData = <?php echo $vacantesJson; ?>;
        const empData = <?php echo $empresasJson; ?>;
        
        if (vacData.length > 0) {
            new Chart(document.getElementById('vacantesChart'), {
                type: 'doughnut',
                data: {
                    labels: vacData.map(v => v.estado.toUpperCase()),
                    datasets: [{
                        data: vacData.map(v => v.total),
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
        
        if (empData.length > 0) {
            new Chart(document.getElementById('empresasChart'), {
                type: 'line',
                data: {
                    labels: empData.map(e => e.mes),
                    datasets: [{
                        label: 'Empresas Registradas',
                        data: empData.map(e => e.total),
                        borderColor: '#E60013',
                        backgroundColor: 'rgba(230,0,19,0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    </script>
</body>
</html>
