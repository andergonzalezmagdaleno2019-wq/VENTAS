<div class="container is-fluid mb-6">
	<h1 class="title">Empresa</h1>
	<h2 class="subtitle"><i class="fas fa-store-alt fa-fw"></i> &nbsp; Datos de empresa y Logo</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		$datos=$insLogin->seleccionarDatos("Normal","empresa LIMIT 1","*",0);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>
    
<div class="has-text-centered mb-6">
    <figure class="image is-128x128 is-inline-block" style="border: 2px solid #ccc; border-radius: 10px; padding: 5px;">
        <?php 
            // Rutas físicas para comprobación
            $path_logo = "./app/views/img/logo.png";
            $path_black = "./app/views/img/logo_black.png";
            
            if(is_file($path_logo)): ?>
                <img src="<?php echo APP_URL; ?>app/views/img/logo.png?v=<?php echo time(); ?>" class="logo-light">
                
                <img src="<?php echo APP_URL; ?>app/views/img/logo_black.png?v=<?php echo time(); ?>" class="logo-dark" style="display: none;">
                
                <?php if(!is_file($path_black)): ?>
                    <script>console.warn("Fasnet: Falta el archivo logo_black.png en app/views/img/");</script>
                <?php endif; ?>
                
        <?php else: ?>
            <img src="<?php echo APP_URL; ?>app/views/img/default.png">
        <?php endif; ?>
    </figure>
</div>
	<h2 class="title has-text-centered"><?php echo $datos['empresa_nombre']; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/empresaAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

		<input type="hidden" name="modulo_empresa" value="actualizar">
		<input type="hidden" name="empresa_id" value="<?php echo $datos['empresa_id']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="empresa_nombre" value="<?php echo $datos['empresa_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}" maxlength="85" required >
				</div>
		  	</div>
            <div class="column">
		    	<div class="control">
					<label class="label">RIF</label>
				  	<input class="input" type="text" name="empresa_rif" value="<?php echo isset($datos['empresa_rif']) ? $datos['empresa_rif'] : ''; ?>" pattern="[a-zA-Z0-9\- ]{5,40}" maxlength="40" placeholder="Ej: J-12345678-9">
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Teléfono</label>
				  	<input class="input" type="text" name="empresa_telefono" value="<?php echo $datos['empresa_telefono']; ?>" pattern="[0-9()+]{8,20}" maxlength="20" >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label class="label">Email</label>
				  	<input class="input" type="email" name="empresa_email" value="<?php echo $datos['empresa_email']; ?>" maxlength="50" >
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Dirección</label>
				  	<input class="input" type="text" name="empresa_direccion" value="<?php echo $datos['empresa_direccion']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}" maxlength="97" >
				</div>
		  	</div>
		</div>
        
        <div class="columns">
			<div class="column">
				<label class="label">Actualizar Logo de la Empresa (Para Facturas y Reportes)</label>
				<div class="file is-small has-name is-info">
				  	<label class="file-label">
				    	<input class="file-input" type="file" name="empresa_foto" accept=".jpg, .png, .jpeg" >
				    	<span class="file-cta">
				      		<span class="file-icon"><i class="fas fa-upload"></i></span>
				      		<span class="file-label">Seleccione una imagen</span>
				    	</span>
				    	<span class="file-name">JPG, JPEG, PNG. (Recomendado: Fondo transparente)</span>
				  	</label>
				</div>
			</div>
		</div>

		<p class="has-text-centered mt-4">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar Empresa</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>

	<?php }else{ ?>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/empresaAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

		<input type="hidden" name="modulo_empresa" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="empresa_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}" maxlength="85" required >
				</div>
		  	</div>
            <div class="column">
		    	<div class="control">
					<label class="label">RIF</label>
				  	<input class="input" type="text" name="empresa_rif" pattern="[a-zA-Z0-9\- ]{5,40}" maxlength="40" placeholder="Ej: J-12345678-9">
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Teléfono</label>
				  	<input class="input" type="text" name="empresa_telefono" pattern="[0-9()+]{8,20}" maxlength="20" >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label class="label">Email</label>
				  	<input class="input" type="email" name="empresa_email" maxlength="50" >
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label class="label">Dirección</label>
				  	<input class="input" type="text" name="empresa_direccion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}" maxlength="97" >
				</div>
		  	</div>
		</div>

        <div class="columns">
			<div class="column">
				<label class="label">Subir Logo de la Empresa</label>
				<div class="file is-small has-name is-info">
				  	<label class="file-label">
				    	<input class="file-input" type="file" name="empresa_foto" accept=".jpg, .png, .jpeg" >
				    	<span class="file-cta">
				      		<span class="file-icon"><i class="fas fa-upload"></i></span>
				      		<span class="file-label">Seleccione una imagen</span>
				    	</span>
				    	<span class="file-name">JPG, JPEG, PNG.</span>
				  	</label>
				</div>
			</div>
		</div>

		<p class="has-text-centered mt-4">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar Empresa</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>

	<?php } ?>
</div>