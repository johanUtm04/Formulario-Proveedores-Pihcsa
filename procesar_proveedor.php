<?php
/**
 * PIHCSA - Procesamiento de Registro de Proveedores
 * Versión: Producción Clean Architecture (Fixed Schema & Encodings)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion.php");
require('fpdf/fpdf.php'); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 2. Recibir y sanitizar datos
$razon_social = mysqli_real_escape_string($conexion, $_POST['razon_social']);
$rfc          = mysqli_real_escape_string($conexion, strtoupper(trim($_POST['rfc'])));
$domicilio    = mysqli_real_escape_string($conexion, $_POST['domicilio']);
$poblacion    = mysqli_real_escape_string($conexion, $_POST['poblacion']);
$colonia      = mysqli_real_escape_string($conexion, $_POST['colonia']);
$cp           = mysqli_real_escape_string($conexion, $_POST['cp']);
$estado       = mysqli_real_escape_string($conexion, $_POST['estado']);
$email        = mysqli_real_escape_string($conexion, $_POST['email']);
$telefono     = mysqli_real_escape_string($conexion, $_POST['telefono']);

$web_val = isset($_POST['pagina_web']) ? $_POST['pagina_web'] : (isset($_POST['web']) ? $_POST['web'] : '');
$web = mysqli_real_escape_string($conexion, $web_val);

// Tracking privacy acceptance checkbox (1 or 0)
$privacy_accepted = isset($_POST['politica_privacidad']) ? 1 : 0;

if(empty($rfc)) {
    die("Error: El RFC es obligatorio para procesar el expediente.");
}

// Automatically detect provider selection track based on incoming file streams
$provider_type_selection = isset($_FILES['doc_licencia_sanitaria']) && $_FILES['doc_licencia_sanitaria']['error'] === UPLOAD_ERR_OK ? 'licencia' : 'aviso';

// 3. Preparar Directorio de Archivos
$base_dir = __DIR__ . "/uploads/" . $rfc . "/";
if (!file_exists($base_dir)) { 
    if (!@mkdir($base_dir, 0777, true)) {
        if (!is_writable(__DIR__ . "/uploads/")) {
            die("Error de Permisos: La carpeta 'uploads' no permite escritura.");
        }
    }
}

// 4. Función para subir archivos con nombre forzado
function subirArchivo($file_input, $dest_dir, $nombre_forzado) {
    if (isset($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES[$file_input]['name'], PATHINFO_EXTENSION);
        $nombre_final = $nombre_forzado . "." . $extension;
        
        if (move_uploaded_file($_FILES[$file_input]['tmp_name'], $dest_dir . $nombre_final)) {
            return $nombre_final;
        }
    }
    return null;
}

// 5. Procesar las subidas (Mapped exactly to our database structural names)
$p_lic   = subirArchivo('doc_licencia_sanitaria', $base_dir, 'licencia_sanitaria');
$p_av_rs = subirArchivo('doc_aviso_responsable_sanitario', $base_dir, 'aviso_responsable');
$p_fun   = subirArchivo('doc_aviso_funcionamiento', $base_dir, 'aviso_funcionamiento');
$p_const = subirArchivo('doc_constancia_situacion_fiscal', $base_dir, 'situacion_fiscal');
$p_opin  = subirArchivo('doc_opinion_cumplimiento', $base_dir, 'opinion_sat');
$p_banc  = subirArchivo('doc_caratula_bancaria', $base_dir, 'cuenta_bancaria');
$p_dom   = subirArchivo('doc_comprobante_domicilio', $base_dir, 'domicilio');
$p_reg   = subirArchivo('doc_registro_sanitario_vigente', $base_dir, 'registro_sanitario');
$p_hoja  = subirArchivo('doc_hoja_seguridad_producto', $base_dir, 'hoja_seguridad');
$p_ine_r = subirArchivo('doc_ine_representante_legal', $base_dir, 'ine_representante');
$p_ine_s = subirArchivo('doc_ine_responsable_sanitario', $base_dir, 'ine_responsable_sanitario');
$p_placa = subirArchivo('img_placa_responsable', $base_dir, 'foto_placa');
$p_fac   = subirArchivo('img_fachada_calle', $base_dir, 'foto_fachada');
$p_alm   = subirArchivo('img_vista_interna_almacen', $base_dir, 'foto_almacen');

// 6. Generar el PDF del Comprobante (Fixing utf8_decode with mb_convert_encoding)
$nombre_pdf_privacidad = "AVISO_PRIVACIDAD_FIRMADO.pdf";
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$title_text = mb_convert_encoding('PIHCSA PARA HOSPITALES - REGISTRO DE PROVEEDORES', 'ISO-8859-1', 'UTF-8');
$pdf->Cell(0, 15, $title_text, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$body_content = "Razón Social: $razon_social\nRFC: $rfc\nFecha de Registro: " . date('d/m/Y') . "\n\nPolíticas de Privacidad Aceptadas.";
$body_text = mb_convert_encoding($body_content, 'ISO-8859-1', 'UTF-8');
$pdf->MultiCell(0, 8, $body_text);
$pdf->Output('F', $base_dir . $nombre_pdf_privacidad);

// 7. LÓGICA DE BASE DE DATOS (INSERT O UPDATE)
$checkRFC = "SELECT id FROM providers_form WHERE rfc = '$rfc' LIMIT 1";
$resCheck = mysqli_query($conexion, $checkRFC);
$existe = (mysqli_num_rows($resCheck) > 0);

if ($existe) {
    // MODO UPDATE
    $query = "UPDATE providers_form SET 
        razon_social = '$razon_social', 
        domicilio = '$domicilio', 
        poblacion = '$poblacion', 
        colonia = '$colonia', 
        cp = '$cp', 
        estado = '$estado', 
        pagina_web = '$web', 
        telefono = '$telefono', 
        email = '$email',
        provider_type_selection = '$provider_type_selection',
        privacy_agreement_accepted = $privacy_accepted";
    
    if($p_lic)   $query .= ", doc_licencia_sanitaria = '$p_lic'";
    if($p_av_rs) $query .= ", doc_aviso_responsable_sanitario = '$p_av_rs'";
    if($p_fun)   $query .= ", doc_aviso_funcionamiento = '$p_fun'";
    if($p_const) $query .= ", doc_situacion_fiscal = '$p_const'";
    if($p_opin)  $query .= ", doc_opinion_cumplimiento_sat = '$p_opin'";
    if($p_banc)  $query .= ", doc_caratula_cuenta_bancaria = '$p_banc'";
    if($p_dom)   $query .= ", doc_comprobante_domicilio = '$p_dom'";
    if($p_reg)   $query .= ", doc_registro_sanitario_vigente = '$p_reg'";
    if($p_hoja)  $query .= ", doc_hoja_seguridad_ficha_tecnica = '$p_hoja'";
    if($p_ine_r) $query .= ", doc_ine_representante_legal = '$p_ine_r'";
    if($p_ine_s) $query .= ", doc_ine_responsable_sanitario = '$p_ine_s'";
    if($p_placa) $query .= ", img_placa_responsable_sanitario = '$p_placa'";
    if($p_fac)   $query .= ", img_fachada_calle = '$p_fac'";
    if($p_alm)   $query .= ", img_vista_interna_almacen = '$p_alm'";

    $query .= " WHERE rfc = '$rfc'";
} else {
    // MODO INSERT
    $query = "INSERT INTO providers_form (
        razon_social, domicilio, poblacion, colonia, cp, estado, rfc, pagina_web, telefono, email, 
        provider_type_selection, privacy_agreement_accepted,
        doc_licencia_sanitaria, doc_aviso_responsable_sanitario, doc_aviso_funcionamiento, 
        doc_situacion_fiscal, doc_opinion_cumplimiento_sat, doc_caratula_cuenta_bancaria, 
        doc_comprobante_domicilio, doc_registro_sanitario_vigente, doc_hoja_seguridad_ficha_tecnica, 
        doc_ine_representante_legal, doc_ine_responsable_sanitario, 
        img_placa_responsable_sanitario, img_fachada_calle, img_vista_interna_almacen
    ) VALUES (
        '$razon_social', '$domicilio', '$poblacion', '$colonia', '$cp', '$estado', '$rfc', '$web', '$telefono', '$email', 
        '$provider_type_selection', $privacy_accepted,
        ".($p_lic?"'$p_lic'":"NULL").", ".($p_av_rs?"'$p_av_rs'":"NULL").", ".($p_fun?"'$p_fun'":"NULL").", 
        ".($p_const?"'$p_const'":"NULL").", ".($p_opin?"'$p_opin'":"NULL").", ".($p_banc?"'$p_banc'":"NULL").", 
        ".($p_dom?"'$p_dom'":"NULL").", ".($p_reg?"'$p_reg'":"NULL").", ".($p_hoja?"'$p_hoja'":"NULL").", 
        ".($p_ine_r?"'$p_ine_r'":"NULL").", ".($p_ine_s?"'$p_ine_s'":"NULL").", 
        ".($p_placa?"'$p_placa'":"NULL").", ".($p_fac?"'$p_fac'":"NULL").", ".($p_alm?"'$p_alm'":"NULL")."
    )";
}

// 8. Ejecución Final
if (mysqli_query($conexion, $query)) {
    header("Location: index.php?status=success");
} else {
    die("Critical Error in MariaDB execution: " . mysqli_error($conexion));
}
?>