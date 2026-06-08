<?php
/**
 * PIHCSA - Consulta AJAX para Existencia de Proveedor
 * Versión: Producción 2026
 */

header('Content-Type: application/json');
include("conexion.php");

$rfc = isset($_GET['rfc']) ? mysqli_real_escape_string($conexion, strtoupper(trim($_GET['rfc']))) : '';

if (!$rfc) {
    echo json_encode(['existe' => false]);
    exit;
}

// Consulta a la tabla correcta de proveedores
$query = "SELECT * FROM providers_form WHERE rfc = '$rfc' LIMIT 1";
$res = mysqli_query($conexion, $query);

if ($row = mysqli_fetch_assoc($res)) {
    // Ofuscación de email para seguridad
    $email_oculto = "";
    if (!empty($row['email'])) {
        $partes = explode("@", $row['email']);
        if (count($partes) == 2) {
            $email_oculto = substr($partes[0], 0, 1) . "***" . substr($partes[0], -1) . "@" . $partes[1];
        }
    }

    echo json_encode([
        'existe' => true,
        'razon_social' => $row['razon_social'],
        'domicilio'    => $row['domicilio'],
        'poblacion'    => $row['poblacion'],
        'colonia'      => $row['colonia'],
        'cp'           => $row['cp'],
        'estado'       => $row['estado'],
        'telefono'     => $row['telefono'],
        'email'        => $email_oculto,
        'pagina_web'   => $row['pagina_web'],
        // Mapeo de archivos para activar los checks verdes en el JS
        'archivos' => [
            'doc_licencia_sanitaria'          => !empty($row['doc_licencia_sanitaria']),
            'doc_aviso_responsable_sanitario' => !empty($row['doc_aviso_responsable_sanitario']),
            'doc_aviso_funcionamiento'        => !empty($row['doc_aviso_funcionamiento']),
            'doc_constancia_situacion_fiscal' => !empty($row['doc_constancia_situacion_fiscal']),
            'doc_opinion_cumplimiento_sat'        => !empty($row['doc_opinion_cumplimiento_sat']),
            'doc_caratula_cuenta_bancaria'           => !empty($row['doc_caratula_cuenta_bancaria']),
            'doc_comprobante_domicilio'       => !empty($row['doc_comprobante_domicilio']),
            'doc_registro_sanitario_vigente'  => !empty($row['doc_registro_sanitario_vigente']),
            'doc_hoja_seguridad_ficha_tecnica'     => !empty($row['doc_hoja_seguridad_ficha_tecnica']),
            'doc_ine_representante_legal'     => !empty($row['doc_ine_representante_legal']),
            'doc_ine_responsable_sanitario'   => !empty($row['doc_ine_responsable_sanitario']),
            'img_placa_responsable_sanitario'           => !empty($row['img_placa_responsable_sanitario']),
            'img_fachada_calle'               => !empty($row['img_fachada_calle']),
            'img_vista_interna_almacen'       => !empty($row['img_vista_interna_almacen'])
        ]
    ]);
} else {
    echo json_encode(['existe' => false]);
}
?>