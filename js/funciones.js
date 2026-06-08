/**
 * PIHCSA - Funciones de Control de Formulario y AJAX
 * Versión: Proveedores 2026
 */

document.addEventListener('DOMContentLoaded', function() {
    // --- 1. VALIDACIÓN DE PRIVACIDAD Y FIRMA ---
    const checkbox = document.getElementById('checkPrivacidad');
    const inputFirma = document.getElementById('inputFirma');
    const boton = document.getElementById('btnFinalizar');

    function validarAceptacion() {
        if (!inputFirma || !checkbox || !boton) return;
        const nombreValido = inputFirma.value.trim().length > 3; 
        const checkAceptado = checkbox.checked;

        if (checkAceptado && nombreValido) {
            boton.disabled = false;
            boton.classList.remove('btn_deshabilitado');
        } else {
            boton.disabled = true;
            boton.classList.add('btn_deshabilitado');
        }
    }

    if (checkbox && inputFirma) {
        checkbox.addEventListener('change', validarAceptacion);
        inputFirma.addEventListener('input', validarAceptacion);
        validarAceptacion();
    }

    // --- 2. FORMATEO DE RFC ---
    const campoRFC = document.querySelector('input[name="rfc"]');
    if (campoRFC) {
        campoRFC.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/\s/g, '');
            if (this.value.length > 13) this.value = this.value.slice(0, 13);
        });

        // --- 3. MOTOR AJAX PARA PROVEEDORES ---
        campoRFC.addEventListener('blur', function() {
            const rfcValue = this.value.trim();
            if (rfcValue.length < 12) return;

            fetch('buscar_proveedor.php?rfc=' + rfcValue)
                .then(response => {
                    if (!response.ok) throw new Error('Error en el servidor');
                    return response.json();
                })
                .then(data => {
                    const camposInteres = [
                        { name: 'razon_social', value: data.razon_social || "" },
                        { name: 'domicilio', value: data.domicilio || "" },
                        { name: 'poblacion', value: data.poblacion || "" },
                        { name: 'colonia', value: data.colonia || "" },
                        { name: 'cp', value: data.cp || "" },
                        { name: 'estado', value: data.estado || "" },
                        { name: 'telefono', value: data.telefono || "" },
                        { name: 'email', value: data.email || "" },
                        { name: 'pagina_web', value: data.pagina_web || "" }
                    ];

                    if (data.existe) {
                        camposInteres.forEach(item => {
                            const campo = document.querySelector(`input[name="${item.name}"]`);
                            if (campo) {
                                campo.value = item.value;
                                if (item.value !== "" && item.value !== null) {
                                    campo.readOnly = true;
                                    campo.style.background = "#e9ecef";
                                    campo.style.color = "#6c757d";
                                    campo.style.cursor = "not-allowed";
                                }
                            }
                        });
                        gestionarChecksVisuales(data.archivos);
                    } else {
                        // Liberar campos si el RFC no existe
                        camposInteres.forEach(item => {
                            const campo = document.querySelector(`input[name="${item.name}"]`);
                            if (campo) {
                                campo.readOnly = false;
                                campo.style.background = "#ffffff";
                                campo.style.color = "#212529";
                                campo.style.cursor = "text";
                            }
                        });
                        document.querySelectorAll('.aviso-archivo-listo').forEach(el => el.remove());
                    }
                })
                .catch(error => console.error('Error AJAX:', error));
        });
    }

    // --- 4. MANEJO DE NOTIFICACIONES URL ---
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error') === 'rfc_existente') {
        alert("⚠️ RFC YA REGISTRADO: Este proveedor ya cuenta con un expediente.");
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (urlParams.get('status') === 'success') {
        const modalExito = document.getElementById('modalExito');
        if (modalExito) modalExito.style.display = 'block';
    }

    // --- 5. INICIALIZACIÓN DE ESTADOS DE ARCHIVOS ---
    try {
        const inLic = document.getElementById('file_licencia');
        const inAvi = document.getElementById('file_aviso_rs');
        if(inLic && inAvi && inLic.files.length === 0) {
            setEstadoAvisoRS(false);
        }
    } catch (e) {
        console.log("Aviso: Campos legales no encontrados en esta vista.");
    }
});

/**
 * FUNCIONES GLOBALES
 */

