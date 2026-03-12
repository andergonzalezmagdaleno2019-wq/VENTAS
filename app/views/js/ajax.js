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
                // Si el campo tiene nombre de búsqueda, NO procesamos como formulario Ajax
                if(e.target.name === "busqueda_inicial" || e.target.name === "busqueda_eliminar"){
                    return; // Permite que el buscador funcione de forma nativa/tradicional
                }
                
                e.preventDefault();
                procesarDatosDeCaja(contenedor);
            }
        });

        // FASE 4: ACTIVAR BOTONES DE LIMPIAR

        let botonesReset = contenedor.querySelectorAll('button[type="reset"]');
        
        botonesReset.forEach(btn => {

            btn.type = "button"; 
            
            btn.addEventListener("click", function(e){
                e.preventDefault();
                
                // Obtenemos todos los inputs que están dentro del mismo contenedor que el botón
                let campos = contenedor.querySelectorAll("input, select, textarea");

                campos.forEach(campo => {
                    // Limpiamos los valores
                    if (campo.type !== 'hidden') {
                        campo.value = "";
                    }
                    
                    if (campo.tagName.toLowerCase() === "select") {
                        campo.selectedIndex = 0;
                    }

                    if (campo.type === "checkbox" || campo.type === "radio") {
                        campo.checked = false;
                    }

                    campo.style.border = "";
                });

                let textoFoto = contenedor.querySelector(".file-name");
                if(textoFoto) {
                    textoFoto.textContent = "JPG, JPEG, PNG. (MAX 5MB)";
                }

                console.log("Formulario limpiado con éxito.");
            });
        });
    });
});

/* ================================================================
   FUNCIÓN MAESTRA DE ENVÍO INVISIBLE
================================================================ */
function procesarDatosDeCaja(contenedor) {
    // 1. Detectar buscador
    let esBuscador = contenedor.querySelector('input[name="modulo_buscador"]');

    // 2. Validar campos obligatorios
    let camposObligatorios = contenedor.querySelectorAll('[required]');
    let todoValido = true;

    camposObligatorios.forEach(campo => {

        let elementoAResaltar = (campo.tagName === "SELECT") ? campo.parentElement : campo;

        if(!campo.value.trim()){
            todoValido = false;
            elementoAResaltar.classList.add("is-danger"); 
        } else {
            elementoAResaltar.classList.remove("is-danger");
            // Limpieza adicional para el select interno si fuera necesario
            if(campo.tagName === "SELECT") campo.classList.remove("is-danger");
        }
    });

    if(!todoValido){
        Swal.fire('Atención', 'Por favor, llena los campos marcados.', 'warning');
        return;
    }

    // 3. Función de envío (Captura total de datos)
    const realizarEnvio = () => {
        let data = new FormData();
        
        // Seleccionamos TODOS los elementos con atributo name, sin excepciones
        let inputs = contenedor.querySelectorAll("[name]");
        
        inputs.forEach(input => {
            if(input.type === "checkbox" || input.type === "radio"){
                if(input.checked) data.append(input.name, input.value);
            } else if (input.type === "file"){
                if(input.files.length > 0) data.append(input.name, input.files[0]);
            } else {
                // Aquí entran los 'hidden' que el controlador necesita
                data.append(input.name, input.value);
            }
        });

        let config = {
            method: contenedor.getAttribute("data-method") || "POST",
            body: data
        };

        fetch(contenedor.getAttribute("data-action"), config)
        .then(respuesta => respuesta.json())
        .then(respuesta => { 
            // Esto procesará la redirección que vimos en tus videos
            return alertas_ajax(respuesta, contenedor);
        })
        .catch(error => {
            console.error("Error:", error);
        });
    };

    // 4. Ejecución
    if (esBuscador) {
        realizarEnvio();
    } else {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Quieres realizar la acción solicitada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, realizar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                realizarEnvio();
            }
        });
    }
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
    } else if (alerta.tipo == "redireccionar") {
    // Si la alerta tiene título o texto (es un registro/edición), mostramos el Swal
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
        // Si no tiene título (es una búsqueda), redireccionamos DIRECTO sin mostrar nada
        window.location.href = alerta.url;
    }
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