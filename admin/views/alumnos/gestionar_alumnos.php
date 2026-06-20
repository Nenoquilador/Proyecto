<?php
$page_title = 'Directorio Alumnos | Justo Sierra';
$current_page = 'gestionar_alumnos';
$breadcrumb = 'Alumnos / Directorio';
require_once __DIR__ . '/../layouts/alumnos/header.php';
?>
            <div class="page-header">
                <div class="page-header-eyebrow">Directorio</div>
                <h2>Directorio Estudiantil</h2>
                <p>Gestiona, consulta y valida el padrón completo de alumnos.</p>
            </div>

            <div class="search-card">
                <form method="GET" class="search-form">
                    <div class="input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="search-input" placeholder="Nombre, apellido o matrícula..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                    </div>
                    
                    <select name="carrera" class="select-input">
                        <option value="">🏫 Todas las Carreras</option>
                        <?php foreach($lista_carreras as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo (isset($filtro_carrera) && $filtro_carrera == $c) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="btn-search">
                        Explorar
                    </button>
                </form>
            </div>

            <?php if (empty($alumnos)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>Ningún alumno encontrado</h3>
                    <p>No pudimos encontrar estudiantes que coincidan con "<?php echo htmlspecialchars($search_term ?? ''); ?>". Prueba con otros filtros.</p>
                </div>
            <?php else: ?>
                <div class="grid-alumnos">
                    <?php foreach($alumnos as $a): ?>
                        <div class="student-card">
                            <div class="card-header">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($a['nombre'] . ' ' . $a['apellidos']); ?>&background=FFF1F2&color=E60013&rounded=true&bold=true" class="avatar" alt="Avatar">
                                <div>
                                    <div class="student-name"><?php echo htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']); ?></div>
                                    <div class="student-matricula"><i class="far fa-id-badge"></i> <?php echo htmlspecialchars($a['matricula']); ?></div>
                                </div>
                            </div>
                            
                            <div class="badge">
                                <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($a['carrera']); ?>
                            </div>
                            
                            <a href="ver_perfil.php?id=<?php echo $a['id_alumno']; ?>" class="btn-view">
                                Ver Perfil Completo <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
<?php require_once __DIR__ . '/../layouts/alumnos/footer.php'; ?>
