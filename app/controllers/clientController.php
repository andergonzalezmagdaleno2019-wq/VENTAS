<?php
namespace app\controllers;
use app\models\mainModel;

class clientController extends mainModel
{
    /*----------  Controlador registrar cliente  ----------*/
    public function registrarClienteControlador()
    {
        $tipo_documento = $this->limpiarCadena($_POST['cliente_tipo_documento']);
        $numero_documento = $this->limpiarCadena($_POST['cliente_numero_documento']);
        $nombre = $this->limpiarCadena($_POST['cliente_nombre']);
        $apellido = $this->limpiarCadena($_POST['cliente_apellido']);
        $provincia = $this->limpiarCadena($_POST['cliente_provincia']);
        $ciudad = $this->limpiarCadena($_POST['cliente_ciudad']);
        $direccion = $this->limpiarCadena($_POST['cliente_direccion']);
        
        // CAPTURAMOS EL TELÉFONO DIVIDIDO
        $telefono_prefijo = $this->limpiarCadena($_POST['cliente_telefono_codigo']);
        $telefono_numero = $this->limpiarCadena($_POST['cliente_telefono']);
        $email = $this->limpiarCadena($_POST['cliente_email']);

        if ($tipo_documento == "" || $numero_documento == "" || $nombre == "" || $apellido == "" || $provincia == "" || $ciudad == "" || $direccion == "") {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Faltan campos obligatorios", "icono" => "error"]; return json_encode($alerta); exit();
        }

        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $nombre)){ $alerta=["tipo"=>"simple","titulo"=>"Nombre no válido","texto"=>"Solo se permiten letras.","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $apellido)){ $alerta=["tipo"=>"simple","titulo"=>"Apellido no válido","texto"=>"Solo se permiten letras.","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[0-9]{7,10}$/", $numero_documento)){ $alerta=["tipo"=>"simple","titulo"=>"Error en Documento","texto"=>"La cédula debe ser solo números (7 a 10 dígitos).","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}$/", $provincia)){ $alerta = ["tipo" => "simple", "titulo" => "Estado no válido", "texto" => "Sin números, de 4 a 30 letras.", "icono" => "error"]; return json_encode($alerta); exit(); }
		if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}$/", $ciudad)){ $alerta = ["tipo" => "simple", "titulo" => "Ciudad no válida", "texto" => "Sin números, de 4 a 30 letras.", "icono" => "error"]; return json_encode($alerta); exit(); }
		if(!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,70}$/", $direccion)){ $alerta = ["tipo" => "simple", "titulo" => "Dirección no válida", "texto" => "Caracteres no permitidos o longitud incorrecta.", "icono" => "error"]; return json_encode($alerta); exit(); }

        # VALIDACIÓN Y UNIFICACIÓN DE TELÉFONO #
        $telefono = "";
        if ($telefono_numero != "" || $telefono_prefijo != "") {
            if($telefono_prefijo == ""){ $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Debe seleccionar un código de área.", "icono" => "error"]; return json_encode($alerta); exit(); }
            if (!preg_match("/^[0-9]{7}$/", $telefono_numero)) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El número de teléfono debe tener exactamente 7 dígitos.", "icono" => "error"]; return json_encode($alerta); exit(); }
            $telefono = $telefono_prefijo . $telefono_numero;
        }

        if ($email != "") {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $alerta = ["tipo" => "simple", "titulo" => "Email inválido", "texto" => "Formato incorrecto", "icono" => "error"]; return json_encode($alerta); exit(); }
            $check_email = $this->ejecutarConsulta("SELECT cliente_email FROM cliente WHERE cliente_email='$email'");
            if ($check_email->rowCount() > 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El EMAIL ya está registrado", "icono" => "error"]; return json_encode($alerta); exit(); }
        }

        $check_documento = $this->ejecutarConsulta("SELECT cliente_id FROM cliente WHERE cliente_tipo_documento='$tipo_documento' AND cliente_numero_documento='$numero_documento'");
        if ($check_documento->rowCount() > 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El documento ya se encuentra registrado", "icono" => "error"]; return json_encode($alerta); exit(); }

		$cliente_datos_reg = [
			["campo_nombre" => "cliente_tipo_documento", "campo_marcador" => ":TipoDocumento", "campo_valor" => $tipo_documento],
			["campo_nombre" => "cliente_numero_documento", "campo_marcador" => ":NumeroDocumento", "campo_valor" => $numero_documento],
			["campo_nombre" => "cliente_nombre", "campo_marcador" => ":Nombre", "campo_valor" => $nombre],
			["campo_nombre" => "cliente_apellido", "campo_marcador" => ":Apellido", "campo_valor" => $apellido],
			["campo_nombre" => "cliente_provincia", "campo_marcador" => ":Provincia", "campo_valor" => $provincia],
			["campo_nombre" => "cliente_ciudad", "campo_marcador" => ":Ciudad", "campo_valor" => $ciudad],
			["campo_nombre" => "cliente_direccion", "campo_marcador" => ":Direccion", "campo_valor" => $direccion],
			["campo_nombre" => "cliente_telefono", "campo_marcador" => ":Telefono", "campo_valor" => $telefono],
			["campo_nombre" => "cliente_email", "campo_marcador" => ":Email", "campo_valor" => $email]
		];

		$registrar_cliente = $this->guardarDatos("cliente", $cliente_datos_reg);

		if ($registrar_cliente->rowCount() == 1) {
			$this->guardarBitacora("Clientes", "Registro", "Se registró el cliente: " . $nombre . " " . $apellido);
			$alerta = ["tipo" => "redireccionar", "titulo" => "Cliente registrado", "texto" => "Registrado con éxito", "icono" => "success", "url" => APP_URL . "clientList/"];
		} else {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar el cliente", "icono" => "error"];
		}
		return json_encode($alerta);
	}

	/*----------  Controlador listar cliente  ----------*/
	public function listarClienteControlador($pagina, $registros, $url, $busqueda)
	{
		$pagina = $this->limpiarCadena($pagina); $registros = $this->limpiarCadena($registros); $url = $this->limpiarCadena($url); $url = APP_URL . $url . "/"; $busqueda = $this->limpiarCadena($busqueda); $tabla = "";
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1; $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($busqueda) && $busqueda != "") {
			$consulta_datos = "SELECT * FROM cliente WHERE ((cliente_id!='1') AND (cliente_tipo_documento LIKE '%$busqueda%' OR cliente_numero_documento LIKE '%$busqueda%' OR cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_email LIKE '%$busqueda%' OR cliente_provincia LIKE '%$busqueda%' OR cliente_ciudad LIKE '%$busqueda%')) ORDER BY cliente_nombre ASC LIMIT $inicio,$registros";
			$consulta_total = "SELECT COUNT(cliente_id) FROM cliente WHERE ((cliente_id!='1') AND (cliente_tipo_documento LIKE '%$busqueda%' OR cliente_numero_documento LIKE '%$busqueda%' OR cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_email LIKE '%$busqueda%' OR cliente_provincia LIKE '%$busqueda%' OR cliente_ciudad LIKE '%$busqueda%'))";
		} else {
			$consulta_datos = "SELECT * FROM cliente WHERE cliente_id!='1' ORDER BY cliente_nombre ASC LIMIT $inicio,$registros";
			$consulta_total = "SELECT COUNT(cliente_id) FROM cliente WHERE cliente_id!='1'";
		}

		$datos = $this->ejecutarConsulta($consulta_datos); $datos = $datos->fetchAll(); $total = $this->ejecutarConsulta($consulta_total); $total = (int) $total->fetchColumn(); $numeroPaginas = ceil($total / $registros);

		$tabla .= '<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-light"><th class="has-text-centered">#</th><th class="has-text-centered">Documento</th><th class="has-text-centered">Nombre</th><th class="has-text-centered">Teléfono</th><th class="has-text-centered">Actualizar</th><th class="has-text-centered">Eliminar</th></tr></thead><tbody>';

		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1; $pag_inicio = $inicio + 1;
			foreach ($datos as $rows) {
                // Formateamos el teléfono para que se vea bien en la tabla
                $tel_tabla = ($rows['cliente_telefono'] != "") ? substr($rows['cliente_telefono'], 0, 4)."-".substr($rows['cliente_telefono'], 4) : "N/A";
				$tabla .= '<tr class="has-text-centered" ><td>' . $contador . '</td><td>' . $rows['cliente_tipo_documento'] . '-' . $rows['cliente_numero_documento'] . '</td><td>' . $rows['cliente_nombre'] . ' ' . $rows['cliente_apellido'] . '</td><td>' . $tel_tabla . '</td><td><a href="' . APP_URL . 'clientUpdate/' . $rows['cliente_id'] . '/" class="button is-success is-rounded is-small"><i class="fas fa-sync fa-fw"></i></a></td><td><form class="FormularioAjax" action="' . APP_URL . 'app/ajax/clienteAjax.php" method="POST" autocomplete="off" ><input type="hidden" name="modulo_cliente" value="eliminar"><input type="hidden" name="cliente_id" value="' . $rows['cliente_id'] . '"><button type="submit" class="button is-danger is-rounded is-small"><i class="far fa-trash-alt fa-fw"></i></button></form></td></tr>';
				$contador++;
			}
			$pag_final = $contador - 1;
		} else { $tabla .= '<tr class="has-text-centered" ><td colspan="6">No hay registros en el sistema</td></tr>'; }
		$tabla .= '</tbody></table></div>';
		if ($total > 0 && $pagina <= $numeroPaginas) { $tabla .= '<p class="has-text-right">Mostrando clientes <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>'; $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7); }
		return $tabla;
	}

	/*----------  Controlador eliminar cliente  ----------*/
	public function eliminarClienteControlador()
	{
		$id = $this->limpiarCadena($_POST['cliente_id']);
		if ($id == 1) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No podemos eliminar el cliente principal", "icono" => "error"]; return json_encode($alerta); exit(); }
		$datos = $this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
		if ($datos->rowCount() <= 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Cliente no encontrado", "icono" => "error"]; return json_encode($alerta); exit(); } else { $datos = $datos->fetch(); }
		$check_ventas = $this->ejecutarConsulta("SELECT cliente_id FROM venta WHERE cliente_id='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El cliente tiene ventas asociadas", "icono" => "error"]; return json_encode($alerta); exit(); }

		$eliminarCliente = $this->eliminarRegistro("cliente", "cliente_id", $id);
		if ($eliminarCliente->rowCount() == 1) {
			$this->guardarBitacora("Clientes", "Eliminación", "Se eliminó el cliente: " . $datos['cliente_nombre'] . " " . $datos['cliente_apellido']);
			$alerta = ["tipo" => "recargar", "titulo" => "Cliente eliminado", "texto" => "Eliminado con éxito", "icono" => "success"];
		} else { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo eliminar el cliente", "icono" => "error"]; }
		return json_encode($alerta);
	}

    /*----------  Controlador actualizar cliente  ----------*/
    public function actualizarClienteControlador()
    {
        $id = $this->limpiarCadena($_POST['cliente_id']);
        $datos = $this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
        if ($datos->rowCount() <= 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Cliente no encontrado", "icono" => "error"]; return json_encode($alerta); exit(); } else { $datos = $datos->fetch(); }

        $tipo_documento = $this->limpiarCadena($_POST['cliente_tipo_documento']);
        $numero_documento = $this->limpiarCadena($_POST['cliente_numero_documento']);
        $nombre = $this->limpiarCadena($_POST['cliente_nombre']);
        $apellido = $this->limpiarCadena($_POST['cliente_apellido']);
        $provincia = $this->limpiarCadena($_POST['cliente_provincia']);
        $ciudad = $this->limpiarCadena($_POST['cliente_ciudad']);
        $direccion = $this->limpiarCadena($_POST['cliente_direccion']);

        // CAPTURAMOS EL TELÉFONO DIVIDIDO
        $telefono_prefijo = $this->limpiarCadena($_POST['cliente_telefono_codigo']);
        $telefono_numero = $this->limpiarCadena($_POST['cliente_telefono']);
        $email = $this->limpiarCadena($_POST['cliente_email']);

        if ($tipo_documento == "" || $numero_documento == "" || $nombre == "" || $apellido == "" || $provincia == "" || $ciudad == "" || $direccion == "") { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Faltan campos obligatorios", "icono" => "error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $nombre)){ $alerta=["tipo"=>"simple","titulo"=>"Nombre no válido","texto"=>"Solo se permiten letras.","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $apellido)){ $alerta=["tipo"=>"simple","titulo"=>"Apellido no válido","texto"=>"Solo se permiten letras.","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[0-9]{7,10}$/", $numero_documento)){ $alerta=["tipo"=>"simple","titulo"=>"Error en Documento","texto"=>"La cédula debe ser solo números (7 a 10 dígitos).","icono"=>"error"]; return json_encode($alerta); exit(); }
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}$/", $provincia)){ $alerta = ["tipo" => "simple", "titulo" => "Estado no válido", "texto" => "Sin números, de 4 a 30 letras.", "icono" => "error"]; return json_encode($alerta); exit(); }
		if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}$/", $ciudad)){ $alerta = ["tipo" => "simple", "titulo" => "Ciudad no válida", "texto" => "Sin números, de 4 a 30 letras.", "icono" => "error"]; return json_encode($alerta); exit(); }
		if(!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,70}$/", $direccion)){ $alerta = ["tipo" => "simple", "titulo" => "Dirección no válida", "texto" => "Caracteres no permitidos o longitud incorrecta.", "icono" => "error"]; return json_encode($alerta); exit(); }

        # VALIDACIÓN Y UNIFICACIÓN DE TELÉFONO #
        $telefono = "";
        if ($telefono_numero != "" || $telefono_prefijo != "") {
            if($telefono_prefijo == ""){ $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Debe seleccionar un código de área.", "icono" => "error"]; return json_encode($alerta); exit(); }
            if (!preg_match("/^[0-9]{7}$/", $telefono_numero)) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El número de teléfono debe tener exactamente 7 dígitos.", "icono" => "error"]; return json_encode($alerta); exit(); }
            $telefono = $telefono_prefijo . $telefono_numero;
        }

        if ($email != "") {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $alerta = ["tipo" => "simple", "titulo" => "Error de Email", "texto" => "Formato incorrecto", "icono" => "error"]; return json_encode($alerta); exit(); }
            if ($email != $datos['cliente_email']) {
                $check_email = $this->ejecutarConsulta("SELECT cliente_email FROM cliente WHERE cliente_email='$email'");
                if ($check_email->rowCount() > 0) { $alerta = ["tipo" => "simple", "titulo" => "Email en uso", "texto" => "El correo ya pertenece a otro cliente", "icono" => "error"]; return json_encode($alerta); exit(); }
            }
        }

        if ($tipo_documento != $datos['cliente_tipo_documento'] || $numero_documento != $datos['cliente_numero_documento']) {
            $check_documento = $this->ejecutarConsulta("SELECT cliente_id FROM cliente WHERE cliente_tipo_documento='$tipo_documento' AND cliente_numero_documento='$numero_documento'");
            if ($check_documento->rowCount() > 0) { $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El documento ya se encuentra registrado", "icono" => "error"]; return json_encode($alerta); exit(); }
        }

        $cliente_datos_up = [
            ["campo_nombre" => "cliente_tipo_documento", "campo_marcador" => ":TipoDocumento", "campo_valor" => $tipo_documento],
            ["campo_nombre" => "cliente_numero_documento", "campo_marcador" => ":NumeroDocumento", "campo_valor" => $numero_documento],
            ["campo_nombre" => "cliente_nombre", "campo_marcador" => ":Nombre", "campo_valor" => $nombre],
            ["campo_nombre" => "cliente_apellido", "campo_marcador" => ":Apellido", "campo_valor" => $apellido],
            ["campo_nombre" => "cliente_provincia", "campo_marcador" => ":Provincia", "campo_valor" => $provincia],
            ["campo_nombre" => "cliente_ciudad", "campo_marcador" => ":Ciudad", "campo_valor" => $ciudad],
            ["campo_nombre" => "cliente_direccion", "campo_marcador" => ":Direccion", "campo_valor" => $direccion],
            ["campo_nombre" => "cliente_telefono", "campo_marcador" => ":Telefono", "campo_valor" => $telefono],
            ["campo_nombre" => "cliente_email", "campo_marcador" => ":Email", "campo_valor" => $email]
        ];

        $condicion = ["condicion_campo" => "cliente_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

        if ($this->actualizarDatos("cliente", $cliente_datos_up, $condicion)) {
            $this->guardarBitacora("Clientes", "Actualización", "Se actualizaron los datos de: " . $nombre . " " . $apellido);
            $alerta = ["tipo" => "redireccionar", "titulo" => "Cliente actualizado", "texto" => "Actualizado correctamente", "icono" => "success", "url" => APP_URL."clientList/"];
        } else {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo actualizar los datos", "icono" => "error"];
        }
        return json_encode($alerta);
    }
}