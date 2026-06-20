<?php
$page_title = 'Panel de Escolares | Justo Sierra';
$current_page = 'dashboard';
$breadcrumb = 'Resumen';
require_once __DIR__ . '/../layouts/alumnos/header.php';
?>

            <!-- Hero -->
            <div class="welcome-hero">
                <div class="welcome-text">
                    <div class="welcome-eyebrow">Panel de Control</div>
                    <h2>Bienvenido, <?php echo explode(' ', trim(htmlspecialchars($nombre_admin_show)))[0]; ?> 👋</h2>
                    <p>Centro de Control de Servicios Escolares. Aquí tienes una vista rápida del estado de la comunidad estudiantil.</p>
                </div>
                <div class="welcome-badge">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>

            <!-- KPIs -->
            <div class="stats-grid">
                <div class="stat-card red">
                    <div class="stat-card-header">
                        <span class="stat-label">Alumnos Registrados</span>
                        <div class="stat-icon red"><i class="fas fa-user-graduate"></i></div>
                    </div>
                    <div class="stat-value"><?php echo number_format($count_alumnos); ?></div>
                    <div class="stat-sublabel">Total en el sistema</div>
                    <div class="stat-accent-bar"></div>
                </div>

                <div class="stat-card green">
                    <div class="stat-card-header">
                        <span class="stat-label">Currículums en Sistema</span>
                        <div class="stat-icon green"><i class="fas fa-file-invoice"></i></div>
                    </div>
                    <div class="stat-value"><?php echo number_format($count_cvs); ?></div>
                    <div class="stat-sublabel">Documentos cargados</div>
                    <div class="stat-accent-bar"></div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="section-header">
                <h3 class="section-title">Acciones Rápidas</h3>
            </div>
            <div class="actions-grid">
                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-search"></i></div>
                    <div>
                        <h4>Buscar Expediente</h4>
                        <p>Encuentra a un estudiante por nombre o matrícula institucional.</p>
                    </div>
                    <span class="arrow">Ver directorio <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-sync-alt"></i></div>
                    <div>
                        <h4>Actualizar Semestres</h4>
                        <p>Verifica y modifica el avance académico de los estudiantes.</p>
                    </div>
                    <span class="arrow">Ir al directorio <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="gestionar_alumnos.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-file-download"></i></div>
                    <div>
                        <h4>Revisar Documentación</h4>
                        <p>Descarga y revisa los currículums que los alumnos han subido.</p>
                    </div>
                    <span class="arrow">Ver documentos <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="carga_masiva.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-upload"></i></div>
                    <div>
                        <h4>Carga Masiva</h4>
                        <p>Importa nuevos alumnos desde un archivo CSV de manera rápida.</p>
                    </div>
                    <span class="arrow">Ir a carga <i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

<?php require_once __DIR__ . '/../layouts/alumnos/footer.php'; ?>
