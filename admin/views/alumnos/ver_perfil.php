<?php
$page_title = 'Perfil del Alumno | Justo Sierra';
$current_page = 'gestionar_alumnos';
$breadcrumb = 'Alumnos / Perfil #' . ($alumno['id_alumno'] ?? '');
require_once __DIR__ . '/../layouts/alumnos/header.php';
?>
            <?php if (!empty($mensaje_update)): ?>
                <div class="alert alert-<?php echo ($tipo_mensaje === 'success') ? 'success' : 'error'; ?>">
                    <i class="fas <?php echo ($tipo_mensaje === 'success') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> 
                    <div><?php echo htmlspecialchars($mensaje_update); ?></div>
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-banner"></div>
                <div class="profile-header">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($alumno['nombre'] . ' ' . $alumno['apellidos']); ?>&background=FFF1F2&color=E60013&rounded=true&bold=true&size=200" class="profile-avatar">
                    <h2><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h2>
                    <p><?php echo htmlspecialchars($alumno['email']); ?></p>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label><i class="far fa-id-badge"></i> Matrícula Institucional</label>
                        <span><?php echo htmlspecialchars($alumno['matricula']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-graduation-cap"></i> Carrera</label>
                        <span><?php echo htmlspecialchars($alumno['carrera'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="far fa-calendar-alt"></i> Registrado el</label>
                        <span><?php echo date('d/m/Y', strtotime($alumno['fecha_registro'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fab fa-linkedin" style="color: #0077B5;"></i> LinkedIn</label>
                        <span>
                            <?php if(!empty($alumno['perfil_linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($alumno['perfil_linkedin']); ?>" target="_blank" style="color:#0077B5; font-weight:600; text-decoration:none;"><i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i> Ver Perfil</a>
                            <?php else: ?> 
                                <span style="color: var(--text-muted);"><i class="fas fa-minus"></i> No disponible</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="info-item" style="grid-column: 1 / -1; max-width: 500px;">
                        <label><i class="fas fa-level-up-alt"></i> Semestre Actual</label>
                        <form method="POST" class="update-box">
                            <select name="semestre">
                                <option value="7" <?php if($alumno['semestre'] == 7) echo 'selected'; ?>>Séptimo (7mo) Semestre</option>
                                <option value="8" <?php if($alumno['semestre'] == 8) echo 'selected'; ?>>Octavo (8vo) Semestre</option>
                            </select>
                            <button type="submit" name="actualizar_semestre" class="btn-primary"><i class="fas fa-sync-alt"></i> Guardar</button>
                        </form>
                    </div>

                    <div class="info-item" style="grid-column: 1 / -1;">
                        <label><i class="fas fa-folder-open"></i> Documentación (Currículum Vitae)</label>
                        <div style="margin-top:15px;">
                            <?php if(!empty($cv_url)): ?>
                                <a href="<?php echo htmlspecialchars($cv_url); ?>" target="_blank" class="btn-outline" style="display: inline-flex; width: auto;"><i class="fas fa-file-pdf" style="color:var(--js-primary);"></i> Ver Documento PDF</a>
                            <?php else: ?>
                                <div style="background: #FFF1F2; color: #E60013; padding: 12px 20px; border-radius: 12px; display: inline-flex; align-items: center; gap: 10px; font-weight: 600;">
                                    <i class="fas fa-exclamation-circle"></i> El alumno no ha subido su CV
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="padding: 25px 40px; background: var(--bg-body); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <a href="gestionar_alumnos.php" class="btn-outline"><i class="fas fa-arrow-left"></i> Volver al Directorio</a>
                    <span style="color:var(--text-muted); font-size:0.85rem; font-weight: 600;">ID Interno: #<?php echo htmlspecialchars($alumno['id_alumno']); ?></span>
                </div>
            </div>
<?php require_once __DIR__ . '/../layouts/alumnos/footer.php'; ?>
