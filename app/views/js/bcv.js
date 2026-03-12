async function consultarBCV() {

    const url = 'https://ve.dolarapi.com/v1/dolares/oficial';



    try {

        const respuesta = await fetch(url);

        const data = await respuesta.json();

       

        const valorNumerico = data.venta || data.promedio || 0;



        if (valorNumerico === 0) return;



        // Guardamos la tasa globalmente

        localStorage.setItem('tasa_bcv', valorNumerico);



        // Formato venezolano

        const precio = new Intl.NumberFormat('es-VE', {

            minimumFractionDigits: 2,

            maximumFractionDigits: 2

        }).format(valorNumerico);

       

        const fecha = new Date(data.fechaActualizacion).toLocaleDateString('es-VE');



        // 1. Actualizar tarjeta en el Dashboard (Si existe)

        let bcvValorDash = document.getElementById('valor-bcv-dashboard');

        let bcvFechaDash = document.getElementById('fecha-bcv-dashboard');

        actualizarInterfazBase(valorNumerico, data.fechaActualizacion);



        // EJECUCIÓN INMEDIATA + RETRASO PARA AJAX

        actualizarPreciosTabla(valorNumerico);

        setTimeout(() => actualizarPreciosTabla(valorNumerico), 500);

        setTimeout(() => actualizarPreciosTabla(valorNumerico), 1500);



        if (bcvValorDash) {

            bcvValorDash.innerHTML = `<strong>Bs. ${precio}</strong>`;

            bcvFechaDash.innerText = `Vigente desde: ${fecha}`;

        }



        // 2. Actualizar etiqueta en el Navbar (Si existe)

        let bcvNav = document.getElementById('tasa-bcv-navbar');

        if (bcvNav) {

            bcvNav.innerHTML = `<i class="fas fa-dollar-sign"></i> 1 = Bs. ${precio}`;

        }



        // 3. Guardar en memoria local (Para futuras operaciones matemáticas)

        localStorage.setItem('tasa_bcv', valorNumerico);



    } catch (error) {

        console.error("Error al obtener la tasa BCV:", error);

        let bcvNav = document.getElementById('tasa-bcv-navbar');

        if (bcvNav) bcvNav.innerHTML = "BCV: Error";

    }



    function actualizarPreciosTabla(tasa) {

    const celdas = document.querySelectorAll('.precio-bcv');

    // Si no encuentra celdas, el script no hace nada (evita errores)

    if (celdas.length === 0) return;



    celdas.forEach(celda => {

        const usd = parseFloat(celda.getAttribute('data-usd'));

        if (!isNaN(usd)) {

            const totalBs = (usd * tasa).toLocaleString('es-VE', {

                minimumFractionDigits: 2,

                maximumFractionDigits: 2

            });

            celda.innerHTML = `Bs. ${totalBs}`;

            celda.classList.remove('has-text-grey');

            celda.style.fontWeight = "bold";

        }

    });

}



function actualizarInterfazBase(tasa, fechaActualizacion) {

    const precioFmt = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2 }).format(tasa);

    const fechaFmt = new Date(fechaActualizacion).toLocaleDateString('es-VE');



    let bcvNav = document.getElementById('tasa-bcv-navbar');

    if (bcvNav) bcvNav.innerHTML = `<i class="fas fa-dollar-sign"></i> 1 = Bs. ${precioFmt}`;

   

    let bcvValorDash = document.getElementById('valor-bcv-dashboard');

    if (bcvValorDash) bcvValorDash.innerHTML = `<strong>Bs. ${precioFmt}</strong>`;

}

}



// Cargar al inicio

document.addEventListener('DOMContentLoaded', consultarBCV);

// Actualizar cada 6 horas (21600000 ms)

setInterval(consultarBCV, 21600000);