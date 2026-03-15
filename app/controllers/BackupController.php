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
			$ruta = "../backups/";

            if(!file_exists($ruta)){
                mkdir($ruta, 0777, true);
            }

            if(file_put_contents($ruta.$nombre_archivo, $salida_sql)){
                $this->guardarBitacora("Sistema", "Backup", "Se generó una copia de seguridad: ".$nombre_archivo);
                
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
					"texto"=>"No se pudo crear el archivo de respaldo. Verifique permisos de escritura.",
					"icono"=>"error"
				];
            }

			return json_encode($alerta);
		}

        /* Función interna para generar el texto SQL (Versión Profesional Blindada) */
        protected function generarBackupSql(){
            $server=DB_SERVER;
            $user=DB_USER;
            $pass=DB_PASS;
            $dbname=DB_NAME;

            // Conexión directa con MySQLi para el volcado
            $mysqli = new \mysqli($server, $user, $pass, $dbname);
            if ($mysqli->connect_error) {
                die("Error de conexión: " . $mysqli->connect_error);
            }
            $mysqli->select_db($dbname);
            $mysqli->query("SET NAMES 'utf8'");

            $queryTables = $mysqli->query('SHOW TABLES');
            $target_tables = [];
            while($row = $queryTables->fetch_row()){
                $target_tables[] = $row[0];
            }

            /* 1. CONFIGURACIONES DE SEGURIDAD INICIALES */
            $content = "-- --------------------------------------------------------\n";
            $content .= "-- Respaldo del Sistema de Ventas\n";
            $content .= "-- Fecha de generación: " . date("Y-m-d H:i:s") . "\n";
            $content .= "-- --------------------------------------------------------\n\n";
            
            $content .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $content .= "SET time_zone = \"+00:00\";\n";
            $content .= "SET FOREIGN_KEY_CHECKS = 0;\n\n"; // <--- APAGAMOS LLAVES FORÁNEAS

            foreach($target_tables as $table){
                $result = $mysqli->query('SELECT * FROM `'.$table.'`');
                $fields_amount = $result->field_count;
                $rows_num = $mysqli->affected_rows;
                
                $content .= "-- --------------------------------------------------------\n";
                $content .= "-- Estructura de tabla para la tabla `".$table."`\n";
                $content .= "-- --------------------------------------------------------\n\n";

                /* 2. DESTRUCTOR DE TABLAS VIEJAS */
                $content .= "DROP TABLE IF EXISTS `".$table."`;\n";
                
                $res = $mysqli->query('SHOW CREATE TABLE `'.$table.'`');
                $TableMLine = $res->fetch_row();
                $content .= $TableMLine[1].";\n\n";

                /* 3. VOLCADO DE DATOS (INSERT) */
                if ($rows_num > 0) {
                    $content .= "-- Volcado de datos para la tabla `".$table."`\n";
                    $content .= "INSERT INTO `".$table."` VALUES";
                    $st_counter = 0;
                    
                    while($row = $result->fetch_row()){
                        $content .= "\n(";
                        for($j=0; $j<$fields_amount; $j++){
                            if (isset($row[$j])){
                                // Escapar caracteres especiales y saltos de línea
                                $row[$j] = addslashes($row[$j]);
                                $row[$j] = str_replace("\n","\\n", $row[$j]);
                                $row[$j] = str_replace("\r","\\r", $row[$j]);
                                $content .= '"'.$row[$j].'"';
                            }else{
                                /* 4. RESPETO A LOS VALORES NULOS */
                                $content .= 'NULL';
                            }
                            
                            if ($j<($fields_amount-1)){
                                $content.= ', ';
                            }
                        }
                        $content .=")";
                        
                        // Si llegamos a 100 registros o es el último, cerramos con punto y coma
                        if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {
                            $content .= ";\n";
                            // Si aún quedan más filas por procesar, abrimos un nuevo INSERT
                            if ($st_counter+1 != $rows_num) {
                                $content .= "INSERT INTO `".$table."` VALUES";
                            }
                        } else {
                            $content .= ",";
                        }
                        $st_counter++;
                    }
                }
                $content .="\n\n";
            }
            
            /* 5. REACTIVAR SEGURIDAD AL FINALIZAR */
            $content .= "-- Reactivar restricciones de llaves foráneas\n";
            $content .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            
            return $content;
        }
	}