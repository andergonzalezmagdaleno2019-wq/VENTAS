<div class="container is-fluid mb-6">
	<h1 class="title">Proveedores</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar proveedor</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./app/views/inc/btn_back.php";
        
        $id = $insLogin->limpiarCadena($url[1]);
		$datos=$insLogin->seleccionarDatos("Unico","proveedor","proveedor_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered"><?php echo $datos['proveedor_nombre']; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_proveedor" value="actualizar">
		<input type="hidden" name="proveedor_id" value="<?php echo $datos['proveedor_id']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre del Proveedor <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="proveedor_nombre" value="<?php echo $datos['proveedor_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ.,\- ]{3,70}" maxlength="70" required autocomplete="off">
				</div>
		  	</div>
            <div class="column">
					<div class="control">
						<label>RIF / Identificación <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" 
       					name="proveedor_rif" 
       					value="<?php echo $datos['proveedor_rif']; ?>" 
       					pattern="[0-9\-]{1,15}"
       					maxlength="15" 
       					required autocomplete="off">
					</div>
		  	</div>
		</div>

		<?php
            /* Lógica para picar el teléfono guardado del proveedor */
            $tel_db = isset($datos['proveedor_telefono']) ? $datos['proveedor_telefono'] : "";
            $prefijo_prov = ($tel_db != "") ? substr($tel_db, 0, 4) : "";
            $numero_prov = ($tel_db != "") ? substr($tel_db, 4) : "";
        ?>
		<div class="columns">
            <div class="column is-4">
		    	<div class="control">
					<label>Teléfono <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="field has-addons">
                        <p class="control">
                            <span class="select">
                                <select name="proveedor_telefono_codigo" required>
                                    <option value="">Cód.</option>
                                    <?php echo $insLogin->generarSelect(PREFIJOS_TELEFONICOS, $prefijo_prov); ?>
                                </select>
                            </span>
                        </p>
                        <p class="control is-expanded">
                            <input class="input" type="text" name="proveedor_telefono" value="<?php echo $numero_prov; ?>" pattern="[0-9]{7}" maxlength="7" placeholder="1234567" required autocomplete="off">
                        </p>
                    </div>
				</div>
		  	</div>
		  	<div class="column is-8">
		    	<div class="control">
					<label>Dirección</label>
				  	<input class="input" type="text" name="proveedor_direccion" value="<?php echo isset($datos['proveedor_direccion']) ? $datos['proveedor_direccion'] : ''; ?>" maxlength="200" autocomplete="off">
				</div>
		  	</div>
		</div>

		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
		</p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>