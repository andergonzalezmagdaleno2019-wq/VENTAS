<div class="container is-fluid mb-6">
	<h1 class="title">Auditoría</h1>
	<h2 class="subtitle"><i class="fas fa-history fa-fw"></i> &nbsp; Bitácora de movimientos del sistema</h2>
</div>

<div class="container is-fluid pb-6">
	<?php
		use app\controllers\auditController;
		$insAudit = new auditController();

		if(!isset($_SESSION['busqueda_auditList'])){
			$_SESSION['busqueda_auditList'] = "";
		}

		$busqueda = $_SESSION['busqueda_auditList'];
	?>

	<div class="columns">
		<div class="column">
			<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
				<input type="hidden" name="modulo_buscador" value="buscar">
				<input type="hidden" name="modulo_url" value="auditList">
				<div class="field is-grouped">
					<p class="control is-expanded">
						<input class="input is-rounded" type="text" name="txt_buscador" value="<?php echo $busqueda; ?>" placeholder="Buscar por módulo, acción o usuario..." maxlength="30" >
					</p>
					<p class="control">
						<button class="button is-info is-rounded" type="submit" >Buscar</button>
					</p>
				</div>
			</form>
		</div>
	</div>

	<div class="columns">
		<div class="column">
			<?php
				$pagina = (isset($url[1]) && $url[1]!="") ? $url[1] : 1;
				echo $insAudit->listarBitacoraControlador($pagina, 15, $url[0], $busqueda);
			?>
		</div>
	</div>
</div>