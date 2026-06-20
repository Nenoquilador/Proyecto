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
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: var(--text);">Directorio de Empresas</h2>
                <a href="exportar_empresas.php" class="btn-primary" style="text-decoration: none; padding: 10px 20px; font-size: 0.9rem;">
                    <i class="fas fa-file-excel"></i> Exportar a CSV
                </a>
            </div>

            <form method="GET" action="lista_empresas.php" style="margin-bottom: 24px; display: flex; gap: 16px;">
                <div class="search-bar" style="flex: 1;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Buscar por nombre, correo..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="estado" class="form-select" style="width: 200px; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
                    <option value="">Todos los Estados</option>
                    <option value="pendiente" <?php if($filtro_estado == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="aprobada" <?php if($filtro_estado == 'aprobada') echo 'selected'; ?>>Aprobada</option>
                    <option value="rechazada" <?php if($filtro_estado == 'rechazada') echo 'selected'; ?>>Rechazada</option>
                </select>
                <button type="submit" class="btn-primary" style="padding: 0 24px;">Filtrar</button>
            </form>

            <div class="premium-form-card" style="padding: 0; overflow: hidden;">
                <?php if(empty($empresas)): ?>
                    <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                        <i class="fas fa-building" style="font-size: 3rem; color: var(--border-light); margin-bottom: 16px;"></i>
                        <p>No se encontraron empresas con los criterios seleccionados.</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto; width: 100%;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                            <thead style="background: var(--surface-alt);">
                                <tr>
                                    <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Nombre Comercial</th>
                                    <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Contacto</th>
                                    <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Estado</th>
                                    <th style="padding: 16px; text-align: left; font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($empresas as $emp): ?>
                                <tr>
                                    <td style="padding: 16px; border-bottom: 1px solid var(--border-light); font-weight: 500; color: var(--text);">
                                        <?php echo htmlspecialchars($emp['nombre_empresa']); ?>
                                    </td>
                                    <td style="padding: 16px; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($emp['email_contacto']); ?>
                                    </td>
                                    <td style="padding: 16px; border-bottom: 1px solid var(--border-light);">
                                        <span class="status-pill <?php echo htmlspecialchars($emp['estado_validacion']); ?>" style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; text-transform: capitalize;">
                                            <?php echo htmlspecialchars($emp['estado_validacion']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px; border-bottom: 1px solid var(--border-light);">
                                        <a href="gestionar_empresa.php?id=<?php echo $emp['id_empresa']; ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; text-decoration: none;">
                                            Ver Detalles
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($total_pages > 1): ?>
                    <div style="padding: 16px; border-top: 1px solid var(--border-light); display: flex; justify-content: center; gap: 8px;">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&estado=<?php echo urlencode($filtro_estado); ?>" 
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
