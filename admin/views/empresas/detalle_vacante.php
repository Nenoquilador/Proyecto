<?php
require_once __DIR__ . '/../../controllers/DetalleVacanteController.php';
// The controller will include this view and pass the variables ($vacante, $nombre_admin)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Vacante | Justo Sierra</title>
    <link rel="stylesheet" href="../../assets/css/companies_premium.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="navbar-premium">
        <div class="navbar-brand">Panel Admin <span style="color: var(--js-rojo); font-weight: 900;">JS</span></div>
        <div class="navbar-links">
            <span style="color: var(--text-secondary); margin-right: 8px; font-size: 0.85rem;">
                <i class="fas fa-user-shield" style="margin-right: 4px;"></i> <?php echo htmlspecialchars($nombre_admin ?? 'Admin'); ?>
            </span>
            <a href="../../logout.php" class="btn-logout-premium">
                <i class="fas fa-arrow-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </nav>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin ?? 'Admin'); ?>&background=E60013&color=fff&size=144&font-size=0.4&bold=true" alt="Perfil" class="sidebar-avatar">
                <h3 class="sidebar-name"><?php echo htmlspecialchars($nombre_admin ?? 'Admin'); ?></h3>
                <p class="sidebar-role">Vinculación Empresarial</p>
            </div>
            <nav class="sidebar-nav">
                <span class="sidebar-section-label">Métricas</span>
                <a href="dashboard.php" class="nav-pill"><i class="fas fa-chart-pie" style="width: 18px;"></i> Dashboard</a>
                <span class="sidebar-section-label">Gestión</span>
                <a href="lista_empresas.php" class="nav-pill"><i class="fas fa-building" style="width: 18px;"></i> Empresas</a>
                <a href="gestionar_vacantes.php" class="nav-pill active"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
        </aside>
        <main class="main-content">
            <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <a href="gestionar_vacantes.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 8px; display: inline-block;">
                        <i class="fas fa-arrow-left"></i> Volver a vacantes
                    </a>
                    <h2 style="margin:0; font-size: 1.8rem; font-weight: 800; color: var(--text);"><?php echo htmlspecialchars($vacante['titulo']); ?></h2>
                    <p style="color: var(--text-secondary); margin-top: 8px;">Publicada por: <strong style="color: var(--js-rojo);"><i class="far fa-building"></i> <?php echo htmlspecialchars($vacante['nombre_empresa']); ?></strong></p>
                </div>
                <span class="status-pill <?php echo strtolower($vacante['estado_vacante']); ?>" style="text-transform: capitalize; padding: 8px 16px; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($vacante['estado_vacante']); ?>
                </span>
            </div>

            <div class="bento-card premium-form-card animate fadeRight" style="padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Tipo de Contrato</label>
                        <span style="font-size: 1.1rem; font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($vacante['tipo_contrato']); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Modalidad</label>
                        <span style="font-size: 1.1rem; font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($vacante['modalidad']); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Contacto</label>
                        <span style="font-size: 1rem; font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($vacante['email_contacto']); ?></span>
                    </div>
                </div>

                <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border-light);">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Descripción</label>
                    <p style="color: var(--text-secondary); line-height: 1.6;"><?php echo nl2br(htmlspecialchars($vacante['descripcion'])); ?></p>
                </div>
            </div>

            <div style="display: flex; justify-content: center; gap: 20px; padding-top: 32px;">
                <a href="gestionar_vacantes.php" class="btn-premium secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <?php if ($vacante['estado_vacante'] === 'abierta'): ?>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="accion" value="cerrar_vacante">
                    <button type="submit" class="btn-premium" style="background: var(--js-rojo); color: white;">
                        <i class="fas fa-clock"></i> Cerrar Vacante (Admin)
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

