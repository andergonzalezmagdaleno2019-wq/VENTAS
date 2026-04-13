/* ================================================================
    SCRIPT AJAX PROFESIONAL (ESTÁNDAR FETCH API)
================================================================ */

const formularios_ajax = document.querySelectorAll(".FormularioAjax");

function enviar_formulario_ajax(e){
    e.preventDefault();

    // 'this' ahora se refiere al formulario que disparó el evento
    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");
    let texto_pregunta = this.getAttribute('data-pregunta') || "¿Quieres realizar la acción solicitada?";

    let config = {
        method: method,
        mode: 'cors',
        cache: 'no-cache',
        body: data
    };

    // Verificamos si es buscador
    if(this.querySelector('input[name="modulo_buscador"]')){
        fetch(action, config)
        .then(respuesta => respuesta.json())
        .then(respuesta => alertas_ajax(respuesta))
        .catch(error => console.error('Error:', error));
        return;
    }

    // Confirmación con mensaje dinámico
    Swal.fire({
        title: '¿Estás seguro?',
        text: texto_pregunta,
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
            .then(respuesta => alertas_ajax(respuesta))
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error en el servidor.', 'error');
            });
        }
    });
}

// Escuchamos el evento 'submit' natural de los formularios
document.addEventListener("submit", function(e) {
    if (e.target && e.target.classList.contains("FormularioAjax")) {
        // Ejecutamos la función pasando el contexto correcto
        enviar_formulario_ajax.call(e.target, e);
    }
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