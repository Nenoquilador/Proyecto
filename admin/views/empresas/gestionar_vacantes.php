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
                <a href="lista_empresas.php" class="nav-pill <?php echo basename($_SERVER['PHP_SELF']) == 'lista_empresas.php' ? 'active' : ''; ?>"><i class="fas fa-building" style="width: 18px;"></i> Empresas</a>
                <a href="gestionar_vacantes.php" class="nav-pill <?php echo basename($_SERVER['PHP_SELF']) == 'gestionar_vacantes.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill <?php echo basename($_SERVER['PHP_SELF']) == 'gestionar_tramites.php' ? 'active' : ''; ?>"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
            <div class="sidebar-footer">
                <p>Â© <?php echo date('Y'); ?> Universidad Justo Sierra</p>
            </div>
        </aside>
        <main class="main-content">            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: var(--text);">Gestión de Vacantes</h2>
            </div>

            <form method="GET" action="gestionar_vacantes.php" style="margin-bottom: 24px; display: flex; gap: 16px;">
                <div class="search-bar" style="flex: 1;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Buscar por título de la vacante..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="estado" class="form-select" style="width: 200px; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
                    <option value="">Todos los Estados</option>
                    <option value="abierta" <?php if($estado == 'abierta') echo 'selected'; ?>>Abierta</option>
                    <option value="cerrada" <?php if($estado == 'cerrada') echo 'selected'; ?>>Cerrada</option>
                </select>
                <button type="submit" class="btn-primary" style="padding: 0 24px;">Filtrar</button>
            </form>

            <div class="premium-form-card" style="padding: 0; overflow: hidden;">
                <?php if(empty($vacantes)): ?>
                    <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                        <i class="fas fa-briefcase" style="font-size: 3rem; color: var(--border-light); margin-bottom: 16px;"></i>
                        <p>No se encontraron vacantes con los criterios seleccionados.</p>
                    </div>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--surface-alt);">
                            <tr>
                                <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Puesto</th>
                                <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Empresa</th>
                                <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Estado</th>
                                <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Fecha Publicación</th>
                                <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vacantes as $vac): ?>
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid var(--border-light); font-weight: 500; color: var(--text);">
                                    <?php echo htmlspecialchars($vac['titulo']); ?>
                                </td>
                                <td style="padding: 16px; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($vac['nombre_empresa']); ?>
                                </td>
                                <td style="padding: 16px; border-bottom: 1px solid var(--border-light);">
                                    <?php 
                                        $color = $vac['estado'] == 'abierta' ? '#10B981' : '#EF4444';
                                    ?>
                                    <span class="status-badge" style="background: <?php echo $color; ?>; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; text-transform: capitalize;">
                                        <?php echo htmlspecialchars($vac['estado']); ?>
                                    </span>
                                </td>
                                <td style="padding: 16px; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars(date('d/m/Y', strtotime($vac['fecha_publicacion']))); ?>
                                </td>
                                <td style="padding: 16px; border-bottom: 1px solid var(--border-light);">
                                    <a href="detalle_vacante.php?id=<?php echo $vac['id_vacante']; ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; text-decoration: none;">
                                        Revisar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if($total_pages > 1): ?>
                    <div style="padding: 16px; border-top: 1px solid var(--border-light); display: flex; justify-content: center; gap: 8px;">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&estado=<?php echo urlencode($estado); ?>" 
                               class="nav-pill <?php echo $i == $page ? 'active' : ''; ?>" style="padding: 6px 12px; min-width: auto; margin: 0; text-decoration: none;">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>        </main>
    </div>
</body>
</html>
