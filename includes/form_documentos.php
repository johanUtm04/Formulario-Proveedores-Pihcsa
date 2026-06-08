<div class="segmento">
    <h3>2. CARGA DE DOCUMENTOS Y FOTOS</h3>

    <h4 style="color: #005596; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 15px;">Documentación en PDF</h4>

    <div style="display: flex; justify-content: center; margin-bottom: 20px; gap: 0; width: 100%;">
        <button type="button" id="btnOpcionLicencia" onclick="seleccionarTipoLegal('licencia')" style="padding: 12px; border: 1px solid #005596; border-radius: 8px 0 0 8px; cursor: pointer; background: #005596; color: white; font-weight: bold; flex: 1;">Tengo Licencia Sanitaria</button>
        <button type="button" id="btnOpcionFuncionamiento" onclick="seleccionarTipoLegal('funcionamiento')" style="padding: 12px; border: 1px solid #005596; border-radius: 0 8px 8px 0; cursor: pointer; background: white; color: #005596; font-weight: bold; flex: 1;"> Solo Aviso de Funcionamiento</button>
    </div>

    <div id="campos_licencia" style="display: block; background: #f0f7ff; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div class="segmento-campo">
            <p><b>Licencia Sanitaria:</b></p>
            <input type="file" id="file_licencia" name="doc_licencia_sanitaria" accept=".pdf" class="campo_file" onchange="marcarAdjunto(this)">
        </div>
        <div class="segmento-campo">
            <p><b>Aviso Responsable Sanitario:</b></p>
            <input type="file" id="file_aviso_rs" name="doc_aviso_responsable_sanitario" accept=".pdf" class="campo_file" onchange="marcarAdjunto(this)">
        </div>
    </div>

    <div id="campos_funcionamiento" style="display: none; background: #f0f7ff; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div class="segmento-campo">
            <p><b>Aviso de Funcionamiento:</b></p>
            <input type="file" id="file_funcionamiento" name="doc_aviso_funcionamiento" accept=".pdf" class="campo_file" onchange="marcarAdjunto(this)">
        </div>
    </div>

    <div class="segmento-campo">
        <p>Constancia de Situación Fiscal (Actualizada):</p>
        <input type="file" id="file_constancia" class="campo_file" name="doc_constancia_situacion_fiscal" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Opinión de Cumplimiento (SAT):</p>
        <input type="file" id="file_opinion" class="campo_file" name="doc_opinion_cumplimiento_sat" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Carátula de Cuenta Bancaria:</p>
        <input type="file" id="file_banco" class="campo_file" name="doc_caratula_cuenta_bancaria" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Comprobante de Domicilio:</p>
        <input type="file" id="file_domicilio" class="campo_file" name="doc_comprobante_domicilio" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Registro Sanitario Vigente:</p>
        <input type="file" id="file_registro_v" class="campo_file" name="doc_registro_sanitario_vigente" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Hoja de Seguridad / Ficha Técnica:</p>
        <input type="file" id="file_hoja_seg" class="campo_file" name="doc_hoja_seguridad_ficha_tecnica" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>INE Representante Legal:</p>
        <input type="file" id="file_ine_r" class="campo_file" name="doc_ine_representante_legal" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>INE Responsable Sanitario:</p>
        <input type="file" id="file_ine_s" class="campo_file" name="doc_ine_responsable_sanitario" accept=".pdf" onchange="marcarAdjunto(this)">
    </div>

    <h4 style="color: #005596; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 25px;">Evidencia Fotográfica (JPG/PNG)</h4>
    <div class="segmento-campo">
        <p>Foto Placa de Responsable Sanitario:</p>
        <input type="file" id="file_placa" class="campo_file" name="img_placa_responsable_sanitario" accept=".jpg,.jpeg,.png" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Foto Fachada de la Calle:</p>
        <input type="file" id="file_fachada" class="campo_file" name="img_fachada_calle" accept=".jpg,.jpeg,.png" onchange="marcarAdjunto(this)">
    </div>
    <div class="segmento-campo">
        <p>Foto Vista Interna General Almacén:</p>
        <input type="file" id="file_almacen" class="campo_file" name="img_vista_interna_almacen" accept=".jpg,.jpeg,.png" onchange="marcarAdjunto(this)">
    </div>
</div>