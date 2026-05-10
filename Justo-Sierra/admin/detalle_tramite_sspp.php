<?php
// --------------------------------------------------
// DETALLE Y GESTIÓN DE TRÁMITE SSPP - REDISEÑO SAAS
// --------------------------------------------------
session_start();

// CANDADO ESTRICTO: SOLO VINCULACIÓN
if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
    header("Location: ../login.php");
    exit();
}

include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Admin Vinculación UJS';
$id_solicitud = $_GET['id'] ?? null;
$mensaje = '';
$tipo_mensaje = '';

if (!$id_solicitud) {
    header("Location: dashboard_admin.php");
    exit();
}

// --- LÓGICA DE PROCESAMIENTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'marcar_enviado') {
            $sql = "UPDATE solicitudes_sspp SET estado_tramite = 'Formato Enviado' WHERE id_solicitud = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id_solicitud]);
            $mensaje = "Correo marcado como enviado correctamente.";
            $tipo_mensaje = "success";
        } elseif ($accion === 'validar_telefono') {
            $sql = "UPDATE solicitudes_sspp SET estado_tramite = 'Validado por Teléfono', fecha_validacion = CURDATE() WHERE id_solicitud = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id_solicitud]);
            $mensaje = "Validación telefónica registrada.";
            $tipo_mensaje = "success";
        } elseif ($accion === 'finalizar_registro') {
            if (isset($_FILES['archivo_catalogo']) && $_FILES['archivo_catalogo']['error'] === UPLOAD_ERR_OK) {
                $dir_cat = '../archivos_sspp/catalogos/';
                if (!file_exists($dir_cat)) mkdir($dir_cat, 0777, true);
                
                // Aquí también le ponemos el nombre de la empresa al PDF del catálogo final
                $nombre_empresa_limpio = preg_replace('/[^A-Za-z0-9\-]/', '_', $_POST['nombre_empresa_hidden']);
                $nom_cat = time() . '_CATALOGO_' . $nombre_empresa_limpio . '.pdf';
                $ruta_relativa = 'archivos_sspp/catalogos/' . $nom_cat;

                if (move_uploaded_file($_FILES['archivo_catalogo']['tmp_name'], $dir_cat . $nom_cat)) {
                    $conexion->beginTransaction();
                    $stmt_sol = $conexion->prepare("UPDATE solicitudes_sspp SET estado_tramite = 'Aprobado Catálogo', archivo_catalogo_generado = :ruta, fecha_vencimiento = DATE_ADD(CURDATE(), INTERVAL 3 YEAR) WHERE id_solicitud = :id");
                    $stmt_sol->execute([':ruta' => $ruta_relativa, ':id' => $id_solicitud]);
                    
                    $id_emp = $_POST['id_empresa'];
                    $stmt_emp = $conexion->prepare("UPDATE empresas SET estado_validacion = 'aprobada', es_catalogo_sspp = 1, vigencia_sspp = DATE_ADD(CURDATE(), INTERVAL 3 YEAR) WHERE id_empresa = :id_emp");
                    $stmt_emp->execute([':id_emp' => $id_emp]);
                    $conexion->commit();
                    $mensaje = "Empresa activada exitosamente por 3 años.";
                    $tipo_mensaje = "success";
                }
            }
        }
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) $conexion->rollBack();
        $mensaje = "Error: " . $e->getMessage(); $tipo_mensaje = "error";
    }
}

