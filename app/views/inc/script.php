<script src="<?php echo APP_URL; ?>app/views/js/sweetalert2.all.min.js" ></script>

<script src="<?php echo APP_URL; ?>app/views/js/ajax.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/bcv.js"></script>

<script src="<?php echo APP_URL; ?>app/views/js/sweetalert2.all.min.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/ajax.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/bcv.js"></script>

<script>
    /* 1. Suavizar el cambio de colores con CSS */
    const style = document.createElement('style');
    style.innerHTML = `
        * { transition: background-color 0.5s ease, border-color 0.5s ease, color 0.3s ease; }
    `;
    document.head.appendChild(style);

    /* 2. Lógica del Modo Oscuro */
    const btnTheme = document.getElementById("theme-toggle");
    const iconTheme = document.getElementById("theme-icon");
    const bodyElement = document.body;

    // Sincronizar icono al cargar la página
    if (bodyElement.classList.contains("dark-mode")) {
        if(iconTheme) iconTheme.classList.replace("fa-moon", "fa-sun");
    }

    // Evento de clic
    if(btnTheme){
        btnTheme.addEventListener("click", () => {
            bodyElement.classList.toggle("dark-mode");
            const isDark = bodyElement.classList.contains("dark-mode");
            
            // Guardar preferencia
            localStorage.setItem("theme", isDark ? "dark" : "light");
            
            // Cambiar icono
            if (isDark) {
                iconTheme.classList.replace("fa-moon", "fa-sun");
            } else {
                iconTheme.classList.replace("fa-sun", "fa-moon");
            }

            let listaProductos = document.getElementById('lista-productos-filtrados');
            if (listaProductos) {
                listaProductos.innerHTML = ""; 
            }


            document.querySelectorAll('.modal .message').forEach(msg => msg.remove());

 
            let inputBusqueda = document.getElementById('buscar_codigo');
            if (inputBusqueda) {
                inputBusqueda.value = "";
            }


        });
    }
</script>