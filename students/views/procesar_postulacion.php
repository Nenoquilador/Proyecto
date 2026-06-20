<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Postulación</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
</head>
<body class="centrado"> 
    
    <div class="form-card card animate fadeRight" style="text-align: center;"> 
        <h1 style="color: var(--color-js-rojo-principal); font-size: 1.8rem;">
            <?php echo $exito ? '✅ Postulación Exitosa' : '❌ Error de Postulación'; ?>
        </h1>
        
        <div class='mensaje <?php echo $exito ? 'exito' : 'error'; ?>' style="margin-top: 20px;">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        
        <p style="margin-top: 15px; color: var(--color-texto-secundario);">
            Serás redirigido automáticamente. Si no lo haces, haz clic <a href="dashboard.php">aquí</a>.
        </p>
    </div>

</body>
</html>
