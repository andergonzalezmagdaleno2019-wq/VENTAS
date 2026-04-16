/*Mostrar ocultar menu principal*/
let btn_menu = document.getElementById('btn-menu');

// Agregamos este IF para que solo se ejecute si el botón existe
if (btn_menu) {
    btn_menu.addEventListener("click", function(e){
        e.preventDefault();

        let navLateral = document.getElementById('navLateral');
        let pageContent = document.getElementById('pageContent');

        // Verificamos que navLateral y pageContent también existan antes de tocar clases
        if (navLateral && pageContent) {
            if(navLateral.classList.contains('navLateral-change') && pageContent.classList.contains('pageContent-change')){
                navLateral.classList.remove('navLateral-change');
                pageContent.classList.remove('pageContent-change');
            } else {
                navLateral.classList.add('navLateral-change');
                pageContent.classList.add('pageContent-change');
            }
        }
    });
}

/*Mostrar y ocultar submenus*/
let btn_subMenu=document.querySelectorAll(".btn-subMenu");
btn_subMenu.forEach(subMenu => {
    subMenu.addEventListener("click", function(e){

        e.preventDefault();
        if(this.classList.contains('btn-subMenu-show')){
            this.classList.remove('btn-subMenu-show');
        }else{
            this.classList.add('btn-subMenu-show');
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
  // Functions to open and close a modal
  function openModal($el) {
    $el.classList.add('is-active');
  }

  function closeModal($el) {
    $el.classList.remove('is-active');
  }

  function closeAllModals() {
    (document.querySelectorAll('.modal') || []).forEach(($modal) => {
      closeModal($modal);
    });
  }

  // Add a click event on buttons to open a specific modal
  (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
    const modal = $trigger.dataset.target;
    const $target = document.getElementById(modal);

    $trigger.addEventListener('click', () => {
      openModal($target);
    });
  });

  // Add a click event on various child elements to close the parent modal
  (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
    const $target = $close.closest('.modal');

    $close.addEventListener('click', () => {
      closeModal($target);
    });
  });

  // Add a keyboard event to close all modals
  document.addEventListener('keydown', (event) => {
    if (event.code === 'Escape') {
      closeAllModals();
    }
  });
    
  /* Capitalizar la primera letra de los inputs automáticamente */
  document.addEventListener('input', (e) => {
      if (e.target.matches('.input') || e.target.matches('.textarea')) {
          
          // Creamos una lista de palabras prohibidas para capitalizar
          const id = e.target.id.toLowerCase();
          const esCampoProtegido = id.includes('clave') || 
                                  id.includes('email') || 
                                  e.target.type === 'password' || 
                                  e.target.type === 'email';

          // Si es alguno de estos, SALIMOS del script
          if (esCampoProtegido) return;

          let valor = e.target.value;
          if (valor.length > 0) {
              e.target.value = valor.charAt(0).toUpperCase() + valor.slice(1);
          }
      }
  });
});
/*----------  RESALTADO DINÁMICO ----------*/
window.addEventListener('load', function() {
    // 1. Obtenemos la vista actual desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    let vista = urlParams.get('views');

    if (!vista) return; // Si no hay vista, no hacemos nada

    // 2. Mapeo para agrupar búsquedas y actualizaciones con sus listas
    let target = vista;
    if (["userSearch", "userUpdate"].includes(vista)) target = "userList";
    if (["clientSearch", "clientUpdate"].includes(vista)) target = "clientList";
    if (["productSearch", "productUpdate"].includes(vista)) target = "productList";
    if (["saleSearch", "saleUpdate"].includes(vista)) target = "saleList";
    if (["categorySearch", "categoryUpdate"].includes(vista)) target = "categoryList";

    // 3. Buscamos el enlace (debes tener el atributo data-link en tu HTML)
    const activeLink = document.querySelector(`[data-link="${target}"]`);

    if (activeLink) {
        // Aplicamos el estilo de resaltado (Azul Bulma y texto blanco)
        activeLink.style.setProperty('background-color', '#3273dc', 'important');
        activeLink.style.setProperty('color', '#ffffff', 'important');
        activeLink.style.setProperty('border-left', '6px solid #fce473', 'important'); // Borde amarillo

        // 4. Forzamos que el submenú se abra
        const parentUl = activeLink.closest('.sub-menu-options');
        if (parentUl) {
            parentUl.style.display = "block";
            // También rotamos la flechita del botón padre si existe
            const btnPadre = parentUl.previousElementSibling;
            if (btnPadre && btnPadre.classList.contains('btn-subMenu')) {
                btnPadre.classList.add('btn-subMenu-show');
                btnPadre.style.color = "#3273dc";
            }
        }
    }
});