// --- CONSULTA DE DATOS ---
try {
    $stmt = $conexion->prepare("SELECT s.*, e.nombre_empresa, e.email_contacto, e.id_empresa FROM solicitudes_sspp s JOIN empresas e ON s.id_empresa = e.id_empresa WHERE s.id_solicitud = :id");
    $stmt->execute([':id' => $id_solicitud]);
    $tramite = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $asunto = rawurlencode("Registro de Empresa - SSPP Justo Sierra");
    $cuerpo = rawurlencode("Estimados representantes de {$tramite['nombre_empresa']},\n\nPara continuar con su registro, favor de llenar el formato adjunto y subirlo en: http://localhost/JUSTO-SIERRA/login.php");
    $enlace_mailto = "mailto:{$tramite['email_contacto']}?subject={$asunto}&body={$cuerpo}";
} catch (PDOException $e) { die("Error de BD"); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión SSPP | <?php echo htmlspecialchars($tramite['nombre_empresa']); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --js-red: #E60013; 
            --bg-light: #F8FAFC; 
            --text-main: #1E293B; 
            --text-muted: #64748B; 
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); 
        }
        
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; margin: 0; color: var(--text-main); }
        
        /* HEADER & SIDEBAR UNIFICADO */
        .top-header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); position: sticky; top: 0; z-index: 100; }
        .brand-box { display: flex; align-items: center; gap: 12px; color: var(--js-red); }
        .brand-box h1 { margin: 0; font-family: 'Montserrat', sans-serif; font-size: 1.3rem; }
        .btn-exit { background: #FEE2E2; color: var(--js-red); padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-exit:hover { background: var(--js-red); color: white; }

        .admin-wrapper { display: flex; min-height: calc(100vh - 70px); }
        .sidebar { width: 280px; background: white; padding: 30px 20px; border-right: 1px solid #E2E8F0; }
        .sidebar-profile { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #F1F5F9; }
        .sidebar-profile img { width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; padding: 5px; margin-bottom: 10px; }
        .sidebar-profile h3 { margin: 0; font-size: 0.95rem; font-family: 'Montserrat'; }

        .menu-group { margin-bottom: 25px; }
        .menu-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-left: 10px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: var(--text-muted); border-radius: 8px; font-weight: 500; transition: 0.2s; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #FFF1F2; color: var(--js-red); }
        .nav-link.active { background: var(--js-red); color: white; }

        /* CONTENIDO PRINCIPAL Y WORKFLOW CARDS */
        .main-container { flex: 1; padding: 40px; max-width: 1000px; margin: 0 auto; width: 100%; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { display: flex; align-items: center; gap: 15px; }
        .page-title h2 { margin: 0; font-family: 'Montserrat'; font-size: 1.8rem; }
        
        .step-card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 6px solid #E2E8F0; transition: 0.3s; position: relative; }
        .step-card.active { border-left-color: var(--js-red); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .step-card.completed { border-left-color: #10B981; opacity: 0.8; }
        
        .step-number { position: absolute; right: 30px; top: 20px; font-size: 3rem; font-weight: 800; color: #F1F5F9; z-index: 0; }
        .step-content { position: relative; z-index: 1; }
        .step-title { font-family: 'Montserrat'; font-size: 1.2rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--text-main); }
        
        .btn-saas { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.2s; font-size: 0.95rem; }
        .btn-primary { background: var(--js-red); color: white; }
        .btn-primary:hover { background: #C40010; }
        .btn-outline { background: white; border: 1px solid #E2E8F0; color: var(--text-main); }
        .btn-outline:hover { background: #F8FAFC; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        
        .badge-completed { background: #DCFCE7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-left: 10px; }
        .company-tag { display: inline-block; background: #FFF1F2; color: var(--js-red); padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; border: 1px solid #FECDD3; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-box">
            <i class="fas fa-university"></i>
            <h1>JUSTO SIERRA | ADMIN</h1>
        </div>
        <div class="user-nav">
            <span style="font-weight: 500; margin-right: 15px;"><?php echo date('d M, Y'); ?></span>
            <a href="../logout.php" class="btn-exit"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <div class="admin-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_admin); ?>&background=E60013&color=fff" alt="Perfil">
                <h3><?php echo htmlspecialchars($nombre_admin); ?></h3>
                <small style="color: var(--text-muted);">Administrador</small>
            </div>

            <nav>
                <div class="menu-group">
                    <p class="menu-title">General</p>
                    <a href="dashboard_admin.php" class="nav-link"><i class="fas fa-chart-pie"></i> Resumen</a>
                </div>
                <div class="menu-group">
                    <p class="menu-title">Bolsa de Trabajo</p>
                    <a href="gestionar_empresas_admin.php" class="nav-link"><i class="fas fa-building"></i> Empresas</a>
                    <a href="gestionar_vacantes_admin.php" class="nav-link"><i class="fas fa-briefcase"></i> Vacantes</a>
                    <a href="gestionar_tramites_sspp.php" class="nav-link"><i class="fas fa-file-signature"></i> Trámites SSPP</a>
                </div>
            </nav>
        </aside>

        <main class="main-container">
            
            <div class="page-header">
                <div class="page-title">
                    <a href="dashboard_admin.php" style="color:var(--text-muted); font-size: 1.5rem; text-decoration: none;"><i class="fas fa-arrow-left"></i></a>
                    <h2>Gestión de Trámite SSPP</h2>
                </div>
                <div class="company-tag">
                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($tramite['nombre_empresa']); ?>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo ($tipo_mensaje === 'success') ? 'success' : 'error'; ?>">
                    <i class="fas <?php echo ($tipo_mensaje === 'success') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php $st = $tramite['estado_tramite']; ?>
            
            <div class="step-card <?php echo ($st == 'Solicitud Inicial') ? 'active' : 'completed'; ?>">
                <span class="step-number">01</span>
                <div class="step-content">
                    <div class="step-title">
                        <i class="fas fa-paper-plane" style="color:<?php echo ($st == 'Solicitud Inicial') ? 'var(--js-red)' : '#10B981'; ?>"></i>
                        Contactar a la Empresa
                        <?php if($st != 'Solicitud Inicial') echo '<span class="badge-completed">Completado</span>'; ?>
                    </div>
                    <?php if($st == 'Solicitud Inicial'): ?>
                        <p style="color:var(--text-muted);">Envía el formato inicial para que la empresa comience su registro en el portal.</p>
                        <div style="display:flex; gap:10px; margin-top: 15px;">
                            <a href="<?php echo $enlace_mailto; ?>" class="btn-saas btn-outline"><i class="fas fa-envelope"></i> Redactar Correo</a>
                            <form method="POST"><input type="hidden" name="accion" value="marcar_enviado">
                                <button type="submit" class="btn-saas btn-primary"><i class="fas fa-check"></i> Confirmar Envío</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-muted); margin:0;">El formato oficial ha sido enviado. La empresa ya fue notificada.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="step-card <?php echo ($st == 'Formato Enviado' || $st == 'Datos Recibidos') ? 'active' : ($st == 'Solicitud Inicial' ? '' : 'completed'); ?>">
                <span class="step-number">02</span>
                <div class="step-content">
                    <div class="step-title"><i class="fas fa-file-import" style="color:<?php echo ($st == 'Formato Enviado' || $st == 'Datos Recibidos') ? 'var(--js-red)' : '#10B981'; ?>"></i> Revisión de Documentación</div>
                    <?php if($st == 'Formato Enviado'): ?>
                        <div style="background:#FFFBEB; padding:15px; border-radius:8px; color:#92400E; border:1px solid #FEF3C7; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-clock" style="font-size: 1.2rem;"></i> 
                            <span>Esperando a que la empresa cargue el archivo completo desde su panel.</span>
                        </div>
                    <?php elseif(!empty($tramite['notas_admin'])): ?>
                        <p>Documento recibido exitosamente. Descárguelo y verifique que los datos sean correctos.</p>
                        
                        <?php 
                            // Lógica para crear el nombre de descarga
                            $extension = pathinfo($tramite['notas_admin'], PATHINFO_EXTENSION);
                            $nombre_empresa_formateado = preg_replace('/[^A-Za-z0-9\-]/', '_', strtoupper($tramite['nombre_empresa']));
                            $nombre_descarga = "FORMATO_SSPP_" . $nombre_empresa_formateado . "." . $extension;
                        ?>
                        
                        <div style="margin-top: 15px;">
                            <a href="../<?php echo $tramite['notas_admin']; ?>" download="<?php echo htmlspecialchars($nombre_descarga); ?>" class="btn-saas btn-outline" style="color:#2563EB; border-color:#BFDBFE; background: #EFF6FF;">
                                <i class="fas fa-download"></i> Descargar Documento (<?php echo strtoupper($extension); ?>)
                            </a>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-muted); margin:0;">Pendiente de recepción de documentos.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($st == 'Datos Recibidos' || $st == 'Validado por Teléfono' || $st == 'Aprobado Catálogo'): ?>
            <div class="step-card <?php echo ($st == 'Datos Recibidos') ? 'active' : ($st == 'Aprobado Catálogo' ? 'completed' : 'completed'); ?>">
                <span class="step-number">03</span>
                <div class="step-content">
                    <div class="step-title"><i class="fas fa-phone-volume" style="color:<?php echo ($st == 'Datos Recibidos') ? 'var(--js-red)' : '#10B981'; ?>"></i> Validación Telefónica</div>
                    <?php if($st == 'Datos Recibidos'): ?>
                        <p>Llama al contacto de la empresa para confirmar que la información es verídica:</p>
                        <p style="font-size: 1.1rem; background: #F8FAFC; padding: 10px 15px; border-radius: 6px; display: inline-block; border: 1px solid #E2E8F0;">
                            <i class="fas fa-envelope" style="color: var(--text-muted);"></i> <strong><?php echo $tramite['email_contacto']; ?></strong>
                        </p>
                        <form method="POST" style="margin-top: 15px;"><input type="hidden" name="accion" value="validar_telefono">
                            <button type="submit" class="btn-saas btn-primary" style="background:#059669;"><i class="fas fa-check-double"></i> Confirmar Validación</button>
                        </form>
                    <?php else: ?>
                        <p style="color:var(--text-muted); margin:0;">Validación realizada exitosamente el <strong><?php echo date('d/m/Y', strtotime($tramite['fecha_validacion'])); ?></strong>.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if($st == 'Validado por Teléfono' || $st == 'Aprobado Catálogo'): ?>
            <div class="step-card <?php echo ($st == 'Validado por Teléfono') ? 'active' : 'completed'; ?>">
                <span class="step-number">04</span>
                <div class="step-content">
                    <div class="step-title"><i class="fas fa-award" style="color:<?php echo ($st == 'Validado por Teléfono') ? 'var(--js-red)' : '#10B981'; ?>"></i> Activación y Catálogo</div>
                    <?php if($st == 'Validado por Teléfono'): ?>
                        <p>Sube el archivo <strong>FORMATO SS PP CATALOGO</strong> finalizado para activar los permisos de la empresa.</p>
                        <form method="POST" enctype="multipart/form-data" style="margin-top: 15px; background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px dashed #CBD5E1;">
                            <input type="hidden" name="accion" value="finalizar_registro">
                            <input type="hidden" name="id_empresa" value="<?php echo htmlspecialchars($tramite['id_empresa']); ?>">
                            <input type="hidden" name="nombre_empresa_hidden" value="<?php echo htmlspecialchars($tramite['nombre_empresa']); ?>">
                            
                            <input type="file" name="archivo_catalogo" accept=".pdf" required style="margin-bottom:15px; display:block; width: 100%;">
                            <button type="submit" class="btn-saas btn-primary"><i class="fas fa-flag-checkered"></i> Aprobar y Activar Empresa</button>
                        </form>
                    <?php else: ?>
                        <div style="background:#ECFDF5; padding:20px; border-radius:12px; border:1px solid #10B981; color:#065F46; display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-check-circle" style="font-size: 2rem;"></i> 
                            <div>
                                <strong style="display: block; font-size: 1.1rem;">¡Trámite finalizado con éxito!</strong> 
                                Esta empresa ya está en el catálogo y tiene vigencia hasta el <strong><?php echo date('d/m/Y', strtotime($tramite['fecha_vencimiento'])); ?></strong>.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>