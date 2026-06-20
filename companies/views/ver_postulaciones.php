<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulantes: <?php echo htmlspecialchars($vacante_titulo); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
    <link rel="stylesheet" href="../assets/css/companies_premium.css?v=<?php echo time(); ?>"> 
</head>
<body>
    
    <nav class="navbar-premium">
        <a href="dashboard.php" class="navbar-brand"><span class="brand-js">JS</span> Portal Empresas</a>
        <div class="navbar-links">
            <a href="dashboard.php" class="nav-pill"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="gestion_vacantes.php" class="nav-pill active"><i class="fa-solid fa-briefcase"></i> Vacantes</a>
            <a href="perfil_empresa.php" class="nav-pill"><i class="fa-solid fa-building"></i> Perfil</a>
            <span class="welcome-msg"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($_SESSION['nombre_empresa'] ?? ''); ?></span>
            <a href="../logout.php" class="btn-logout-premium"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </nav>

    <div class="premium-dashboard">
        
        <div class="animate-fade-in btn-back">
            <a href="gestion_vacantes.php" class="btn-premium secondary">
                <i class="fas fa-arrow-left"></i> Volver a Vacantes
            </a>
        </div>

        <h1 class="premium-subtitle" style="margin-top: 0; margin-bottom: 2px; display:flex; justify-content:space-between; align-items:center;">
            <span>Alumnos postulados para:</span>
            <span class="kanban-total-count"><i class="fa-solid fa-users"></i> <?php echo $total_postulantes; ?> Total</span>
        </h1>
        <div style="display:flex; justify-content:space-between; align-items:center;" class="animate-fade-in delay-1">
            <h2 class="premium-title" style="margin-bottom: 6px;">
                <?php echo htmlspecialchars($vacante_titulo); ?>
            </h2>
            <a href="exportar_csv.php?id_vacante=<?php echo $id_vacante; ?>" class="btn-premium" style="background-color: #27ae60; color: white;">
                <i class="fas fa-file-csv"></i> Exportar a CSV
            </a>
        </div>

        <form method="GET" action="ver_postulaciones.php" class="filters-form animate-fade-in delay-1" style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; margin-top: 20px;">
            <input type="hidden" name="id_vacante" value="<?php echo $id_vacante; ?>">
            
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: 600; font-size: 0.875rem; margin-bottom: 5px; display: block; color: var(--text-color);">Carrera:</label>
                <select name="carrera" class="input-premium" style="width: 100%; padding: 10px;">
                    <option value="">Todas las Carreras</option>
                    <?php foreach ($carreras_disponibles as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>" <?php echo ($filtros['carrera'] === $c) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: 600; font-size: 0.875rem; margin-bottom: 5px; display: block; color: var(--text-color);">Estado de postulación:</label>
                <select name="estado" class="input-premium" style="width: 100%; padding: 10px;">
                    <option value="">Todos los Estados</option>
                    <option value="enviada" <?php echo ($filtros['estado'] === 'enviada') ? 'selected' : ''; ?>>Nuevas / Recibidas</option>
                    <option value="vista" <?php echo ($filtros['estado'] === 'vista') ? 'selected' : ''; ?>>Vistas / En Revisión</option>
                    <option value="en_proceso" <?php echo ($filtros['estado'] === 'en_proceso') ? 'selected' : ''; ?>>En Entrevista / Selección</option>
                    <option value="aceptada" <?php echo ($filtros['estado'] === 'aceptada') ? 'selected' : ''; ?>>Aceptados</option>
                    <option value="rechazada" <?php echo ($filtros['estado'] === 'rechazada') ? 'selected' : ''; ?>>No Seleccionados</option>
                </select>
            </div>
            
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn-premium primary" style="padding: 10px 20px; height: 42px;"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?> animate-fade-in">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_db): ?>
            <div class="mensaje error animate-fade-in"><?php echo htmlspecialchars($error_db); ?></div>
        <?php elseif (empty($postulantes)): ?>
            <div class="job-card-empty animate-fade-in delay-2">
                <i class="fas fa-users-slash"></i>
                <h2>No se encontraron postulantes.</h2>
                <p>Intenta con otros filtros o vuelve más tarde.</p>
            </div>
        <?php else: ?>
            
            <div class="job-list-premium animate-fade-in delay-2" style="margin-top: 12px; display: grid; gap: 20px; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php foreach ($postulantes as $p): ?>
                    <div class="premium-job-card" id="post-card-<?php echo $p['id_postulacion']; ?>">
                        <div class="job-card-header">
                            <h2 style="font-size: 1.1rem;"><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']); ?></h2>
                            <p style="font-size: 0.85rem;"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($p['carrera']); ?></p>
                        </div>
                        <div class="job-card-body">
                            <p style="margin-bottom: 5px;"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($p['matricula']); ?></p>
                            <p style="margin-bottom: 5px;"><i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($p['email']); ?>" style="color:var(--text-muted); text-decoration:none;"><?php echo htmlspecialchars($p['email']); ?></a></p>
                            <p style="margin-bottom: 5px;"><i class="far fa-clock"></i> Postulado: <?php echo date('d/M/Y', strtotime($p['fecha_postulacion'])); ?></p>
                            
                            <div style="margin-top: 15px;">
                                <label for="status-<?php echo $p['id_postulacion']; ?>" style="font-weight: 600; font-size: 0.875rem; color: var(--text-color);">Estado:</label>
                                <select id="status-<?php echo $p['id_postulacion']; ?>" onchange="updateStatus(<?php echo $p['id_postulacion']; ?>, this.value)" class="input-premium" style="width: 100%; display: block; padding: 8px; font-size: 0.875rem; margin-top: 5px;">
                                    <option value="enviada" <?php echo ($p['estado_postulacion'] === 'enviada') ? 'selected' : ''; ?>>Nuevas / Recibidas</option>
                                    <option value="vista" <?php echo ($p['estado_postulacion'] === 'vista') ? 'selected' : ''; ?>>Vistas / En Revisión</option>
                                    <option value="en_proceso" <?php echo ($p['estado_postulacion'] === 'en_proceso') ? 'selected' : ''; ?>>En Entrevista / Selección</option>
                                    <option value="aceptada" <?php echo ($p['estado_postulacion'] === 'aceptada') ? 'selected' : ''; ?>>Aceptados</option>
                                    <option value="rechazada" <?php echo ($p['estado_postulacion'] === 'rechazada') ? 'selected' : ''; ?>>No Seleccionados</option>
                                </select>
                                <div id="status-msg-<?php echo $p['id_postulacion']; ?>" style="font-size: 0.875rem; color: #27ae60; display: none; margin-top: 5px;"><i class="fas fa-check"></i> Actualizado correctamente</div>
                            </div>

                            <div class="k-notes-area" id="notes-area-<?php echo $p['id_postulacion']; ?>" style="display: none; margin-top: 15px;">
                                <textarea class="input-premium" id="notes-input-<?php echo $p['id_postulacion']; ?>" placeholder="Escribe notas sobre el alumno..." style="width: 100%; min-height: 80px; margin-bottom: 10px; padding: 10px;"><?php echo htmlspecialchars($p['notas_empresa'] ?? ''); ?></textarea>
                                <button onclick="saveNotes(<?php echo $p['id_postulacion']; ?>)" class="btn-premium primary" style="font-size: 0.875rem; padding: 8px 16px;">
                                    Guardar Notas
                                </button>
                            </div>
                        </div>
                        <div class="job-card-footer" style="padding-top: 10px; border-top: 1px solid var(--border-light);">
                            <div class="table-actions-group" style="display: flex; gap: 10px;">
                                <?php if ($p['cv_url']): ?>
                                <a href="../<?php echo htmlspecialchars($p['cv_url']); ?>" target="_blank" class="btn-premium secondary" title="Ver CV" style="flex: 1; text-align: center;">
                                    <i class="fas fa-file-pdf"></i> Ver CV
                                </a>
                                <?php endif; ?>
                                <button class="btn-premium secondary" onclick="toggleNotes(<?php echo $p['id_postulacion']; ?>)" title="Notas Internas" style="flex: 1;">
                                    <i class="fas fa-sticky-note"></i> Notas
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination-container animate-fade-in delay-2" style="display: flex; justify-content: center; margin-top: 30px; gap: 8px; flex-wrap: wrap;">
                    <?php if ($page > 1): ?>
                        <a href="?id_vacante=<?php echo $id_vacante; ?>&page=<?php echo $page - 1; ?>&carrera=<?php echo urlencode($filtros['carrera']); ?>&estado=<?php echo urlencode($filtros['estado']); ?>" class="btn-premium secondary">&laquo; Anterior</a>
                    <?php endif; ?>
                    
                    <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        if ($start > 1) {
                            echo '<a href="?id_vacante=' . $id_vacante . '&page=1&carrera=' . urlencode($filtros['carrera']) . '&estado=' . urlencode($filtros['estado']) . '" class="btn-premium secondary">1</a>';
                            if ($start > 2) echo '<span style="align-self: center;">...</span>';
                        }
                    ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?id_vacante=<?php echo $id_vacante; ?>&page=<?php echo $i; ?>&carrera=<?php echo urlencode($filtros['carrera']); ?>&estado=<?php echo urlencode($filtros['estado']); ?>" class="btn-premium <?php echo ($i === $page) ? 'primary' : 'secondary'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php 
                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) echo '<span style="align-self: center;">...</span>';
                            echo '<a href="?id_vacante=' . $id_vacante . '&page=' . $total_pages . '&carrera=' . urlencode($filtros['carrera']) . '&estado=' . urlencode($filtros['estado']) . '" class="btn-premium secondary">' . $total_pages . '</a>';
                        }
                    ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?id_vacante=<?php echo $id_vacante; ?>&page=<?php echo $page + 1; ?>&carrera=<?php echo urlencode($filtros['carrera']); ?>&estado=<?php echo urlencode($filtros['estado']); ?>" class="btn-premium secondary">Siguiente &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <script>
                function updateStatus(id, status) {
                    var formData = new FormData();
                    formData.append('ajax_action', 'update_status');
                    formData.append('id_postulacion', id);
                    formData.append('new_status', status);

                    fetch('ver_postulaciones.php?id_vacante=<?php echo $id_vacante; ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var msg = document.getElementById('status-msg-' + id);
                            msg.style.display = 'block';
                            setTimeout(() => { msg.style.display = 'none'; }, 3000);
                        } else {
                            alert('Error al actualizar el estado.');
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión.');
                    });
                }

                function toggleNotes(id) {
                    var area = document.getElementById('notes-area-' + id);
                    area.style.display = (area.style.display === 'none' || area.style.display === '') ? 'block' : 'none';
                }

                function saveNotes(id) {
                    var notas = document.getElementById('notes-input-' + id).value;
                    var btn = document.querySelector('#notes-area-' + id + ' button');
                    var originalText = btn.textContent;
                    
                    btn.textContent = 'Guardando...';
                    btn.style.opacity = '0.7';
                    
                    var formData = new FormData();
                    formData.append('ajax_action', 'update_notes');
                    formData.append('id_postulacion', id);
                    formData.append('notas', notas);

                    fetch('ver_postulaciones.php?id_vacante=<?php echo $id_vacante; ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        btn.style.opacity = '1';
                        if (data.success) {
                            btn.textContent = '¡Guardado!';
                            btn.style.background = '#27ae60';
                            setTimeout(() => {
                                btn.textContent = 'Guardar Notas';
                                btn.style.background = 'var(--js-rojo)';
                                toggleNotes(id);
                            }, 1500);
                        } else {
                            btn.textContent = 'Error';
                            setTimeout(() => btn.textContent = originalText, 2000);
                        }
                    })
                    .catch(error => {
                        btn.style.opacity = '1';
                        btn.textContent = 'Error';
                        setTimeout(() => btn.textContent = originalText, 2000);
                    });
                }
            </script>

        <?php endif; ?>
    </div>

    <footer class="footer-premium">
        <div class="footer-brand">Universidad <span class="accent">Justo Sierra</span></div>
        <p>Portal de Empresas &mdash; Educar para la Vida</p>
    </footer>

</body>
</html>



