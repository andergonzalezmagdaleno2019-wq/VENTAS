<div class="container is-fluid mb-6">
	<h1 class="title">Copias de Seguridad</h1>
	<h2 class="subtitle"><i class="fas fa-database fa-fw"></i> &nbsp; Realizar respaldo</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        
        if($_SESSION['rol'] != 1){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <strong>¡Acceso Restringido!</strong><br>
                No tienes permisos para realizar copias de seguridad.
                <br><br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-outlined is-rounded">
                    <i class="fas fa-arrow-left"></i> &nbsp; Regresar al Inicio
                </a>
            </div>';
            exit(); 
        }
        
    ?>

    
<div class="container pb-6 pt-6">
    <div class="columns is-centered">
        <div class="column is-half has-text-centered">
            <figure class="image is-128x128" style="margin: 0 auto;">
                <img src="<?php echo APP_URL; ?>app/views/img/backup_icon.png" alt="Backup"> </figure>
            <br>
            <p class="mb-4">Al presionar el botón se generará una copia de seguridad completa de la base de datos en la carpeta del servidor.</p>
            
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/backupAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_backup" value="backup">
                <button type="submit" class="button is-info is-large is-rounded">
                    <i class="fas fa-download fa-fw"></i> &nbsp; Generar Respaldo
                </button>
            </form>
        </div>
    </div>
</div>