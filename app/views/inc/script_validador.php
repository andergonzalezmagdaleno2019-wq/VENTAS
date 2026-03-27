<script>
document.addEventListener('DOMContentLoaded', function() {
    
    /* 1. INTERCEPTOR DE MENSAJES NATIVOS DEL NAVEGADOR */
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('invalid', function() {
            if (!this.value) {
                this.setCustomValidity('Este campo es obligatorio, por favor llénalo.');
            } else if (this.dataset.filtro === 'rif' && this.value.length < 8) {
                this.setCustomValidity('El RIF está incompleto. Faltan números (Mínimo 8 caracteres).');
            } else if (this.classList.contains('only-numbers') && this.value.length < 7) {
                this.setCustomValidity('El teléfono está incompleto. Faltan números (Deben ser 7 dígitos exactos).');
            } else if (this.hasAttribute('title')) {
                this.setCustomValidity(this.getAttribute('title'));
            } else {
                this.setCustomValidity('Formato incorrecto o datos incompletos.');
            }
        });

        // Limpia el error apenas el usuario empieza a corregirlo
        input.addEventListener('input', function() {
            this.setCustomValidity(''); 
        });
    });
    /* 2. DINAMISMO DEL DOCUMENTO DE IDENTIDAD (Cédulas, RIF) */

    const docTipo = document.getElementById('proveedor_rif_tipo') || document.getElementById('doc_tipo');
    const docNumero = document.getElementById('proveedor_rif_numero') || document.getElementById('doc_numero');

    if(docTipo && docNumero) {
        function ajustarDocumento() {
            let tipo = docTipo.value;
            docNumero.classList.remove('is-danger');
            
            if (tipo === 'V' || tipo === 'E') {
                docNumero.setAttribute('maxlength', '8');
                docNumero.setAttribute('minlength', '7');
                docNumero.setAttribute('pattern', '[0-9]{7,8}');
                docNumero.title = 'La cédula debe tener entre 7 y 8 números exactos.';
                docNumero.placeholder = 'Ej: 12345678 (Solo números)';
                docNumero.dataset.filtro = 'numeros';
            } else if (tipo === 'J' || tipo === 'G') {
                docNumero.setAttribute('maxlength', '11'); 
                docNumero.setAttribute('minlength', '8');
                docNumero.setAttribute('pattern', '[0-9\-]{8,11}');
                docNumero.title = 'El RIF debe tener entre 8 y 10 números. Se permite un guion.';
                docNumero.placeholder = 'Ej: 24892342-1';
                docNumero.dataset.filtro = 'rif';
            } else {
                docNumero.setAttribute('maxlength', '20');
                docNumero.setAttribute('minlength', '5');
                docNumero.setAttribute('pattern', '[a-zA-Z0-9\-]{5,20}');
                docNumero.title = 'Ingrese un documento válido (Letras y números).';
                docNumero.placeholder = 'Ej: PAS-123456';
                docNumero.dataset.filtro = 'alfanumerico';
            }
        }

        docTipo.addEventListener('change', function() {
            docNumero.value = ''; 
            ajustarDocumento();
        });
        
        if(docTipo.value !== ""){ ajustarDocumento(); }
    }

    /* 3. PROTECCIÓN DE TECLADO EN TIEMPO REAL */
    document.addEventListener('input', function(e) {
        let input = e.target;
        
        if (input.classList.contains('only-letters')) {
            let original = input.value;
            let limpiado = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, 'No se permiten números ni símbolos.'); }
        }
        
        if (input.classList.contains('only-numbers') || input.dataset.filtro === 'numeros') {
            let original = input.value;
            let limpiado = original.replace(/[^0-9]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, '¡Solo se permiten números en esta casilla!'); }
        }
        
        if (input.dataset.filtro === 'alfanumerico') {
            let original = input.value;
            let limpiado = original.replace(/[^a-zA-Z0-9\-]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, 'Sin símbolos extraños, solo letras, números y guiones.'); }
        }
        
        if (input.dataset.filtro === 'usuario') {
            let original = input.value;
            let limpiado = original.replace(/[^a-zA-Z0-9_]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, 'Solo letras, números y guión bajo (_).'); }
        }

        // --- NUEVAS REGLAS PARA PROVEEDORES ---
        if (input.dataset.filtro === 'rif') {
            let original = input.value;
            let limpiado = original.replace(/[^0-9\-]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, 'El RIF solo permite números y guiones (-).'); }
        }
        
        if (input.dataset.filtro === 'empresa') {
            let original = input.value;
            let limpiado = original.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/\s]/g, '');
            if (original !== limpiado) { input.value = limpiado; mostrarAviso(input, 'Símbolo no permitido detectado y eliminado.'); }
        }
    });

    /* 4. ALERTA VISUAL (Globo rojo flotante) */
    function mostrarAviso(elemento, mensaje) {
        let contenedor = elemento.closest('.control');
        if(!contenedor) return;
        let aviso = contenedor.querySelector('.aviso-tiempo-real');
        if (!aviso) {
            aviso = document.createElement('p');
            aviso.className = 'help is-danger aviso-tiempo-real has-text-weight-bold';
            aviso.style.position = 'absolute';
            aviso.style.bottom = '-20px';
            aviso.style.left = '0';
            aviso.style.zIndex = '10';
            contenedor.appendChild(aviso);
            contenedor.style.position = 'relative';
            contenedor.style.marginBottom = '20px';
        }
        aviso.innerText = mensaje;
        aviso.style.display = 'block';
        elemento.classList.add('is-danger');
        clearTimeout(elemento.timeoutAviso);
        elemento.timeoutAviso = setTimeout(() => {
            aviso.style.display = 'none';
            elemento.classList.remove('is-danger');
        }, 2500); 
    }
});
</script>