function cerrarModal() {
    const modal = document.getElementById('modalExito');
    if (modal) {
        modal.style.display = 'none';
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

function marcarAdjunto(input) {
    if (input.files.length > 0) {
        input.style.borderColor = "#28a745";
        input.classList.add('archivo-adjuntado');
        if (input.id === 'file_licencia') setEstadoAvisoRS(true);
    } else {
        input.style.borderColor = "";
        input.classList.remove('archivo-adjuntado');
        if (input.id === 'file_licencia') setEstadoAvisoRS(false);
    }
}

function setEstadoAvisoRS(activo) {
    const inAvi = document.getElementById('file_aviso_rs');
    if (!inAvi) return;
    inAvi.disabled = !activo;
    inAvi.style.opacity = activo ? "1" : "0.4";
    inAvi.style.filter = activo ? "none" : "grayscale(100%)";
    inAvi.style.cursor = activo ? "pointer" : "not-allowed";
    if (!activo) inAvi.value = "";
    if (inAvi.parentElement) inAvi.parentElement.style.opacity = activo ? "1" : "0.5";
}

function seleccionarTipoLegal(tipo) {
    const elementos = {
        btnLic: document.getElementById('btnOpcionLicencia'),
        btnFun: document.getElementById('btnOpcionFuncionamiento'),
        divLic: document.getElementById('campos_licencia'),
        divFun: document.getElementById('campos_funcionamiento'),
        inLic: document.getElementById('file_licencia'),
        inAvi: document.getElementById('file_aviso_rs'),
        inFun: document.getElementById('file_funcionamiento')
    };

    if (tipo === 'licencia') {
        elementos.btnLic.style.background = '#005596'; elementos.btnLic.style.color = 'white';
        elementos.btnFun.style.background = 'white'; elementos.btnFun.style.color = '#005596';
        elementos.divLic.style.display = 'block';
        elementos.divFun.style.display = 'none';
        elementos.inLic.required = true; elementos.inAvi.required = true; elementos.inFun.required = false; 
    } else {
        elementos.btnFun.style.background = '#005596'; elementos.btnFun.style.color = 'white';
        elementos.btnLic.style.background = 'white'; elementos.btnLic.style.color = '#005596';
        elementos.divLic.style.display = 'none';
        elementos.divFun.style.display = 'block';
        elementos.inLic.required = false; elementos.inAvi.required = false; elementos.inFun.required = true;
    }
}

function gestionarChecksVisuales(archivos) {
    const mapeo = {
        'doc_licencia_sanitaria': 'file_licencia',
        'doc_aviso_responsable_sanitario': 'file_aviso_rs',
        'doc_aviso_funcionamiento': 'file_funcionamiento', // Corregido: antes apuntaba a aviso_rs
        'doc_constancia_situacion_fiscal': 'file_constancia',
        'doc_opinion_cumplimiento_sat': 'file_opinion',
        'doc_caratula_cuenta_bancaria': 'file_banco',
        'doc_comprobante_domicilio': 'file_domicilio',
        'doc_registro_sanitario_vigente': 'file_registro_v',
        'doc_ine_representante_legal': 'file_ine_r',
        'doc_ine_responsable_sanitario': 'file_ine_s',
        'img_placa_responsable_sanitario': 'file_placa',
        'img_fachada_calle': 'file_fachada',
        'img_vista_interna_almacen': 'file_almacen'
    };

    for (const [columnaDB, idInput] of Object.entries(mapeo)) {
        const input = document.getElementById(idInput);
        
        if (archivos && archivos[columnaDB] && input) {
            
            // --- LAS LÍNEAS CLAVE PARA QUITAR EL GLOBO NARANJA ---
            input.setCustomValidity(""); // Limpia cualquier mensaje de error manual
            input.required = false;      // Le dice al navegador que ya no es obligatorio subir uno nuevo
            // ----------------------------------------------------

            if (!document.getElementById('check_' + idInput)) {
                const aviso = document.createElement('small');
                aviso.id = 'check_' + idInput;
                aviso.className = 'aviso-archivo-listo';
                aviso.innerHTML = " ✅ Documento ya registrado";
                aviso.style.color = "#28a745";
                aviso.style.display = "block";
                aviso.style.fontWeight = "bold";
                input.after(aviso);
                input.style.borderColor = "#28a745";
            }
        }
    }
}

// --- 6. CONTROL DE PESO TOTAL ---
document.querySelector('form').addEventListener('submit', function(e) {
    const LIMITE_MAXIMO = 8 * 1024 * 1024; 
    let pesoTotal = 0;
    const inputsArchivos = document.querySelectorAll('input[type="file"]');
    
    inputsArchivos.forEach(input => {
        if (input.files.length > 0) {
            pesoTotal += input.files[0].size;
        }
    });

    if (pesoTotal > LIMITE_MAXIMO) {
        e.preventDefault();
        let pesoEnMB = (pesoTotal / (1024 * 1024)).toFixed(2);
        alert(`¡Error! Los archivos pesan ${pesoEnMB} MB. El límite es de 8 MB.`);
    }
});