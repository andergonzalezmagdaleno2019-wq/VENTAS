/* ================================================================
   SCRIPT AJAX PROFESIONAL (ESTÁNDAR FETCH API)
================================================================ */

const formularios_ajax = document.querySelectorAll(".FormularioAjax");

function enviar_formulario_ajax(e){
    // Pausa el envío tradicional para hacerlo por Ajax
    e.preventDefault();

    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");

    let config = {
        method: method,
        mode: 'cors',
        cache: 'no-cache',
        body: data
    };

    // Verificamos si es el buscador (el buscador no requiere confirmación)
    let es_buscador = false;
    if(this.querySelector('input[name="modulo_buscador"]')){
        es_buscador = true;
    }

    if(es_buscador){
        fetch(action, config)
        .then(respuesta => respuesta.json())
        .then(respuesta => alertas_ajax(respuesta))
        .catch(error => console.error('Error:', error));
        return;
    }

    // Si es un formulario de Guardar/Actualizar/Eliminar, preguntamos primero
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres realizar la acción solicitada?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, realizar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(action, config)
            .then(respuesta => respuesta.json())
            .then(respuesta => {
                return alertas_ajax(respuesta);
            })
            .catch(error => {
                console.error('Error de Fetch:', error);
                Swal.fire('Error', 'Ocurrió un error en la petición al servidor.', 'error');
            });
        }
    });
}

// Escuchamos el evento 'submit' natural de los formularios
formularios_ajax.forEach(formularios => {
    formularios.addEventListener("submit", enviar_formulario_ajax);
});

/* ================================================================
   MANEJO DE ALERTAS SWEETALERT
================================================================ */
function alertas_ajax(alerta){
    if(alerta.tipo === "simple"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });
    }else if(alerta.tipo === "recargar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                location.reload();
            }
        });
    }else if(alerta.tipo === "limpiar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                // Resetea el formulario activo
                document.querySelector(".FormularioAjax").reset();
                let fileNames = document.querySelectorAll(".file-name");
                if(fileNames) fileNames.forEach(fn => fn.textContent = "JPG, JPEG, PNG. (MAX 5MB)");
            }
        });
    }else if(alerta.tipo === "redireccionar"){
        if (alerta.titulo && alerta.titulo.trim() !== "") {
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
        } else {
            window.location.href = alerta.url;
        }
    }
}

/* ================================================================
   BOTÓN CERRAR SESIÓN
================================================================ */
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

/* ================================================================
   UTILIDAD: ACTUALIZAR TEXTO DEL INPUT FILE (FOTOS)
================================================================ */
let fileInputs = document.querySelectorAll('.file-input');
if(fileInputs.length > 0){
    fileInputs.forEach(input => {
        input.addEventListener('change', () => {
            if(input.files.length > 0){
                let fileName = input.closest('.file').querySelector('.file-name');
                fileName.textContent = input.files[0].name;
            }
        });
    });
}