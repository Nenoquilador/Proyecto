<?php
$page_title = 'Carga Masiva de Alumnos | Justo Sierra';
$current_page = 'carga_masiva';
$breadcrumb = 'Alumnos / Carga Masiva';
require_once __DIR__ . '/../layouts/alumnos/header.php';
?>
            <div class="page-header">
                <div class="page-header-eyebrow">Importación</div>
                <h2>Carga Masiva de Alumnos</h2>
                <p>Sube y procesa bases de datos estudiantiles desde un archivo CSV de forma rápida y segura.</p>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $error ? 'error' : 'success'; ?>">
                    <span class="alert-icon">
                        <i class="fas <?php echo $error ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                    </span>
                    <span><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Formato del archivo CSV</h3>
                    <p>La primera fila es el encabezado y será omitida. Las columnas deben estar en este orden exacto:</p>
                </div>
                <div class="card-body" style="padding-top: 0;">
                    <table class="spec-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Campo</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="col-num">1</td><td class="col-field">Matrícula</td><td class="col-desc">Número de matrícula institucional único</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">2</td><td class="col-field">Nombre</td><td class="col-desc">Nombre(s) del estudiante</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">3</td><td class="col-field">Apellidos</td><td class="col-desc">Apellido paterno y materno</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">4</td><td class="col-field">Email</td><td class="col-desc">Correo electrónico institucional</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">5</td><td class="col-field">Carrera</td><td class="col-desc">Nombre completo de la carrera</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">6</td><td class="col-field">Semestre</td><td class="col-desc">Semestre actual (número entero)</td><td><span class="badge-required">Requerido</span></td></tr>
                            <tr><td class="col-num">7</td><td class="col-field">Password Temporal</td><td class="col-desc">Contraseña inicial de acceso al sistema</td><td><span class="badge-required">Requerido</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Subir archivo</h3>
                    <p>Selecciona un archivo <strong>.csv</strong> codificado en UTF-8 desde tu dispositivo.</p>
                </div>
                <div class="card-body">
                    <form action="carga_masiva.php" method="POST" enctype="multipart/form-data" id="upload-form">
                        <?php require_once __DIR__ . '/../../../config/Security.php'; echo Security::getCsrfInput(); ?>
                        
                        <div class="upload-zone" id="upload-zone">
                            <input type="file" name="csv_file" accept=".csv" required id="csv-input">
                            <div class="upload-icon"><i class="fas fa-file-csv"></i></div>
                            <p class="upload-title">Arrastra tu archivo aquí</p>
                            <p class="upload-subtitle">o haz clic para seleccionarlo desde tu equipo</p>
                            <span class="upload-chip">Solo archivos .csv</span>
                        </div>
                        
                        <div class="file-selected-info" id="file-info">
                            <i class="fas fa-check-circle"></i>
                            <span id="file-name-display"></span>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-cloud-arrow-up"></i> Iniciar Procesamiento
                        </button>
                    </form>
                </div>
            </div>

            <?php if (!empty($resultados)): ?>
                <?php
                    $total_ok   = count(array_filter($resultados, fn($r) => strpos($r,'Error')===false && strpos($r,'Omitida')===false));
                    $total_warn = count($resultados) - $total_ok;
                ?>
                <div class="card">
                    <div class="results-header">
                        <span style="font-family:'Outfit'; font-weight:700; font-size:0.95rem;">Resultados del procesamiento</span>
                        <?php if($total_ok > 0): ?>
                            <span class="results-counter counter-ok"><?php echo $total_ok; ?> insertados</span>
                        <?php endif; ?>
                        <?php if($total_warn > 0): ?>
                            <span class="results-counter counter-warn"><?php echo $total_warn; ?> omitidos</span>
                        <?php endif; ?>
                    </div>
                    <div class="results-list">
                        <?php foreach($resultados as $res):
                            $is_warn = strpos($res, 'Error') !== false || strpos($res, 'Omitida') !== false;
                        ?>
                            <div class="result-item <?php echo $is_warn ? 'warn' : 'ok'; ?>">
                                <div class="result-dot"></div>
                                <span><?php echo htmlspecialchars($res, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

    <script>
        const input = document.getElementById('csv-input');
        const zone  = document.getElementById('upload-zone');
        const info  = document.getElementById('file-info');
        const fname = document.getElementById('file-name-display');

        input.addEventListener('change', () => {
            if (input.files.length > 0) {
                fname.textContent = input.files[0].name + ' · ' + (input.files[0].size / 1024).toFixed(1) + ' KB';
                info.style.display = 'flex';
            }
        });
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', e => {
            e.preventDefault(); zone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    </script>
<?php require_once __DIR__ . '/../layouts/alumnos/footer.php'; ?>
