<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['rfc']) || empty(trim($_GET['rfc']))) {
    header("Location: admin.php?error=missing_rfc");
    exit;
}

require_once 'conexion.php';
$rfc = strtoupper(trim($_GET['rfc']));
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : 'Cliente';

$tabla = ($tipo === 'Proveedor') ? 'providers_form' : 'clients_form';

$stmt = mysqli_prepare($conexion, "SELECT * FROM {$tabla} WHERE rfc = ? LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $rfc);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
} else {
    die("Error interno: No se pudo preparar la consulta a la base de datos.");
}

if (!$resultado || mysqli_num_rows($resultado) === 0) {
    die("Error: El expediente solicitado no existe.");
}

$registro_crudo = mysqli_fetch_assoc($resultado);

// SOLUCIÓN DEL FATAL ERROR: Se cierra la sentencia preparada ($stmt), no la conexión ($conexion)
mysqli_stmt_close($stmt);

// ADAPTADOR DINÁMICO: Normaliza la estructura para la vista unificada
$cliente = [];
if ($tipo === 'Proveedor') {
    $cliente['razon_social'] = $registro_crudo['razon_social'] ?? '';
    $cliente['rfc']          = $registro_crudo['rfc'] ?? '';
    $cliente['email']        = $registro_crudo['email'] ?? '';
    $cliente['telefono']     = $registro_crudo['telefono'] ?? '';
    $cliente['domicilio']    = $registro_crudo['domicilio'] ?? '';
    $cliente['colonia']      = $registro_crudo['colonia'] ?? '';
    $cliente['cp']           = $registro_crudo['cp'] ?? '';
    $cliente['poblacion']    = $registro_crudo['poblacion'] ?? '';
    $cliente['estado']       = $registro_crudo['estado'] ?? '';
    
    // Documentos comunes mapeados a las columnas reales de providers_form (snake_case)
    $cliente['doc_licencia_sanitaria']         = $registro_crudo['doc_licencia_sanitaria'] ?? '';
    $cliente['doc_aviso_responsableSanitario']   = $registro_crudo['doc_aviso_responsable_sanitario'] ?? '';
    $cliente['doc_aviso_funcionamiento']        = $registro_crudo['doc_aviso_funcionamiento'] ?? '';
    $cliente['doc_comprobante_domicilio']      = $registro_crudo['doc_comprobante_domicilio'] ?? '';
    $cliente['doc_ine_representanteLegal']     = $registro_crudo['doc_ine_representante_legal'] ?? '';
    $cliente['doc_ine_responsableSanitario']    = $registro_crudo['doc_ine_responsable_sanitario'] ?? '';
    
    // Documentos exclusivos de proveedores
    $cliente['doc_situacion_fiscal']           = $registro_crudo['doc_situacion_fiscal'] ?? ''; 
    $cliente['doc_opinion_cumplimiento_sat']       = $registro_crudo['doc_opinion_cumplimiento_sat'] ?? '';
    $cliente['doc_caratula_cuenta_bancaria']          = $registro_crudo['doc_caratula_cuenta_bancaria'] ?? '';
    $cliente['doc_registro_sanitario_vigente']  = $registro_crudo['doc_registro_sanitario_vigente'] ?? '';
    $cliente['doc_hoja_seguridad_ficha_tecnica']     = $registro_crudo['doc_hoja_seguridad_ficha_tecnica'] ?? '';

    // Estructura de imágenes para proveedor
    $cliente['img_placa_responsable_sanitario']          = $registro_crudo['img_placa_responsable_sanitario'] ?? '';
    $cliente['img_fachada']                    = $registro_crudo['img_fachada_calle'] ?? '';
    $cliente['img_almacen']                    = $registro_crudo['img_vista_interna_almacen'] ?? '';
    $cliente['privacy_agreement_accepted']                  = $registro_crudo['privacy_agreement_accepted'] ?? null;
} else {
    // Si es Cliente, pasamos el arreglo directo pero inicializamos los campos extras vacíos para evitar 'Undefined index'
    $cliente = $registro_crudo;
    $cliente['doc_situacion_fiscal']           = '';
    $cliente['doc_opinion_cumplimiento_sat']       = '';
    $cliente['doc_caratula_cuenta_bancaria']          = '';
    $cliente['doc_registro_sanitario_vigente']  = '';
    $cliente['doc_hoja_seguridad_ficha_tecnica']     = '';
    $cliente['img_placa_responsable_sanitario']          = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente - <?php echo htmlspecialchars($cliente['razon_social']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #ddd;
        }
        .header-box {
            border-bottom: 2px solid #005596;
            padding-bottom: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-box h2 {
            color: #005596;
            margin: 0;
            text-transform: uppercase;
            font-size: 22px;
        }
        .btn-return {
            background: #6c757d;
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-return:hover {
            background: #5a6268;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }
        .info-group label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-group div {
            font-size: 15px;
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .docs-section h3 {
            color: #005596;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            font-size: 16px;
            margin-top: 25px;
        }
        .docs-list {
            list-style: none;
            padding: 0;
        }
        .docs-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #fff;
            border: 1px solid #eee;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .btn-view {
            background: #005596;
            color: #fff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-view:hover {
            background: #003d6b;
        }
        .no-doc {
            color: #999;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <h2>Expediente Digital <?php echo htmlspecialchars($tipo); ?></h2>
        <a href="admin.php" class="btn-return">← Volver al Panel</a>
    </div>

    <div class="info-grid">
        <div class="info-group">
            <label>Razón Social</label>
            <div><?php echo htmlspecialchars($cliente['razon_social']); ?></div>
        </div>
        <div class="info-group">
            <label>RFC</label>
            <div><?php echo htmlspecialchars($cliente['rfc']); ?></div>
        </div>
        <div class="info-group">
            <label>Email</label>
            <div><?php echo htmlspecialchars($cliente['email'] ?? 'No registrado'); ?></div>
        </div>
        <div class="info-group">
            <label>Teléfono</label>
            <div><?php echo htmlspecialchars($cliente['telefono'] ?? 'No registrado'); ?></div>
        </div>
        <div class="info-group" style="grid-column: span 2;">
            <label>Domicilio Completo</label>
            <div><?php echo htmlspecialchars($cliente['domicilio'] . ', ' . $cliente['colonia'] . ', C.P. ' . $cliente['cp'] . ', ' . $cliente['poblacion'] . ', ' . $cliente['estado']); ?></div>
        </div>
    </div>

    <div class="docs-section">
        <h3>Documentación Legal (PDF)</h3>
        <ul class="docs-list">
            <li>
                <span>Licencia Sanitaria</span>
                <?php if (!empty($cliente['doc_licencia_sanitaria'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_licencia_sanitaria']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <li>
                <span>Aviso de Responsable Sanitario</span>
                <?php if (!empty($cliente['doc_aviso_responsableSanitario'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_aviso_responsableSanitario']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <li>
                <span>Aviso de Funcionamiento</span>
                <?php if (!empty($cliente['doc_aviso_funcionamiento'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_aviso_funcionamiento']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <li>
                <span>INE Responsable Sanitario</span>
                <?php if (!empty($cliente['doc_ine_responsableSanitario'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_ine_responsableSanitario']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <li>
                <span>INE Representante Legal</span>
                <?php if (!empty($cliente['doc_ine_representanteLegal'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_ine_representanteLegal']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <li>
                <span>Comprobante de Domicilio</span>
                <?php if (!empty($cliente['doc_comprobante_domicilio'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_comprobante_domicilio']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                <?php else: ?>
                    <span class="no-doc">No cargado</span>
                <?php endif; ?>
            </li>

            <?php if ($tipo === 'Proveedor'): ?>
                <li>
                    <span>Constancia de Situación Fiscal (SAT)</span>
                    <?php if (!empty($cliente['doc_situacion_fiscal'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_situacion_fiscal']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                    <?php else: ?>
                        <span class="no-doc">No cargado</span>
                    <?php endif; ?>
                </li>

                <li>
                    <span>Opinión de Cumplimiento (SAT)</span>
                    <?php if (!empty($cliente['doc_opinion_cumplimiento_sat'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_opinion_cumplimiento_sat']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                    <?php else: ?>
                        <span class="no-doc">No cargado</span>
                    <?php endif; ?>
                </li>

                <li>
                    <span>Carátula de Cuenta Bancaria</span>
                    <?php if (!empty($cliente['doc_caratula_cuenta_bancaria'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_caratula_cuenta_bancaria']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                    <?php else: ?>
                        <span class="no-doc">No cargado</span>
                    <?php endif; ?>
                </li>

                <li>
                    <span>Registro Sanitario Vigente</span>
                    <?php if (!empty($cliente['doc_registro_sanitario_vigente'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_registro_sanitario_vigente']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                    <?php else: ?>
                        <span class="no-doc">No cargado</span>
                    <?php endif; ?>
                </li>

                <li>
                    <span>Hoja de Seguridad / Ficha Técnica</span>
                    <?php if (!empty($cliente['doc_hoja_seguridad_ficha_tecnica'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['doc_hoja_seguridad_ficha_tecnica']); ?>" target="_blank" class="btn-view">Ver Documento</a>
                    <?php else: ?>
                        <span class="no-doc">No cargado</span>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        </ul>

        <h3>Evidencia Fotográfica e Identidad</h3>
        <ul class="docs-list">
            <?php if ($tipo === 'Proveedor'): ?>
                <li>
                    <span>Fotografía Placa Responsable Sanitario</span>
                    <?php if (!empty($cliente['img_placa_responsable_sanitario'])): ?>
                        <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['img_placa_responsable_sanitario']); ?>" target="_blank" class="btn-view" style="background: #e67e22;">Ver Imagen</a>
                    <?php else: ?>
                        <span class="no-doc">Sin Imagen</span>
                    <?php endif; ?>
                </li>
            <?php endif; ?>

            <li>
                <span>Fotografía de la Fachada</span>
                <?php if (!empty($cliente['img_fachada'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['img_fachada']); ?>" target="_blank" class="btn-view" style="background: #e67e22;">Ver Imagen</a>
                <?php else: ?>
                    <span class="no-doc">Sin Imagen</span>
                <?php endif; ?>
            </li>

            <li>
                <span>Fotografía Vista Interna Almacén</span>
                <?php if (!empty($cliente['img_almacen'])): ?>
                    <a href="leer_documento.php?rfc=<?php echo urlencode($cliente['rfc']); ?>&archivo=<?php echo urlencode($cliente['img_almacen']); ?>" target="_blank" class="btn-view" style="background: #e67e22;">Ver Imagen</a>
                <?php else: ?>
                    <span class="no-doc">Sin Imagen</span>
                <?php endif; ?>
            </li>
<!-- 
            <li>
                <span>Firma Digital Custodiada</span>
                <?php if (!empty($cliente['privacy_agreement_accepted'])): ?>
                    <span class="btn-view" style="background: #28a745; cursor: default;">✓ Registrada</span>
                <?php else: ?>
                    <span class="no-doc" style="color: #dc3545; font-weight: bold;">Falta Firma</span>
                <?php endif; ?>
            </li> -->
        </ul>
    </div>
</div>

</body>
</html>