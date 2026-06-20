<?php
require_once __DIR__ . '/../../controllers/DetalleTramiteController.php';
// The controller will include this view and pass the variables ($tramite, $mensaje, $tipo_mensaje, $nombre_admin)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trámite SSPP | Justo Sierra</title>
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
                <a href="gestionar_vacantes.php" class="nav-pill"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill active"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
        </aside>
        <main class="main-content">
            <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <a href="gestionar_tramites.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 8px; display: inline-block;">
                        <i class="fas fa-arrow-left"></i> Volver a trámites
                    </a>
                    <h2 style="margin:0; font-size: 1.8rem; font-weight: 800; color: var(--text);">Gestión de Trámite SSPP</h2>
                </div>
                <div class="status-pill abierta" style="font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($tramite['nombre_empresa']); ?>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="mensaje <?php echo ($tipo_mensaje === 'success') ? 'exito' : 'error'; ?> animate-fade-in" style="margin-bottom: 24px;">
                    <i class="fas <?php echo ($tipo_mensaje === 'success') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php $st = $tramite['estado_tramite']; ?>

            <!-- PASO 1 -->
            <div class="bento-card step-card animate fadeRight <?php echo ($st == 'Solicitud Inicial') ? 'active' : 'completed'; ?>" style="padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <span class="step-number" style="font-size: 2rem; font-weight: 900; color: var(--border-light); margin-bottom: 16px; display: block;">01</span>
                <div style="display: flex; gap: 24px;">
                    <div style="flex-shrink: 0; font-size: 2rem;">
                        <i class="fas fa-paper-plane" style="color:<?php echo ($st == 'Solicitud Inicial') ? 'var(--js-rojo)' : '#10B981'; ?>;"></i> 
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 1.2rem; font-weight: 700; color: var(--text);">Enviar Formato a la Empresa</h3>
                    <?php if($st == 'Solicitud Inicial'): ?>
                        <p style="color: var(--text-secondary); margin-bottom: 16px;">La empresa ha solicitado iniciar el trámite de Servicio Social y Prácticas Profesionales.</p>
                        <form method="POST">
                            <input type="hidden" name="accion" value="enviar_formato">
                            <button type="submit" class="btn-premium primary">
                                <i class="fas fa-envelope"></i> Marcar Formato como Enviado
                            </button>
                        </form>
                    <?php else: ?>
                        <p style="color: #10B981; font-weight: 500; margin: 0;"><i class="fas fa-check-circle"></i> Formato enviado.</p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- PASO 2 -->
            <div class="bento-card step-card animate fadeRight <?php echo ($st == 'Formato Enviado' || $st == 'Datos Recibidos') ? 'active' : ($st == 'Solicitud Inicial' ? '' : 'completed'); ?>" style="animation-delay: 0.1s; padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <span class="step-number" style="font-size: 2rem; font-weight: 900; color: var(--border-light); margin-bottom: 16px; display: block;">02</span>
                <div style="display: flex; gap: 24px;">
                    <div style="flex-shrink: 0; font-size: 2rem;">
                        <i class="fas fa-file-import" style="color:<?php echo ($st == 'Formato Enviado' || $st == 'Datos Recibidos') ? 'var(--js-rojo)' : '#10B981'; ?>;"></i> 
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 1.2rem; font-weight: 700; color: var(--text);">Recepción del Formato Contestado</h3>
                    <?php if($st == 'Formato Enviado'): ?>
                        <div style="background: var(--surface-alt); padding: 16px; border-radius: 8px; border: 1px dashed var(--border-light); display: inline-flex; align-items: center; gap: 12px; color: var(--text-muted);">
                            <i class="fas fa-clock" style="font-size: 1.2rem;"></i> 
                            <span style="font-weight: 500;">Esperando a que la empresa cargue el archivo completo desde su panel.</span>
                        </div>
                    <?php elseif(!empty($tramite['notas_admin'])): ?>
                        <p style="color: var(--text-secondary); margin-bottom: 16px;">Documento recibido exitosamente. DescÃ¡rguelo y verifique que los datos sean correctos.</p>
                        
                        <?php 
                            $extension = pathinfo($tramite['notas_admin'], PATHINFO_EXTENSION);
                            $nombre_empresa_formateado = preg_replace('/[^A-Za-z0-9\-]/', '_', strtoupper($tramite['nombre_empresa']));
                            $nombre_descarga = "FORMATO_SSPP_" . $nombre_empresa_formateado . "." . $extension;
                        ?>
                        
                        <div style="margin-top: 16px;">
                            <a href="../<?php echo $tramite['notas_admin']; ?>" download="<?php echo htmlspecialchars($nombre_descarga); ?>" 
                               class="btn-premium secondary">
                                <i class="fas fa-download"></i> Descargar Documento (<?php echo strtoupper($extension); ?>)
                            </a>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-muted); margin: 0; font-weight: 500;">Pendiente de recepciÃ³n de documentos.</p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- PASO 3 -->
            <?php if($st == 'Datos Recibidos' || $st == 'Validado por Teléfono' || $st == 'Aprobado Catálogo'): ?>
            <div class="bento-card step-card animate fadeRight <?php echo ($st == 'Datos Recibidos') ? 'active' : 'completed'; ?>" style="animation-delay: 0.2s; padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <span class="step-number" style="font-size: 2rem; font-weight: 900; color: var(--border-light); margin-bottom: 16px; display: block;">03</span>
                <div style="display: flex; gap: 24px;">
                    <div style="flex-shrink: 0; font-size: 2rem;">
                        <i class="fas fa-phone-volume" style="color:<?php echo ($st == 'Datos Recibidos') ? 'var(--js-rojo)' : '#10B981'; ?>;"></i> 
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 1.2rem; font-weight: 700; color: var(--text);">Validación TelefÃ³nica</h3>
                    <?php if($st == 'Datos Recibidos'): ?>
                        <p style="color: var(--text-secondary); margin-bottom: 16px;">Llama al contacto de la empresa para confirmar que la informaciÃ³n es verÃ­dica:</p>
                        <div style="display: flex; gap: 32px; margin-bottom: 24px; padding: 16px; background: var(--surface-alt); border-radius: 8px;">
                            <div>
                                <span style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Correo de Contacto</span>
                                <strong style="color: var(--text); font-size: 1.1rem;"><?php echo htmlspecialchars($tramite['email_contacto']); ?></strong>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="accion" value="validar_telefono">
                            <button type="submit" class="btn-premium primary">
                                <i class="fas fa-phone-square-alt"></i> Marcar como Validado por Teléfono
                            </button>
                        </form>
                    <?php else: ?>
                        <p style="color: #10B981; font-weight: 500; margin: 0;"><i class="fas fa-check-circle"></i> Validación telefÃ³nica completada exitosamente.</p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- PASO 4 -->
            <?php if($st == 'Validado por Teléfono' || $st == 'Aprobado Catálogo'): ?>
            <div class="bento-card step-card animate fadeRight <?php echo ($st == 'Validado por Teléfono') ? 'active' : 'completed'; ?>" style="animation-delay: 0.3s; padding: 32px; background: var(--surface); border: 1px solid var(--border-light, #E2E8F0); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <span class="step-number" style="font-size: 2rem; font-weight: 900; color: var(--border-light); margin-bottom: 16px; display: block;">04</span>
                <div style="display: flex; gap: 24px;">
                    <div style="flex-shrink: 0; font-size: 2rem;">
                        <i class="fas fa-check-double" style="color:<?php echo ($st == 'Validado por Teléfono') ? 'var(--js-rojo)' : '#10B981'; ?>;"></i> 
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 1.2rem; font-weight: 700; color: var(--text);">AprobaciÃ³n Final y Catálogo</h3>
                    <?php if($st == 'Validado por Teléfono'): ?>
                        <p style="color: var(--text-secondary); margin-bottom: 20px;">Sube el archivo <strong>FORMATO SS PP CATALOGO</strong> finalizado para activar los permisos de la empresa.</p>
                        
                        <form method="POST" enctype="multipart/form-data" class="premium-form-card" style="padding: 24px; border: 1px solid var(--border-light); box-shadow: none;">
                            <input type="hidden" name="accion" value="aprobar_final">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text); margin-bottom: 8px;">Subir Archivo de Catálogo Firmado (PDF)</label>
                                <input type="file" name="archivo_catalogo" required accept=".pdf" class="premium-input" style="width: 100%; box-sizing: border-box;">
                            </div>
                            <button type="submit" class="btn-premium primary" style="width: 100%; justify-content: center;">
                                <i class="fas fa-upload"></i> Finalizar Trámite y Activar Permisos
                            </button>
                        </form>
                    <?php else: ?>
                        <p style="color: #10B981; font-weight: 500; margin: 0;"><i class="fas fa-check-circle"></i> Trámite finalizado. La empresa ya puede publicar vacantes.</p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>

