<?php
require_once __DIR__ . '/../../controllers/GestionarEmpresaController.php';
// The controller will include this view and pass the variables ($empresa, $carreras_guardadas, $todas_las_carreras, $mensaje, $tipo_mensaje, $nombre_admin)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Empresa | Justo Sierra</title>
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
                <a href="lista_empresas.php" class="nav-pill active"><i class="fas fa-building" style="width: 18px;"></i> Empresas</a>
                <a href="gestionar_vacantes.php" class="nav-pill"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <a href="lista_empresas.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 8px; display: inline-block;">
                        <i class="fas fa-arrow-left"></i> Volver a empresas
                    </a>
                    <h2 style="margin:0; font-size: 1.8rem; font-weight: 800; color: var(--text);">Revisión de Registro</h2>
                    <p style="color: var(--text-secondary); margin-top: 8px;">Verifica la autenticidad de la empresa antes de activarla.</p>
                </div>
                <span class="status-pill <?php echo strtolower($empresa['estado_validacion']); ?>" style="text-transform: capitalize; padding: 8px 16px; font-size: 0.9rem;">
                    Estado actual: <?php echo htmlspecialchars($empresa['estado_validacion']); ?>
                </span>
            </div>

            <?php if ($mensaje): ?>
                <div class="mensaje <?php echo ($tipo_mensaje === 'success') ? 'exito' : 'error'; ?> animate-fade-in" style="margin-bottom: 24px;">
                    <i class="fas <?php echo ($tipo_mensaje === 'success') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="bento-card premium-form-card animate fadeRight" style="padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 40px;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Nombre Comercial</label>
                        <span style="font-size: 1.1rem; font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Razón Social</label>
                        <span style="font-size: 1.1rem; font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($empresa['razon_social'] ?? 'No registrado'); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">RFC</label>
                        <span style="font-size: 1rem; font-weight: 500; color: var(--text);"><?php echo htmlspecialchars($empresa['rfc']); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Teléfono</label>
                        <span style="font-size: 1rem; font-weight: 500; color: var(--text);"><?php echo htmlspecialchars($empresa['telefono_contacto'] ?? 'No registrado'); ?></span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Correo de Contacto</label>
                        <span style="font-size: 1rem; font-weight: 500; color: var(--text);"><?php echo htmlspecialchars($empresa['email_contacto']); ?></span>
                    </div>
                </div>

                <!-- FORMULARIO DE ACCIONES -->
                <div style="background: var(--surface-alt); padding: 24px; border-radius: 12px; border: 1px solid var(--border-light);">
                    <form method="POST" action="gestionar_empresa.php?id=<?php echo $empresa['id_empresa']; ?>">
                        <input type="hidden" name="accion" value="cambiar_estado">
                        
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text); margin-bottom: 8px;">Decisión de Registro</label>
                            <select name="estado_validacion" required class="form-select premium-input" style="width: 100%; box-sizing: border-box;">
                                <option value="pendiente" <?php if($empresa['estado_validacion'] == 'pendiente') echo 'selected'; ?>>Dejar en Pendiente</option>
                                <option value="aprobada" <?php if($empresa['estado_validacion'] == 'aprobada') echo 'selected'; ?>>Aprobar Empresa</option>
                                <option value="rechazada" <?php if($empresa['estado_validacion'] == 'rechazada') echo 'selected'; ?>>Rechazar Empresa</option>
                            </select>
                        </div>
                        
                        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; color: var(--text); border-top: 1px solid var(--border-light); padding-top: 24px;">
                            <i class="fas fa-graduation-cap" style="color: var(--js-rojo);"></i> Carreras a las que puede postular
                        </h3>
                        <p style="color: var(--text-secondary); margin-bottom: 16px; font-size: 0.9rem;">Selecciona los perfiles que esta empresa busca (para bolsa de trabajo y SSPP).</p>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px; margin-bottom: 24px; max-height: 200px; overflow-y: auto; padding: 16px; background: white; border: 1px solid var(--border-light); border-radius: 8px;">
                            <?php foreach($todas_las_carreras as $c): ?>
                                <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.85rem; color: var(--text); cursor: pointer; transition: transform 0.2s;">
                                    <input type="checkbox" name="carreras[]" value="<?php echo htmlspecialchars($c); ?>" <?php if(in_array($c, $carreras_guardadas)) echo 'checked'; ?> style="margin-top: 2px;">
                                    <span><?php echo $c; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- SECCIÃ“N: BITÃCORA -->
                        <h3 style="margin: 0 0 12px 0; font-size: 1.1rem; color: #92400E; display: flex; align-items: center; gap: 8px; padding-top: 16px; border-top: 1px dashed rgba(253, 230, 138, 0.8);">
                            <i class="fas fa-book"></i> Bitácora Administrativa (Solo Interno)
                        </h3>
                        <p style="color: #B45309; margin-bottom: 16px; font-size: 0.9rem;">Escribe aquíÃ­ recordatorios o historial de llamóadas con esta empresa. La empresa no puede ver esto.</p>
                        
                        <textarea name="notas_internas" class="premium-input" rows="4" placeholder="Ej. Se le llamóÃ³ el martes..." style="width: 100%; box-sizing: border-box; margin-bottom: 16px;"><?php echo htmlspecialchars($empresa['notas_internas'] ?? ''); ?></textarea>
                        
                        <button type="submit" class="btn-premium primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

