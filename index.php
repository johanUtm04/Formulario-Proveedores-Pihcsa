<?php 
$titulo_pagina = "Registro de Proveedores - PIHCSA";
include 'includes/header.php'; 
?>

<?php include 'includes/politica_privacidad.php'; ?>

<div class="contenedor_formulario">
    <div class="producto-titulo">
        <h3 style="margin: 5px 0 0 0;">REGISTRO DE PROVEEDORES Y EXPEDIENTE DIGITAL</h3>
    </div>

    <form name="registro_pihcsa" action="procesar_proveedor.php" method="post" enctype="multipart/form-data" id="formPihcsa">
        <div class="columnas-flex">
            <?php include 'includes/form_datos_generales.php'; ?>
            <?php include 'includes/form_documentos.php'; ?>
        </div>

        <?php include 'includes/seccion_firma.php'; ?>

        <div style="text-align: center;">
            <input type="submit" id="btnFinalizar" class="btn_pihcsa btn_deshabilitado" value="FINALIZAR REGISTRO" disabled>
        </div>
    </form>
</div>

<div id="modalExito" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <div style="font-size: 60px; color: #28a745; margin-bottom: 15px;">✔</div>
        <h2 style="color: #005596; margin-top: 0; font-family: Arial, sans-serif;">¡Registro Exitoso!</h2>
        <p style="color: #666; font-size: 16px; margin-bottom: 20px;">La información y los documentos del proveedor han sido guardados correctamente.</p>
        <button type="button" onclick="cerrarModal()" style="background: #005596; color: white; border: none; padding: 10px 30px; border-radius: 6px; cursor: pointer; font-weight: bold;">Aceptar</button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>