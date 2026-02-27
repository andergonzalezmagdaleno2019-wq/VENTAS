async function consultarBCV() {
    const url = 'https://ve.dolarapi.com/v1/dolares/oficial';

    try {
        const respuesta = await fetch(url);
        const data = await respuesta.json();
        
        const valorNumerico = data.venta || data.promedio || 0;

        if (valorNumerico === 0) return;

        // Formato venezolano
        const precio = new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(valorNumerico);
        
        const fecha = new Date(data.fechaActualizacion).toLocaleDateString('es-VE');

        // 1. Actualizar tarjeta en el Dashboard (Si existe)
        let bcvValorDash = document.getElementById('valor-bcv-dashboard');
        let bcvFechaDash = document.getElementById('fecha-bcv-dashboard');
        
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
}

// Cargar al inicio
document.addEventListener('DOMContentLoaded', consultarBCV);
// Actualizar cada 6 horas (21600000 ms)
setInterval(consultarBCV, 21600000);