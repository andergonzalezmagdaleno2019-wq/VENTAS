<div class="container is-fluid mt-4">
    <?php
        $datos_empresa=$insLogin->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
        $nombre_empresa = "Sistema de Ventas";
        if($datos_empresa->rowCount()==1){
            $datos_empresa=$datos_empresa->fetch();
            $nombre_empresa = $datos_empresa['empresa_nombre'];
        }
    ?>
    <div class="has-text-centered mb-6">
        <figure class="image is-inline-block" style="max-width: 300px; border: 2px solid #ccc; border-radius: 10px; padding: 15px;">
            <?php 
                $path_logo = "./app/views/img/logo.png";
                $path_black = "./app/views/img/logo_black.png";
                
                if(is_file($path_logo)): ?>
                    <img src="<?php echo APP_URL; ?>app/views/img/logo.png?v=<?php echo time(); ?>" class="logo-light" style="width: 100%; height: auto;">
                    
                    <img src="<?php echo APP_URL; ?>app/views/img/logo_black.png?v=<?php echo time(); ?>" class="logo-dark" style="width: 100%; height: auto;">
            <?php else: ?>
                    <img src="<?php echo APP_URL; ?>app/views/img/default.png" style="width: 100px;">
            <?php endif; ?>
        </figure>
    </div>
    <div class="columns is-flex is-justify-content-center mt-2 mb-2">
  		<h1 class="title is-2 has-text-weight-bold has-text-link"><?php echo $nombre_empresa; ?></h1>
  	</div>
  	<div class="columns is-flex is-justify-content-center">
  		<h2 class="subtitle">¡Bienvenido <?php 
            $nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
            $apellido = isset($_SESSION['apellido']) ? $_SESSION['apellido'] : '';
            echo $nombre . " " . $apellido; 
        ?>!</h2>
  	</div>
</div>
<?php
	// 1. Consultas Generales (Contadores)
	$total_usuarios=$insLogin->seleccionarDatos("Normal","usuario WHERE usuario_id!='1' AND usuario_id!='".$_SESSION['id']."'","usuario_id",0);
	$total_clientes=$insLogin->seleccionarDatos("Normal","cliente WHERE cliente_id!='1'","cliente_id",0);
	$total_categorias=$insLogin->seleccionarDatos("Normal","categoria","categoria_id",0);
	$total_productos=$insLogin->seleccionarDatos("Normal","producto","producto_id",0);
	$total_ventas=$insLogin->seleccionarDatos("Normal","venta","venta_id",0);

    // 2. Consultas de ALERTAS INTELIGENTES (Stock de Activos)
    $alertas_bajas = $insLogin->seleccionarDatos("Normal", "producto WHERE producto_stock <= producto_stock_min AND producto_estado='Activo' ORDER BY producto_stock ASC", "*", 0);
    $alertas_altas = $insLogin->seleccionarDatos("Normal", "producto WHERE producto_stock >= producto_stock_max AND producto_estado='Activo' ORDER BY producto_stock DESC", "*", 0);

    // 3. Consulta para el Balance Diario
    $ventas_hoy_q = $insLogin->seleccionarDatos("Normal", "venta WHERE venta_fecha = '".date("Y-m-d")."'", "SUM(venta_total) as total_usd, SUM(venta_total * venta_tasa_bcv) as total_bs", 0);
    $ventas_hoy = $ventas_hoy_q->fetch();
    $total_usd_hoy = ($ventas_hoy['total_usd'] > 0) ? $ventas_hoy['total_usd'] : 0;
    $total_bs_hoy = ($ventas_hoy['total_bs'] > 0) ? $ventas_hoy['total_bs'] : 0;
?>

