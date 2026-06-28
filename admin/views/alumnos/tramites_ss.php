<?php
$page_title = 'Trámites de Servicio Social | Servicios Escolares';
$current_page = 'tramites_ss';
$breadcrumb = 'Servicio Social';
require_once __DIR__ . '/../layouts/alumnos/header.php';
?>

    <style>
        body { background-color: #f0f2f5 !important; }
        .container { max-width: 1000px; margin: 20px auto; padding: 30px; background: #fff !important; border-radius: 12px; box-shadow: 0 8px 24px rgba(230, 0, 19, 0.08); font-family: 'Inter', sans-serif; color: #1a1a1a !important; }
        .container h2 { color: var(--color-js-rojo-principal, #E60013) !important; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid var(--color-js-amarillo, #FCC800); padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: 'Inter', sans-serif; color: #1a1a1a !important; }
        th, td { padding: 15px 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: rgba(230, 0, 19, 0.05) !important; color: var(--color-js-rojo-principal, #E60013) !important; font-weight: 700; border-bottom: 2px solid var(--color-js-rojo-principal, #E60013); }
        tr:hover { background-color: rgba(252, 200, 0, 0.05) !important; }
        .btn { padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; color: white; background: #666; font-weight: 600; font-size: 0.9em; transition: transform 0.2s, box-shadow 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .btn-success { background: linear-gradient(60deg, #E60013, #FCC800); color: white; }
        .btn-success:hover { box-shadow: 0 6px 15px rgba(230, 0, 19, 0.2); }
        .badge { padding: 6px 12px; border-radius: 12px; font-size: 0.85em; font-weight: bold; }
        .badge-solicitud { background: rgba(252, 200, 0, 0.2); color: #b38f00; border: 1px solid #FCC800; }
        .badge-proceso { background: rgba(39, 174, 96, 0.1); color: #27ae60; border: 1px solid #27ae60; }
    </style>

    <div class="container">
        <h2>Listado de Trámites</h2>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'pago_validado'): ?>
            <div style="background: rgba(39, 174, 96, 0.1); color: #27ae60; padding: 15px; border-radius: 6px; border-left: 4px solid #27ae60; margin-bottom: 20px; font-weight: 600;">
                ✓ Pago validado correctamente. Se ha desbloqueado el formato de Servicio Social para el alumno.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Matrícula</th>
                    <th>Empresa</th>
                    <th>Estado Actual</th>
                    <th>Fecha de Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tramites)): ?>
                    <tr><td colspan="6" style="text-align: center; color: #666; padding: 30px;">No hay trámites registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($tramites as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellidos']) ?></td>
                            <td><?= htmlspecialchars($t['matricula']) ?></td>
                            <td><?= htmlspecialchars($t['empresa_nombre'] ?: $t['empresa_bd_nombre']) ?></td>
                            <td>
                                <?php 
                                    $estado = $t['estado_tramite']; 
                                    if ($estado === 'solicitud_creditos') {
                                        echo '<span class="badge badge-solicitud">Falta Pago en Cajas</span>';
                                    } else {
                                        echo '<span class="badge badge-proceso">Pago Validado</span>';
                                    }
                                ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($t['fecha_solicitud'])) ?></td>
                            <td>
                                <?php if ($estado === 'solicitud_creditos'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="validar_pago">
                                        <input type="hidden" name="id_tramite" value="<?= $t['id_tramite'] ?>">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('¿Confirmar que el alumno ha pagado para desbloquear su formato?')">Validar Pago</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #666; font-size: 0.9em;">Completado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once __DIR__ . '/../layouts/alumnos/footer.php'; ?>
