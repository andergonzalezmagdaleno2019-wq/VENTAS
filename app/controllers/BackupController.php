<?php

	namespace app\controllers;
	use app\models\mainModel;

	class backupController extends mainModel{

		public function generarRespaldoControlador(){

			$fecha = date("Y-m-d_H-i-s");
			$salida_sql = $this->generarBackupSql();
            
            // Nombre del archivo
			$nombre_archivo = "backup_ventas_".$fecha.".sql";
            
            // Ruta donde se guardará (asegúrate de crear la carpeta 'backups' en la raiz o app)
            // Vamos a guardarlo en una carpeta 'backups' en la raiz del proyecto
			$ruta = "../backups/";

            if(!file_exists($ruta)){
                mkdir($ruta, 0777, true);
            }

            if(file_put_contents($ruta.$nombre_archivo, $salida_sql)){
                $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Respaldo Exitoso",
					"texto"=>"La copia de seguridad se ha creado correctamente: ".$nombre_archivo,
					"icono"=>"success"
				];
            }else{
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error",
					"texto"=>"No se pudo crear el archivo de respaldo. Verifique permisos.",
					"icono"=>"error"
				];
            }

			return json_encode($alerta);
		}

        /* Función interna para generar el texto SQL */
        protected function generarBackupSql(){
            $server=DB_SERVER;
            $user=DB_USER;
            $pass=DB_PASS;
            $dbname=DB_NAME;

            $mysqli = new \mysqli($server, $user, $pass, $dbname);
            $mysqli->select_db($dbname);
            $mysqli->query("SET NAMES 'utf8'");

            $queryTables = $mysqli->query('SHOW TABLES');
            while($row = $queryTables->fetch_row()){
                $target_tables[] = $row[0];
            }

            $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n";

            foreach($target_tables as $table){
                $result = $mysqli->query('SELECT * FROM '.$table);
                $fields_amount = $result->field_count;
                $rows_num=$mysqli->affected_rows;
                $res = $mysqli->query('SHOW CREATE TABLE '.$table);
                $TableMLine = $res->fetch_row();
                $content .= "\n\n".$TableMLine[1].";\n\n";

                for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter=0) {
                    while($row = $result->fetch_row()){
                        if ($st_counter%100 == 0 || $st_counter == 0 ){
                            $content .= "\nINSERT INTO ".$table." VALUES";
                        }
                        $content .= "\n(";
                        for($j=0; $j<$fields_amount; $j++){
                            $row[$j] = str_replace("\n","\\n", addslashes($row[$j]));
                            if (isset($row[$j])){
                                $content .= '"'.$row[$j].'"' ;
                            }else{
                                $content .= '""';
                            }
                            if ($j<($fields_amount-1)){
                                $content.= ',';
                            }
                        }
                        $content .=")";
                        if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {
                            $content .= ";";
                        } else {
                            $content .= ",";
                        }
                        $st_counter=$st_counter+1;
                    }
                } $content .="\n\n\n";
            }
            return $content;
        }
	}