<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Trámite de Servicio Social</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f0f2f5 !important; }
        .ss-container { max-width: 900px; margin: 40px auto; padding: 30px; background: #fff !important; border-radius: 12px; box-shadow: 0 8px 24px rgba(230, 0, 19, 0.08); font-family: 'Inter', sans-serif; color: #1a1a1a !important; }
        .ss-container p, .ss-container li { color: #1a1a1a !important; }
        .ss-container h2 { color: var(--color-js-rojo-principal, #E60013) !important; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid var(--color-js-amarillo, #FCC800); padding-bottom: 10px; }
        .reglas { background: rgba(252, 200, 0, 0.15) !important; border-left: 4px solid var(--color-js-amarillo, #FCC800); padding: 15px; margin-bottom: 25px; border-radius: 0 8px 8px 0; }
        .reglas h3 { color: #b38f00 !important; margin-top: 0; }
        .etapa { border: 1px solid #e0e0e0 !important; padding: 25px; margin-bottom: 25px; border-radius: 8px; transition: all 0.3s ease; }
        .etapa.activa { border-color: var(--color-js-rojo-principal, #E60013) !important; background: rgba(230, 0, 19, 0.02) !important; box-shadow: 0 4px 12px rgba(230, 0, 19, 0.05); }
        .etapa h3 { margin-top: 0; color: var(--color-js-rojo-secundario, #EA0029) !important; font-weight: 700; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-family: 'Inter', sans-serif; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--color-js-rojo-principal, #E60013); outline: none; box-shadow: 0 0 0 3px rgba(230, 0, 19, 0.1); }
        .btn-submit, button { background: linear-gradient(60deg, #E60013, #FCC800); color: #fff; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-submit:hover, button:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(230, 0, 19, 0.2); }
        .alerta { background: var(--color-js-rojo-principal, #E60013); color: white; padding: 12px 15px; border-radius: 6px; margin-top: 15px; font-weight: 600; }
        
        /* Topbar fixes for isolated page */
        .topbar { background: linear-gradient(60deg, #E60013, #FCC800); padding: 15px 30px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .topbar h1 { margin: 0; font-size: 1.5rem; color: white !important; }
        .topbar nav a { color: white; text-decoration: none; margin-left: 20px; font-weight: 600; }
        .topbar nav a:hover { opacity: 0.8; text-decoration: underline; }
    </style>
</head>
<body>
    <header class="topbar">
        <h1>Servicio Social</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="mis_postulaciones.php">Mis Postulaciones</a>
            <a href="../logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="ss-container">
        <h2>Trámite de Servicio Social</h2>
        <p><strong>Empresa:</strong> <?= htmlspecialchars($postulacion['nombre_empresa'] ?? '') ?></p>
        <p><strong>Vacante:</strong> <?= htmlspecialchars($postulacion['titulo'] ?? '') ?></p>

        <!-- Módulo 2: Panel de Instrucciones y Lineamientos -->
        <div class="reglas">
            <h3>Lineamientos Institucionales</h3>
            <ul>
                <li>El Servicio Social se realiza en un lapso mínimo de 6 meses y máximo de 2 años.</li>
                <li>Se deben cubrir un total de 480 horas en la Ciudad de México o 600 horas en cualquier Estado de la República.</li>
                <li>Es importante no tener adeudos Administrativos para poder realizar el trámite.</li>
                <li>Si el Servicio Social es cancelado por la empresa, se someterá a revisión y el alumno puede ser acreedor a una sanción de entre 3 y 6 meses antes de poder reiniciar el trámite.</li>
            </ul>
        </div>

        <?php $estado = $tramite['estado_tramite'] ?? null; ?>

        <!-- Módulo 1: Carta de Presentación y Créditos -->
        <div class="etapa <?= !$estado || $estado === 'solicitud_creditos' ? 'activa' : '' ?>">
            <h3>Módulo 1: Carta de Créditos</h3>
            <?php if (!$estado): ?>
                <form method="POST" target="_blank">
                    <input type="hidden" name="accion" value="iniciar_tramite">
                    <p style="margin-bottom: 15px;">Para iniciar tu trámite, descarga la <strong>Carta de Créditos</strong>. Al descargarla, el sistema registrará el inicio de tu trámite.</p>
                    <button type="submit" class="btn-submit"><i class="fas fa-file-pdf"></i> Descargar Carta de Créditos</button>
                </form>
                <div class="alerta">
                    Aviso: Este documento debe imprimirse físicamente para realizar el pago en Cajas antes de continuar con los documentos de Servicio Social.
                </div>
            <?php else: ?>
                <p>✓ Trámite iniciado.</p>
                <a href="../formatos_oficiales/Carta de Créditos mar2020 (1).pdf" target="_blank" class="btn-submit" style="display:inline-block; text-decoration:none; margin-top: 10px;">
                    <i class="fas fa-file-pdf"></i> Volver a Descargar Carta de Créditos
                </a>
                
                <?php if ($estado === 'solicitud_creditos'): ?>
                    <div class="alerta" style="margin-top:20px;">
                        <strong>Pendiente:</strong> Realiza el pago en Cajas. Una vez que Servicios Escolares valide tu pago en el sistema, se desbloqueará el documento final de Servicio Social.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Módulo 2: Documento de Servicio Social (Desbloqueado tras el pago) -->
        <?php if ($estado && $estado !== 'solicitud_creditos'): ?>
        <div class="etapa activa">
            <h3>Módulo 2: Documento de Servicio Social</h3>
            <p style="color: #27ae60; font-weight: 600; margin-bottom: 15px;">✓ Tu pago ha sido validado por Servicios Escolares.</p>
            <p style="margin-bottom: 15px;">Ya puedes descargar tu formato oficial de Servicio Social para continuar con tu proceso en la empresa.</p>
            <a href="../formatos_oficiales/Servicio Social mar2023 (1).pdf" target="_blank" class="btn-submit" style="display:inline-block; text-decoration:none;">
                <i class="fas fa-file-pdf"></i> Descargar Documento Servicio Social
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
