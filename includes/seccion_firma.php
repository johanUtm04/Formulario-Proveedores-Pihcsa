        <div class="contenedor-privacidad" style="flex-direction: column; gap: 15px;">
            <div style="display: flex; align-items: center; justify-content: center; width: 100%;">
                <input type="checkbox" id="checkPrivacidad" style="width: 20px; height: 20px; margin-right: 10px;">
                <label for="checkPrivacidad" style="font-size: 14px; color: #444;">
                    He leído y acepto el <a href="javascript:void(0)" onclick="document.getElementById('modalPrivacidad').style.display='block'" style="color: #005596; font-weight: bold; text-decoration: underline;">Aviso de Privacidad</a> de PIHCSA PARA HOSPITALES.
                </label>
            </div>

            <div style="width: 100%; max-width: 400px; text-align: center; border-top: 1px solid #ddd; padding-top: 15px;">
                <p style="font-size: 12px; color: #666; margin-bottom: 5px; text-transform: uppercase;">Escriba su nombre completo (Firma autógrafa digital)</p>
                <input type="text" 
                       name="firma_digital" 
                       id="inputFirma" 
                       placeholder="Nombre y Apellidos" 
                       style="width: 100%; padding: 8px; border: none; border-bottom: 2px solid #005596; background: transparent; text-align: center; font-family: 'Courier New', Courier, monospace; font-size: 16px; outline: none;" 
                       required>
            </div>
        </div>