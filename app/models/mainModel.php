<?php
	
	namespace app\models;
	use \PDO;

	if(file_exists(__DIR__."/../../config/server.php")){
		require_once __DIR__."/../../config/server.php";
	}

	class mainModel{

		private $server=DB_SERVER;
		private $db=DB_NAME;
		private $user=DB_USER;
		private $pass=DB_PASS;

		protected function conectar(){
			$conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db,$this->user,$this->pass);
			$conexion->exec("SET CHARACTER SET utf8");
			return $conexion;
		}

		protected function ejecutarConsulta($consulta){
			$sql=$this->conectar()->prepare($consulta);
			$sql->execute();
			return $sql;
		}

		public function limpiarCadena($cadena){
			$palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," DELETE ","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==",";","::"];
			$cadena=trim($cadena);
			$cadena=stripslashes($cadena);
			foreach($palabras as $palabra){
				$cadena=str_ireplace($palabra, "", $cadena);
			}
			$cadena=trim($cadena);
			$cadena=stripslashes($cadena);
			return $cadena;
		}

		protected function verificarDatos($filtro,$cadena){
			return !preg_match("/^".$filtro."$/", $cadena);
		}

		protected function guardarDatos($tabla,$datos){
			$query="INSERT INTO $tabla (";
			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_nombre"];
				$C++;
			}
			$query.=") VALUES(";
			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_marcador"];
				$C++;
			}
			$query.=")";
			$sql=$this->conectar()->prepare($query);
			foreach ($datos as $clave){
				$sql->bindValue($clave["campo_marcador"],$clave["campo_valor"]);
			}
			$sql->execute();
			return $sql;
		}

		public function seleccionarDatos($tipo,$tabla,$campo,$id){
            if($tipo=="Unico"){
                $sql=$this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo=:ID");
                $sql->bindValue(":ID",$id);
            }elseif($tipo=="Normal"){
                $sql=$this->conectar()->prepare("SELECT $campo FROM $tabla");
            }
            $sql->execute();
            return $sql;
		}

		protected function actualizarDatos($tabla,$datos,$condicion){
			$query="UPDATE $tabla SET ";
			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
				$C++;
			}
			$query.=" WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"];
			$sql=$this->conectar()->prepare($query);
			foreach ($datos as $clave){
				$sql->bindValue($clave["campo_marcador"],$clave["campo_valor"]);
			}
			$sql->bindValue($condicion["condicion_marcador"],$condicion["condicion_valor"]);
			$sql->execute();
			return $sql;
		}

		protected function eliminarRegistro($tabla,$campo,$id){
            $sql=$this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
            $sql->bindValue(":id",$id);
            $sql->execute();
            return $sql;
        }

		protected function paginadorTablas($pagina,$numeroPaginas,$url,$botones){
	        $tabla='<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';
	        if($pagina<=1){
	            $tabla.='<a class="pagination-previous is-disabled" disabled >Anterior</a><ul class="pagination-list">';
	        }else{
	            $tabla.='<a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a><ul class="pagination-list"><li><a class="pagination-link" href="'.$url.'1/">1</a></li><li><span class="pagination-ellipsis">&hellip;</span></li>';
	        }
	        $ci=0;
	        for($i=$pagina; $i<=$numeroPaginas; $i++){
	            if($ci>=$botones){ break; }
	            if($pagina==$i){
	                $tabla.='<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a></li>';
	            }else{
	                $tabla.='<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>';
	            }
	            $ci++;
	        }
	        if($pagina==$numeroPaginas){
	            $tabla.='</ul><a class="pagination-next is-disabled" disabled >Siguiente</a>';
	        }else{
	            $tabla.='<li><span class="pagination-ellipsis">&hellip;</span></li><li><a class="pagination-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li></ul><a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>';
	        }
	        $tabla.='</nav>';
	        return $tabla;
	    }

        /*---------- Función generar select (¡LA QUE FALTABA!) ----------*/
        public function generarSelect($datos,$campo_db){
            $check_select='';
            $text_select='';
            $count_select=1;
            $select='';
            foreach($datos as $row){
                if($campo_db==$row){
                    $check_select='selected=""';
                    $text_select=' (Actual)';
                }
                $select.='<option value="'.$row.'" '.$check_select.'>'.$count_select.' - '.$row.$text_select.'</option>';
                $check_select='';
                $text_select='';
                $count_select++;
            }
            return $select;
        }

		protected function generarCodigoAleatorio($longitud,$correlativo){
			$codigo="";
			$caracter="Letra";
			for($i=1; $i<=$longitud; $i++){
				if($caracter=="Letra"){
					$letra_aleatoria=chr(rand(ord("a"),ord("z")));
					$codigo.=strtoupper($letra_aleatoria);
					$caracter="Numero";
				}else{
					$codigo.=rand(0,9);
					$caracter="Letra";
				}
			}
			return $codigo."-".$correlativo;
		}

		public function limitarCadena($cadena,$limite,$sufijo){
			if(strlen($cadena)>$limite){ return substr($cadena,0,$limite).$sufijo; }else{ return $cadena; }
		}

        /*---------- Función para Auditoría ----------*/
        protected function guardarBitacora($modulo, $accion, $descripcion){
            $fecha = date("Y-m-d");
            $hora = date("h:i:s a");
            $usuario_id = (isset($_SESSION['id'])) ? $_SESSION['id'] : 0;
            if($usuario_id==0){ return false; }
            $datos_bitacora = [
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$usuario_id],
                ["campo_nombre"=>"bitacora_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                ["campo_nombre"=>"bitacora_hora","campo_marcador"=>":Hora","campo_valor"=>$hora],
                ["campo_nombre"=>"bitacora_modulo","campo_marcador"=>":Modulo","campo_valor"=>$modulo],
                ["campo_nombre"=>"bitacora_accion","campo_marcador"=>":Accion","campo_valor"=>$accion],
                ["campo_nombre"=>"bitacora_descripcion","campo_marcador"=>":Descripcion","campo_valor"=>$descripcion]
            ];
            return $this->guardarDatos("bitacora", $datos_bitacora);
        }
	}