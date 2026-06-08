<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$sql = "SELECT id, razon_social COLLATE utf8mb4_general_ci AS razon_social, rfc COLLATE utf8mb4_general_ci AS rfc, fecha_registro, 'Cliente' AS tipo FROM clients_form 
        UNION ALL 
        SELECT id, razon_social COLLATE utf8mb4_general_ci AS razon_social, rfc COLLATE utf8mb4_general_ci AS rfc, fecha_registro, 'Proveedor' AS tipo FROM providers_form 
        ORDER BY fecha_registro DESC";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - PIHCSA</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-container {
            width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: #005596;
            color: white;
            padding: 18px 25px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 85, 150, 0.15);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-info-block {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info-block span {
            font-size: 0.95rem;
        }

        .btn-logout {
            background-color: #d9534f;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
            transition: background-color 0.2s ease;
        }

        .btn-logout:hover {
            background-color: #c9302c;
        }

        .section-title h3 {
            color: #005596; 
            border-bottom: 2px solid #005596; 
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .grid-expedientes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .tarjeta-expediente {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 24px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 260px;
            box-sizing: border-box;
        }

        .tarjeta-expediente:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0, 53, 107, 0.1);
            border-color: #005596;
        }

        .top-tarjeta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .icon-folder {
            font-size: 36px;
            line-height: 1;
        }

        .badge-tipo {
            font-size: 0.72rem;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .badge-cliente {
            background-color: #e6fffa;
            color: #00875a;
            border: 1px solid #b3f5e4;
        }

        .badge-proveedor {
            background-color: #fffaf0;
            color: #b76e00;
            border: 1px solid #ffe8cc;
        }

        .tarjeta-titulo {
            font-weight: bold;
            color: #111;
            margin-bottom: 6px;
            font-size: 1.05rem;
            line-height: 1.4;
        }

        .tarjeta-sub {
            color: #555;
            font-size: 0.88rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .tarjeta-sub span {
            display: block;
            margin-top: 4px;
            font-size: 0.78rem;
            color: #888;
        }

        .badge-archivos {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #bae6fd;
        }

        .btn-ver {
            display: block;
            text-align: center;
            background: #005596;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.88rem;
            font-weight: bold;
            transition: background 0.2s ease;
        }

        .btn-ver:hover {
            background: #003d6b;
        }

        .status-empty {
            display: block;
            text-align: center;
            color: #777;
            font-size: 0.85rem;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 4px;
            border: 1px dashed #cbd5e1;
            font-weight: 500;
        }

        .no-data {
            text-align: center;
            color: #64748b;
            padding: 40px;
            grid-column: 1 / -1;
            background: white;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

<div class="admin-container">
    
    <div class="admin-header">
        <h1>Panel de Expedientes Digitales</h1>
        <div class="user-info-block">
            <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['admin_nombre']); ?></strong></span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>

    <div class="section-title">
        <h3>Expedientes Globales Recientes</h3>
    </div>
    
    <div class="grid-expedientes">
        <?php 
        if ($resultado && mysqli_num_rows($resultado) > 0): 
            while($registro = mysqli_fetch_assoc($resultado)): 
                $rfc_registro = $registro['rfc'];
                $tipo_registro = $registro['tipo'];
                $ruta_carpeta = "uploads/" . $rfc_registro;
                $existe_carpeta = is_dir($ruta_carpeta);
                
                $conteo_archivos = 0;
                if ($existe_carpeta) {
                    $archivos = glob($ruta_carpeta . "/*");
                    if ($archivos !== false) {
                        $conteo_archivos = count($archivos);
                    }
                }
                
                $css_badge = ($tipo_registro === 'Cliente') ? 'badge-cliente' : 'badge-proveedor';
        ?>
            <div class="tarjeta-expediente">
                <div>
                    <div class="top-tarjeta">
                        <div class="icon-folder">
                            <?php echo ($existe_carpeta && $conteo_archivos > 0) ? '📁' : '📂'; ?>
                        </div>
                        <span class="badge-tipo <?php echo $css_badge; ?>">
                            <?php echo $tipo_registro; ?>
                        </span>
                    </div>
                    <div class="tarjeta-titulo"><?php echo htmlspecialchars($registro['razon_social']); ?></div>
                    <div class="tarjeta-sub">
                        <strong>RFC:</strong> <?php echo htmlspecialchars($rfc_registro); ?>
                        <span>Registrado: <?php echo $registro['fecha_registro']; ?></span>
                    </div>
                </div>
                
                <div>
                    <?php if ($existe_carpeta): ?>
                        <div class="badge-archivos">
                            <?php echo $conteo_archivos; ?> <?php echo ($conteo_archivos == 1) ? 'archivo cargado' : 'archivos cargados'; ?>
                        </div>
                        <a href="ver_expediente.php?rfc=<?php echo urlencode($rfc_registro); ?>&tipo=<?php echo urlencode($tipo_registro); ?>" class="btn-ver">Ver Documentos</a>
                    <?php else: ?>
                        <span class="status-empty">Accede desde el panel de Cliente</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <div class="no-data">No se han encontrado registros en la base de datos.</div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>