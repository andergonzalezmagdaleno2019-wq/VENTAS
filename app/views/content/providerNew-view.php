<div class="container is-fluid mb-6">
	<h1 class="title">Proveedores</h1>
	<h2 class="subtitle">Nuevo proveedor</h2>
</div>

<div class="container is-fluid pb-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_proveedor" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre del Proveedor <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="proveedor_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}" maxlength="70" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>RIF / Identificación <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="proveedor_rif" pattern="[0-9\-]{1,15}" maxlength="15" placeholder="Ej: 12345678-9" required >
				</div>
		  	</div>

		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Teléfono de contacto del proveedor <?php echo CAMPO_OBLIGATORIO; ?></label>
					<div class="field has-addons">
						<p class="control">
							<span class="select" id="cont-cod-prov">
								<select name="proveedor_telefono_codigo" id="prov_cod_tel" required>
									<option value="" selected>Cód.</option>
									<?php
										echo $insLogin->generarSelect(PREFIJOS_TELEFONICOS, "VACIO");
									?>
								</select>
							</span>
						</p>
						<p class="control is-expanded">
							<input class="input" type="text" name="proveedor_telefono"
								pattern="[0-9]{7}" maxlength="7"
								placeholder="1234567" required>
						</p>
					</div>
				</div>
			</div>
		  	<div class="column">
		    	<div class="control">
					<label>Dirección</label>
				  	<input class="input" type="text" name="proveedor_direccion" maxlength="200" >
				</div>
		  	</div>
		</div>

		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
			<small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
		</p>
	</form>
</div>