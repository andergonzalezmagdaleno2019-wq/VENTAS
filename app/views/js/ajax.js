/* ================================================================
   BOMBA NUCLEAR ANTI-CHROME: DESTRUCTOR DE FORMULARIOS
================================================================ */
document.addEventListener("DOMContentLoaded", function(){

    const formularios = document.querySelectorAll(".FormularioAjax");

    formularios.forEach(form => {
        // FASE 1: DESTRUIR LA ETIQUETA <FORM>
        // Si el elemento es un formulario, lo convertimos en un <div>
        let contenedor;
        let actionURL = form.getAttribute("action");
        let methodType = form.getAttribute("method");

        if(form.tagName.toLowerCase() === 'form') {
            contenedor = document.createElement('div'); // Creamos un div falso
            
            // Le copiamos todas las clases de diseño de Bulma para que no se dañe la vista
            Array.from(form.attributes).forEach(attr => {
                if(attr.name !== 'action' && attr.name !== 'method') {
                    contenedor.setAttribute(attr.name, attr.value);
                }
            });
            
            // Guardamos la ruta oculta
            contenedor.setAttribute('data-action', actionURL);
            contenedor.setAttribute('data-method', methodType);

            // Mudamos todas las cajas de texto y botones adentro del nuevo div
            while(form.firstChild) {
                contenedor.appendChild(form.firstChild);
            }
            
            // Eliminamos el form original y ponemos el div
            form.parentNode.replaceChild(contenedor, form);
        } else {
            contenedor = form;
        }

        // FASE 2: DESACTIVAR BOTONES SUBMIT
        let botones = contenedor.querySelectorAll('button[type="submit"]');
        botones.forEach(btn => {
            btn.setAttribute('type', 'button'); 
            btn.addEventListener("click", function(e){
                e.preventDefault();
                procesarDatosDeCaja(contenedor);
            });
        });

        // FASE 3: CAPTURAR EL ENTER
        contenedor.addEventListener("keydown", function(e){
            if(e.key === "Enter" && e.target.tagName !== "TEXTAREA"){
                e.preventDefault();
                procesarDatosDeCaja(contenedor);
            }
        });
    });
});

/* ================================================================
   FUNCIÓN MAESTRA DE ENVÍO INVISIBLE
================================================================ */
function procesarDatosDeCaja(contenedor) {
    // 1. Validar campos obligatorios a mano (porque ya no hay form HTML)
    let camposObligatorios = contenedor.querySelectorAll('[required]');
    let todoValido = true;
    camposObligatorios.forEach(campo => {
        if(!campo.value.trim()){
            todoValido = false;
            campo.style.border = "1px solid red"; // Resalta en rojo si está vacío
        } else {
            campo.style.border = "";
        }
    });

    if(!todoValido){
        Swal.fire('Atención', 'Por favor, llena todos los campos obligatorios marcados en rojo.', 'warning');
        return;
    }

    // 2. Alerta de confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Quieres realizar la acción solicitada",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, realizar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed){
            
            // 3. Empaquetar datos como un fantasma
            let data = new FormData();
            let inputs = contenedor.querySelectorAll("input, select, textarea");
            
            inputs.forEach(input => {
                if(input.name){
                    if(input.type === "checkbox" || input.type === "radio"){
                        if(input.checked) data.append(input.name, input.value);
                    } else if (input.type === "file"){
                        if(input.files.length > 0) data.append(input.name, input.files[0]);
                    } else {
                        data.append(input.name, input.value);
                    }
                }
            });

            let config = {
                method: contenedor.getAttribute("data-method"),
                body: data
            };

            // 4. Enviar a PHP
            fetch(contenedor.getAttribute("data-action"), config)
            .then(respuesta => respuesta.json())
            .then(respuesta => { 
                return alertas_ajax(respuesta, contenedor);
            })
            .catch(error => {
                console.error("Error AJAX:", error);
                Swal.fire({ icon: 'error', title: 'Ocurrió un error', text: 'No se pudo procesar. Revisa la consola.' });
            });
        }
    });
}

/* ================================================================
   MANEJO DE ALERTAS Y LIMPIEZA
================================================================ */
function alertas_ajax(alerta, contenedorActual){
    if(alerta.tipo == "simple"){
        Swal.fire({ icon: alerta.icono, title: alerta.titulo, text: alerta.texto, confirmButtonText: 'Aceptar' });
    }else if(alerta.tipo == "recargar"){
        Swal.fire({ icon: alerta.icono, title: alerta.titulo, text: alerta.texto, confirmButtonText: 'Aceptar' }).then((result) => {
            if(result.isConfirmed){ location.reload(); }
        });
    }else if(alerta.tipo == "limpiar"){
        Swal.fire({ icon: alerta.icono, title: alerta.titulo, text: alerta.texto, confirmButtonText: 'Aceptar' }).then((result) => {
            if(result.isConfirmed){ 
                // Limpieza manual de las cajas
                let limpiarCampos = contenedorActual.querySelectorAll("input, textarea");
                limpiarCampos.forEach(c => {
                    if(c.type !== 'hidden' && c.type !== 'button') c.value = '';
                });
            }
        });
}else if(alerta.tipo == "redireccionar"){
        // Agregué el Swal.fire para que el usuario vea el éxito antes de irse
        Swal.fire({ 
            icon: alerta.icono, 
            title: alerta.titulo, 
            text: alerta.texto, 
            confirmButtonText: 'Aceptar' 
        }).then((result) => {
            if(result.isConfirmed){ 
                window.location.href = alerta.url; 
            }
        });
    }
}

/* Boton cerrar sesion */
let btn_exit = document.querySelectorAll(".btn-exit");
btn_exit.forEach(exitSystem => {
    exitSystem.addEventListener("click", function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Quieres salir del sistema?',
            text: "La sesión actual se cerrará",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = this.getAttribute("href");
            }
        });
    });
});