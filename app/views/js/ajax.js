/* Esperamos a que todo el HTML cargue antes de ejecutar el script */
document.addEventListener("DOMContentLoaded", function(){

    /* Buscamos todos los formularios con la clase .FormularioAjax */
    const formularios_ajax = document.querySelectorAll(".FormularioAjax");

    /* Si encontramos formularios, les agregamos el evento */
    formularios_ajax.forEach(formularios => {

        formularios.addEventListener("submit", function(e){
            
            /* 1. DETENEMOS LA RECARGA DE LA PAGINA */
            e.preventDefault();

            /* 2. Mostramos la alerta de confirmación */
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

                    /* 3. Preparamos los datos del formulario */
                    let data = new FormData(this);
                    let method = this.getAttribute("method");
                    let action = this.getAttribute("action");

                    let encabezados = new Headers();

                    let config = {
                        method: method,
                        headers: encabezados,
                        mode: 'cors',
                        cache: 'no-cache',
                        body: data
                    };

                    /* 4. Enviamos los datos al servidor (PHP) */
                    fetch(action, config)
                    .then(respuesta => respuesta.json())
                    .then(respuesta => { 
                        return alertas_ajax(respuesta);
                    })
                    .catch(error => {
                        console.error("Error en la petición AJAX:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error',
                            text: 'No se pudo procesar la solicitud. Revisa la consola para más detalles.'
                        });
                    });
                }
            });

        });

    });

});

/* Función para manejar las respuestas del servidor */
function alertas_ajax(alerta){
    if(alerta.tipo == "simple"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });

    }else if(alerta.tipo == "recargar"){

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

    }else if(alerta.tipo == "limpiar"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                document.querySelector(".FormularioAjax").reset();
            }
        });

    }else if(alerta.tipo == "redireccionar"){
        window.location.href = alerta.url;
    }
}

/* Boton cerrar sesion */
let btn_exit = document.querySelectorAll(".btn-exit");
btn_exit.forEach(exitSystem => {
    exitSystem.addEventListener("click", function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Quieres salir del sistema?',
            text: "La sesión actual se cerrará y saldrás del sistema",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = this.getAttribute("href");
                window.location.href = url;
            }
        });
    });
});