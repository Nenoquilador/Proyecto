<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Empresas | Justo Sierra</title>
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
                <i class="fas fa-user-shield" style="margin-right: 4px;"></i> <?php echo htmlspecialchars($_SESSION['nombre_admin'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <a href="../../logout.php" class="btn-logout-premium">
                <i class="fas fa-arrow-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </nav>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nombre_admin'] ?? 'Admin'); ?>&background=E60013&color=fff&size=144&font-size=0.4&bold=true" alt="Perfil" class="sidebar-avatar">
                <h3 class="sidebar-name"><?php echo htmlspecialchars($_SESSION['nombre_admin'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="sidebar-role">Vinculación Empresarial</p>
            </div>
            <nav class="sidebar-nav">
                <span class="sidebar-section-label">Métricas</span>
                <a href="dashboard.php" class="nav-pill"><i class="fas fa-chart-pie" style="width: 18px;"></i> Dashboard</a>
                <span class="sidebar-section-label">Gestión</span>
                <a href="lista_empresas.php" class="nav-pill"><i class="fas fa-building" style="width: 18px;"></i> Empresas</a>
                <a href="gestionar_vacantes.php" class="nav-pill"><i class="fas fa-briefcase" style="width: 18px;"></i> Vacantes</a>
                <a href="gestionar_tramites.php" class="nav-pill"><i class="fas fa-file-signature" style="width: 18px;"></i> Trámites SSPP</a>
                <a href="carga_masiva.php" class="nav-pill active"><i class="fas fa-upload" style="width: 18px;"></i> Carga Masiva</a>
            </nav>
            <div class="sidebar-footer">
                <p>© <?php echo date('Y'); ?> Universidad Justo Sierra</p>
            </div>
        </aside>
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: var(--text);">Carga Masiva de Empresas</h2>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div style="margin-bottom: 24px; padding: 16px; border-radius: 8px; font-weight: 500; <?php echo $error ? 'background: #fee2e2; color: #991b1b; border: 1px solid #f87171;' : 'background: #dcfce7; color: #166534; border: 1px solid #4ade80;'; ?>">
                    <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="premium-form-card" style="padding: 32px; margin-bottom: 24px;">
                <h3 style="margin-top: 0; color: var(--text); font-weight: 700;">Subir archivo CSV</h3>
                <p style="color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6; font-size: 0.95rem;">
                    Sube un archivo <strong>.csv (codificado en UTF-8)</strong> con la siguiente estructura.<br>
                    <strong>Nota:</strong> La primera fila se omitirá automáticamente como encabezado.<br>
                    <code style="background: var(--surface-alt); padding: 6px 12px; border-radius: 6px; display: inline-block; margin-top: 12px; font-family: monospace; border: 1px solid var(--border-light); color: var(--text);">Nombre Empresa, Email Contacto, Descripción, Sitio Web, Carreras Afines, Password Temporal</code>
                </p>
                
                <form action="carga_masiva.php" method="POST" enctype="multipart/form-data">
                    <?php require_once __DIR__ . '/../../../config/Security.php'; echo Security::getCsrfInput(); ?>
                    
                    <div style="margin-bottom: 24px;">
                        <input type="file" name="csv_file" accept=".csv" required style="padding: 20px; border: 2px dashed var(--border); border-radius: 12px; width: 100%; box-sizing: border-box; background: var(--surface-alt); font-family: 'Inter', sans-serif;">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="padding: 12px 24px; font-size: 1rem;"><i class="fa-solid fa-cloud-arrow-up" style="margin-right: 8px;"></i> Procesar Carga de Datos</button>
                </form>
            </div>

            <?php if (!empty($resultados)): ?>
                <div class="premium-form-card" style="padding: 32px;">
                    <h3 style="margin-top: 0; margin-bottom: 16px; color: var(--text); font-weight: 700;">Resultados del Procesamiento</h3>
                    <div style="max-height: 400px; overflow-y: auto; background: var(--surface-alt); padding: 20px; border-radius: 12px; border: 1px solid var(--border-light);">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach($resultados as $res): ?>
                                <li style="padding: 10px 0; border-bottom: 1px solid var(--border-light); font-size: 0.95rem; font-weight: 500; <?php echo strpos($res, 'Error') !== false || strpos($res, 'Omitida') !== false ? 'color: #D97706;' : 'color: #059669;'; ?>">
                                    <i class="fas <?php echo strpos($res, 'Error') !== false || strpos($res, 'Omitida') !== false ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($res, ENT_QUOTES, 'UTF-8'); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
