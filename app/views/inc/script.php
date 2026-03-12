<script src="<?php echo APP_URL; ?>app/views/js/sweetalert2.all.min.js" ></script>

<script src="<?php echo APP_URL; ?>app/views/js/ajax.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/bcv.js"></script>

<script src="<?php echo APP_URL; ?>app/views/js/sweetalert2.all.min.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/ajax.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/bcv.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btnTheme = document.getElementById("theme-toggle");
    const iconContainer = document.querySelector("#theme-toggle .icon");
    const bodyElement = document.body;

    //Rutas de los SVG para evitar recargas innecesarias
const svgMoon = `<svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-moon-stars" viewBox="0 0 16 16">
  <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278M4.858 1.311A7.27 7.27 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.32 7.32 0 0 0 5.205-2.162q-.506.063-1.029.063c-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286"/>
  <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
</svg>`;

const svgSun = `<svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-brightness-low" viewBox="0 0 16 16">
  <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8m.5-9.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 11a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m5-5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m-11 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m9.743-4.036a.5.5 0 1 1-.707-.707.5.5 0 0 1 .707.707m-7.779 7.779a.5.5 0 1 1-.707-.707.5.5 0 0 1 .707.707m7.072 0a.5.5 0 1 1 .707-.707.5.5 0 0 1-.707.707M3.757 4.464a.5.5 0 1 1 .707-.707.5.5 0 0 1-.707.707"/>
</svg>`;
    /* 2. Función para aplicar el tema (se usa al cargar y al hacer clic) */
    const applyTheme = (theme) => {
        if (theme === "dark") {
                bodyElement.classList.add("dark-mode");
                if (iconContainer) iconContainer.innerHTML = svgSun;
        } else {
                bodyElement.classList.remove("dark-mode");
                if (iconContainer) iconContainer.innerHTML = svgMoon;
        }
    };

    /* 3. Ejecución inmediata al cargar la página */
    const savedTheme = localStorage.getItem("theme") || "light";
    applyTheme(savedTheme);

    /* 4. Evento de clic principal */
    if (btnTheme) {
        btnTheme.addEventListener("click", (e) => {
            e.preventDefault(); // Evita saltos de página si es un <a>
            
            // Alternar clase
            bodyElement.classList.toggle("dark-mode");
            const isDarkNow = bodyElement.classList.contains("dark-mode");
            
            // Guardar en memoria local
            const newTheme = isDarkNow ? "dark" : "light";
            localStorage.setItem("theme", newTheme);
            
            // Cambiar icono visualmente
            applyTheme(newTheme);

            // --- Lógica de limpieza de tu sistema ---
            try {
                let listaProductos = document.getElementById('lista-productos-filtrados');
                if (listaProductos) listaProductos.innerHTML = ""; 

                document.querySelectorAll('.modal .message').forEach(msg => msg.remove());

                let inputBusqueda = document.getElementById('buscar_codigo');
                if (inputBusqueda) inputBusqueda.value = "";
            } catch (error) {
                console.warn("Error en la limpieza de campos, pero el tema cambió correctamente.");
            }
        });
    } else {
        console.error("Error: No se encontró el elemento con ID 'theme-toggle'. Revisa tu HTML.");
    }
});
</script>