<div class="container pb-6 pt-6">

    <div class="columns is-centered mb-6">
        
        <div class="column is-4"> 
            <div class="box has-text-centered p-5" style="border-top: 5px solid #004595; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: 100%;">
                <h3 class="title is-5 has-text-grey-dark is-flex is-align-items-center is-justify-content-center mb-4">
                    <i class="fas fa-landmark" style="color: #004595; margin-right: 10px; font-size: 1.3rem;"></i>
                    Tasa Oficial BCV
                </h3>
                <p id="valor-bcv-dashboard" class="title is-2 has-text-info mb-4">Cargando...</p>
                <p id="fecha-bcv-dashboard" class="subtitle is-6 has-text-grey mt-2"></p>
            </div>
        </div>

        <div class="column is-4"> 
            <div class="box has-text-centered p-5" style="border-top: 5px solid #28a745; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: 100%;">
                <h3 class="title is-5 has-text-grey-dark is-flex is-align-items-center is-justify-content-center mb-4">
                    <i class="fas fa-cash-register" style="color: #28a745; margin-right: 10px; font-size: 1.3rem;"></i>
                    Balance Diario de Ventas
                </h3>
                
                <div class="mt-4 mb-5">
                    <p class="is-size-2 has-text-success has-text-weight-bold" style="margin-bottom: 5px; line-height: 1;">
                        $<?php echo number_format($total_usd_hoy, 2); ?>
                    </p>
                    <p class="is-size-5 has-text-link has-text-weight-bold" style="line-height: 1;">
                        Bs. <?php echo number_format($total_bs_hoy, 2, ',', '.'); ?>
                    </p>
                </div>

                <a href="<?php echo APP_URL; ?>app/pdf/report_daily_closing.php" target="_blank" class="button is-success is-rounded is-outlined mt-2 is-small">
                    <i class="fas fa-file-pdf"></i> &nbsp; Imprimir Cierre Diario
                </a>
            </div>
        </div>

    </div>
    
    <div class="columns is-multiline mb-6">
        <?php if($alertas_bajas && $alertas_bajas->rowCount()>0){ ?>
        <div class="column is-12">
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <h4 class="title is-4"><i class="fas fa-exclamation-triangle"></i> &nbsp; ¡Atención! Stock Crítico</h4>
                <p>Tienes <strong><?php echo $alertas_bajas->rowCount(); ?></strong> productos que se están agotando:</p>
                <ul style="margin-top: 10px; margin-bottom: 10px; list-style: circle; margin-left: 20px;">
                    <?php
                        $cc=0;
                        while($prod = $alertas_bajas->fetch()){
                            echo "<li><b>".$prod['producto_nombre']."</b> (Tienes: ".$prod['producto_stock']." | Mínimo: ".$prod['producto_stock_min'].")</li>";
                            $cc++;
                            if($cc>=5){ echo "<li>... y otros más.</li>"; break; }
                        }
                    ?>
                </ul>
                <a href="<?php echo APP_URL; ?>purchaseNew/" class="button is-danger is-small is-rounded">Ir a Comprar (Reponer)</a>
            </div>
        </div>
        <?php } ?>

        <?php if($alertas_altas && $alertas_altas->rowCount()>0){ ?>
        <div class="column is-12">
            <div class="notification is-warning is-light">
                <button class="delete"></button>
                <h4 class="title is-4"><i class="fas fa-boxes"></i> &nbsp; Aviso de Exceso de Inventario</h4>
                <p>Hay <strong><?php echo $alertas_altas->rowCount(); ?></strong> productos que superan el stock máximo recomendado.</p>
                <a href="<?php echo APP_URL; ?>productList/" class="button is-warning is-small is-rounded mt-2">Ver Inventario</a>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="columns pb-6">
		<div class="column">
			<nav class="level is-mobile">
			  	<div class="level-item has-text-centered">
			    	<a href="<?php echo APP_URL; ?>userList/">
			      		<p class="heading"><i class="fas fa-users fa-fw"></i> &nbsp; Usuarios</p>
			      		<p class="title"><?php echo $total_usuarios->rowCount(); ?></p>
			    	</a>
			  	</div>
			  	<div class="level-item has-text-centered">
				    <a href="<?php echo APP_URL; ?>clientList/">
				      	<p class="heading"><i class="fas fa-address-book fa-fw"></i> &nbsp; Clientes</p>
				      	<p class="title"><?php echo $total_clientes->rowCount(); ?></p>
				    </a>
			  	</div>
                <div class="level-item has-text-centered">
				    <a href="<?php echo APP_URL; ?>categoryList/">
				      <p class="heading"><i class="fas fa-tags fa-fw"></i> &nbsp; Categorías</p>
				      <p class="title"><?php echo $total_categorias->rowCount(); ?></p>
				    </a>
			  	</div>
			</nav>
		</div>
	</div>

	<div class="columns pt-6 pb-6">
		<div class="column">
			<nav class="level is-mobile">
			  	<div class="level-item has-text-centered">
				    <a href="<?php echo APP_URL; ?>productList/">
				      	<p class="heading"><i class="fas fa-cubes fa-fw"></i> &nbsp; Inventario</p>
				      	<p class="title"><?php echo $total_productos->rowCount(); ?></p>
				    </a>
			  	</div>
			  	<div class="level-item has-text-centered">
			    	<a href="<?php echo APP_URL; ?>saleList/">
			      		<p class="heading"><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ventas</p>
			      		<p class="title"><?php echo $total_ventas->rowCount(); ?></p>
			    	</a>
			  	</div>
                <div class="level-item has-text-centered">
				    <a href="<?php echo APP_URL; ?>providerNew/">
				      <p class="heading"><i class="fas fa-truck fa-fw"></i> &nbsp; Proveedores</p>
				      <p class="title">+</p>
				    </a>
			  	</div>
			</nav>
		</div>
	</div>

    <div class="columns pt-6">
		<div class="column">
			<nav class="level is-mobile">
			  	<div class="level-item has-text-centered">
				    <a href="<?php echo APP_URL; ?>purchaseList/">
				      	<p class="heading"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Compras (Historial)</p>
				      	<p class="title"><i class="fas fa-history"></i></p>
				    </a>
			  	</div>
			</nav>
		</div>
	</div>

</div>