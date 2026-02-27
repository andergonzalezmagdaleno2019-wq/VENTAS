<?php
	namespace app\controllers;
	use app\models\mainModel;

	class auditController extends mainModel{

		/*----------  Controlador listar bitácora  ----------*/
		public function listarBitacoraControlador($pagina,$registros,$url,$busqueda){
			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);
			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";
			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){
				$consulta_datos="SELECT * FROM bitacora INNER JOIN usuario ON bitacora.usuario_id=usuario.usuario_id WHERE bitacora.bitacora_modulo LIKE '%$busqueda%' OR bitacora.bitacora_accion LIKE '%$busqueda%' OR usuario.usuario_usuario LIKE '%$busqueda%' ORDER BY bitacora.bitacora_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(bitacora_id) FROM bitacora INNER JOIN usuario ON bitacora.usuario_id=usuario.usuario_id WHERE bitacora.bitacora_modulo LIKE '%$busqueda%' OR bitacora.bitacora_accion LIKE '%$busqueda%' OR usuario.usuario_usuario LIKE '%$busqueda%'";
			}else{
				$consulta_datos="SELECT * FROM bitacora INNER JOIN usuario ON bitacora.usuario_id=usuario.usuario_id ORDER BY bitacora.bitacora_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(bitacora_id) FROM bitacora";
			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();
			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();
			$numeroPaginas =ceil($total/$registros);

			$tabla.='<div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">Fecha y Hora</th>
		                    <th class="has-text-centered">Usuario</th>
		                    <th class="has-text-centered">Módulo</th>
		                    <th class="has-text-centered">Acción</th>
		                    <th class="has-text-centered">Descripción</th>
		                </tr>
		            </thead>
		            <tbody>';

			if($total>=1 && $pagina<=$numeroPaginas){
				foreach($datos as $rows){
					$tabla.='
						<tr class="has-text-centered" >
							<td>'.date("d/m/Y", strtotime($rows['bitacora_fecha'])).' - '.$rows['bitacora_hora'].'</td>
							<td>'.$rows['usuario_usuario'].'</td>
							<td><span class="tag is-info is-light">'.$rows['bitacora_modulo'].'</span></td>
							<td><strong>'.$rows['bitacora_accion'].'</strong></td>
							<td class="has-text-left">'.$rows['bitacora_descripcion'].'</td>
						</tr>';
				}
			}else{
				$tabla.='<tr class="has-text-centered" ><td colspan="5">No hay registros de auditoría</td></tr>';
			}

			$tabla.='</tbody></table></div>';

			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando registros <strong>'.($inicio+1).'</strong> al <strong>'.($inicio+count($datos)).'</strong> de un <strong>total de '.$total.'</strong></p>';
				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}
			return $tabla;
		}
	}