<?php

    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel{

        /*----------  Controlador registrar usuario  ----------*/
        public function registrarUsuarioControlador(){

            // Capturamos todos los datos enviados desde el formulario
            $tipo_doc=$this->limpiarCadena($_POST['usuario_tipo_documento']);
            $dni=$this->limpiarCadena($_POST['usuario_dni']);
            $nombre=$this->limpiarCadena($_POST['usuario_nombre']);
            $apellido=$this->limpiarCadena($_POST['usuario_apellido']);
            $usuario=$this->limpiarCadena($_POST['usuario_usuario']);
            $email=$this->limpiarCadena($_POST['usuario_email']);
            $clave1=$this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2=$this->limpiarCadena($_POST['usuario_clave_2']);
            $caja=$this->limpiarCadena($_POST['usuario_caja']);
            $rol = isset($_POST['usuario_rol']) ? $this->limpiarCadena($_POST['usuario_rol']) : 2;

            /*==============================================================
            =            ORDEN LÓGICO DE VALIDACIÓN (TOP - DOWN)           =
            ==============================================================*/

            /*== 1. Verificando campos obligatorios generales ==*/
            if($tipo_doc=="" || $dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $email=="" || $clave1=="" || $clave2==""){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan campos obligatorios por llenar.","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== 2. VALIDACIÓN: Tipo de Documento ==*/
            if($tipo_doc != "V" && $tipo_doc != "E"){
                $alerta=["tipo"=>"simple","titulo"=>"Tipo de documento inválido","texto"=>"Seleccione un tipo de documento válido (V o E).","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            /*== 3. VALIDACIÓN: Cédula (DNI) ==*/
            if(!preg_match("/^[0-9]{7,10}$/", $dni)){
                $msj_dni = (is_numeric($dni)) 
                    ? "La cédula debe tener entre 7 y 10 dígitos (escribiste ".strlen($dni).")." 
                    : "La cédula '$dni' no es válida. No debe incluir letras, puntos ni espacios, solo números.";
                $alerta=["tipo"=>"simple","titulo"=>"Error en Documento","texto"=>$msj_dni,"icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            /*== 4. Verificando DNI repetido en la Base de Datos ==*/
            $check_dni=$this->ejecutarConsulta("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni' AND usuario_tipo_documento='$tipo_doc'");
            if($check_dni->rowCount()>0){
                $alerta=["tipo"=>"simple","titulo"=>"Cédula Duplicada","texto"=>"La cédula $tipo_doc-$dni ya pertenece a otro usuario registrado en el sistema.","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== 5. VALIDACIÓN: Nombres (Estricto: Solo letras) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $nombre)){
                $alerta=["tipo"=>"simple","titulo"=>"Nombre no válido","texto"=>"El nombre '$nombre' contiene números o caracteres no permitidos. Solo se permiten letras.","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            /*== 6. VALIDACIÓN: Apellidos (Estricto: Solo letras) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $apellido)){
                $alerta=["tipo"=>"simple","titulo"=>"Apellido no válido","texto"=>"El apellido '$apellido' contiene números o caracteres especiales. Por favor, use solo letras.","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            /*== 7. VALIDACIÓN: Nombre de Usuario (Para Login) ==*/
            if(!preg_match("/^[a-zA-Z0-9_]{4,20}$/", $usuario)){
                $alerta=["tipo"=>"simple","titulo"=>"Usuario inválido","texto"=>"El usuario de inicio de sesión no permite espacios ni caracteres raros (solo letras, números y guión bajo).","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            /*== 8. Verificando Usuario repetido ==*/
            $check_usuario=$this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
            if($check_usuario->rowCount()>0){
                $alerta=["tipo"=>"simple","titulo"=>"Usuario Duplicado","texto"=>"El nombre de usuario '$usuario' ya está ocupado por otra persona. Elige uno distinto.","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== 9. VALIDACIÓN: Email ==*/
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $alerta = ["tipo" => "simple", "titulo" => "Email inválido", "texto" => "El formato del correo electrónico es incorrecto.", "icono" => "error"];
                return json_encode($alerta); exit();
            }
            $check_email=$this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
            if($check_email->rowCount()>0){
                $alerta=["tipo"=>"simple","titulo"=>"Correo Duplicado","texto"=>"El correo electrónico '$email' ya se encuentra registrado.","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== 10. VALIDACIÓN: Claves ==*/
            if(strlen($clave1) < 7){
                $alerta=["tipo"=>"simple","titulo"=>"Error en Clave","texto"=>"Por seguridad, la contraseña debe tener al menos 7 caracteres.","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }
            if($clave1!=$clave2){
                $alerta=["tipo"=>"simple","titulo"=>"Error en Clave","texto"=>"Las contraseñas no coinciden. Escríbelas con cuidado.","icono"=>"error"]; return json_encode($alerta); exit();
            }else{
                $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]);
            }

            /*==============================================================
            =                    FIN DE LAS VALIDACIONES                   =
            ==============================================================*/

            /*== Gestión de Foto ==*/
            $img_dir="../views/fotos/";
            $foto="";
            if(isset($_FILES['usuario_foto']) && $_FILES['usuario_foto']['name']!="" && $_FILES['usuario_foto']['size']>0){
                if(!file_exists($img_dir)){ mkdir($img_dir,0777); }
                if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
                    $alerta=["tipo"=>"simple","titulo"=>"Error de Fotografía","texto"=>"El formato de la imagen no está permitido (Solo JPG/PNG).","icono"=>"error"]; return json_encode($alerta); exit();
                }
                $foto=str_ireplace(" ","_",$nombre)."_".rand(0,100);
                $foto=(mime_content_type($_FILES['usuario_foto']['tmp_name'])=='image/jpeg') ? $foto.".jpg" : $foto.".png";
                if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
                    $alerta=["tipo"=>"simple","titulo"=>"Error de Fotografía","texto"=>"No se pudo subir la imagen al servidor.","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

            /*== Preparando datos para el registro ==*/
            $usuario_datos_reg=[
                ["campo_nombre"=>"usuario_tipo_documento","campo_marcador"=>":Tipo","campo_valor"=>$tipo_doc],
                ["campo_nombre"=>"usuario_dni","campo_marcador"=>":Dni","campo_valor"=>$dni],
                ["campo_nombre"=>"usuario_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
                ["campo_nombre"=>"usuario_apellido","campo_marcador"=>":Apellido","campo_valor"=>$apellido],
                ["campo_nombre"=>"usuario_usuario","campo_marcador"=>":Usuario","campo_valor"=>$usuario],
                ["campo_nombre"=>"usuario_email","campo_marcador"=>":Email","campo_valor"=>$email],
                ["campo_nombre"=>"usuario_clave","campo_marcador"=>":Clave","campo_valor"=>$clave],
                ["campo_nombre"=>"usuario_foto","campo_marcador"=>":Foto","campo_valor"=>$foto],
                ["campo_nombre"=>"caja_id","campo_marcador"=>":Caja","campo_valor"=>$caja],
                ["campo_nombre"=>"rol_id","campo_marcador"=>":Rol","campo_valor"=>$rol],
                ["campo_nombre"=>"usuario_estado","campo_marcador"=>":Estado","campo_valor"=>"Activo"]
            ];

            /*== Ejecutando el registro ==*/
            $registrar_usuario=$this->guardarDatos("usuario",$usuario_datos_reg);

            if($registrar_usuario->rowCount()==1){
                $this->guardarBitacora("Usuarios", "Registro", "Se registró el usuario: " . $nombre . " " . $apellido);
                $alerta=["tipo"=>"redireccionar","titulo"=>"Éxito","texto"=>"El empleado ha sido registrado correctamente en el sistema.","icono"=>"success" ,"url" => APP_URL."userList/"];
            }else{
                if(is_file($img_dir.$foto)){ chmod($img_dir.$foto,0777); unlink($img_dir.$foto); }
                $alerta=["tipo"=>"simple","titulo"=>"Error de Servidor","texto"=>"No se pudo registrar el usuario en la Base de Datos.","icono"=>"error"];
            }
            return json_encode($alerta);
        }


        /*----------  Controlador listar usuario  ----------*/
        public function listarUsuarioControlador($pagina,$registros,$url,$busqueda){
            $pagina=$this->limpiarCadena($pagina);
            $registros=$this->limpiarCadena($registros);
            $url=$this->limpiarCadena($url);
            $url=APP_URL.$url."/";
            $busqueda=$this->limpiarCadena($busqueda);
            $tabla="";

            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
            $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            if(isset($busqueda) && $busqueda!=""){
                $consulta_datos="SELECT * FROM usuario WHERE ((usuario_id!='1' AND usuario_id!='".$_SESSION['id']."') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
                $consulta_total="SELECT COUNT(usuario_id) FROM usuario WHERE ((usuario_id!='1' AND usuario_id!='".$_SESSION['id']."') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%'))";
            }else{
                $consulta_datos="SELECT * FROM usuario WHERE usuario_id!='1' AND usuario_id!='".$_SESSION['id']."' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
                $consulta_total="SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id!='1' AND usuario_id!='".$_SESSION['id']."'";
            }

            $datos = $this->ejecutarConsulta($consulta_datos);
            $datos = $datos->fetchAll();
            $total = $this->ejecutarConsulta($consulta_total);
            $total = (int) $total->fetchColumn();
            $numeroPaginas =ceil($total/$registros);

            $tabla.='<div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr class="has-background-link-dark">
                            <th class="has-text-centered has-text-white">#</th>
                            <th class="has-text-centered has-text-white">Nombres</th>
                            <th class="has-text-centered has-text-white">Cédula</th>
                            <th class="has-text-centered has-text-white">Usuario</th>
                            <th class="has-text-centered has-text-white">Rol Asignado</th>
                            <th class="has-text-centered has-text-white">Foto</th>
                            <th class="has-text-centered has-text-white" colspan="3">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>';

            if($total>=1 && $pagina<=$numeroPaginas){
                $contador=$inicio+1;
                $pag_inicio=$inicio+1;
                foreach($datos as $rows){
                    
                    $rol_etiqueta = '<span class="tag is-light">Desconocido</span>';
                    if(isset($rows['rol_id'])){
                        if($rows['rol_id'] == 1){ $rol_etiqueta = '<span class="tag is-danger is-light">Administrador</span>'; } 
                        elseif($rows['rol_id'] == 2){ $rol_etiqueta = '<span class="tag is-info is-light">Vendedor</span>'; } 
                        elseif($rows['rol_id'] == 3){ $rol_etiqueta = '<span class="tag is-success is-light">Supervisor</span>'; }
                    }

                    $estado = (isset($rows['usuario_estado'])) ? $rows['usuario_estado'] : "Activo";
                    if($estado == "Activo"){
                        $btn_estado = '<button type="submit" class="button is-warning is-rounded is-small" title="Inhabilitar Sistema"><i class="fas fa-user-slash"></i></button>';
                    } else {
                        $btn_estado = '<button type="submit" class="button is-dark is-rounded is-small" title="Activar Sistema"><i class="fas fa-user-check"></i></button>';
                    }

                    // Se añade visualización de Cédula en la tabla general
                    $cedula_str = $rows['usuario_tipo_documento']."-".$rows['usuario_dni'];

                    $tabla.='
                        <tr class="has-text-centered" >
                            <td style="vertical-align: middle;">'.$contador.'</td>
                            <td style="vertical-align: middle;">'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
                            <td style="vertical-align: middle;"><strong>'.$cedula_str.'</strong></td>
                            <td style="vertical-align: middle;">'.$rows['usuario_usuario'].'</td>
                            <td style="vertical-align: middle;">'.$rol_etiqueta.'</td>
                            <td style="vertical-align: middle;">
                                <a href="'.APP_URL.'userPhoto/'.$rows['usuario_id'].'/" class="button is-info is-rounded is-small"><i class="fas fa-camera"></i></a>
                            </td>
                            <td style="vertical-align: middle;">
                                <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >
                                    <input type="hidden" name="modulo_usuario" value="inhabilitar">
                                    <input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">
                                    '.$btn_estado.'
                                </form>
                            </td>
                            <td style="vertical-align: middle;">
                                <a href="'.APP_URL.'userUpdate/'.$rows['usuario_id'].'/" class="button is-success is-rounded is-small"><i class="fas fa-sync"></i></a>
                            </td>
                            <td style="vertical-align: middle;">
                                <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >
                                    <input type="hidden" name="modulo_usuario" value="eliminar">
                                    <input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">
                                    <button type="submit" class="button is-danger is-rounded is-small"><i class="far fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>';
                    $contador++;
                }
                $pag_final=$contador-1;
            }else{
                $tabla.='<tr class="has-text-centered" ><td colspan="9">No hay registros en el sistema</td></tr>';
            }

            $tabla.='</tbody></table></div>';

            if($total>0 && $pagina<=$numeroPaginas){
                $tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
                $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
            }
            return $tabla;
        }

        /*----------  Controlador eliminar usuario (BLINDADO) ----------*/
        public function eliminarUsuarioControlador(){
            $id=$this->limpiarCadena($_POST['usuario_id']);
            if($id==1){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se puede eliminar el usuario principal","icono"=>"error"]; return json_encode($alerta); exit(); }

            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos=$datos->fetch();

            /* AUDITORÍA 1: ¿Tiene ventas? */
            $check_ventas=$this->ejecutarConsulta("SELECT usuario_id FROM venta WHERE usuario_id='$id' LIMIT 1");
            if($check_ventas->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Acción Denegada","texto"=>"No se puede eliminar este usuario porque tiene VENTAS registradas en el sistema. Sugerencia: Proceda a Inhabilitarlo.","icono"=>"warning"]; return json_encode($alerta); exit(); }

            /* AUDITORÍA 2: ¿Tiene compras registradas? */
            $check_compras=$this->ejecutarConsulta("SELECT usuario_id FROM compra WHERE usuario_id='$id' LIMIT 1");
            if($check_compras->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Acción Denegada","texto"=>"No se puede eliminar este usuario porque ha registrado ÓRDENES DE COMPRA. Sugerencia: Proceda a Inhabilitarlo.","icono"=>"warning"]; return json_encode($alerta); exit(); }

            /* AUDITORÍA 3: ¿Recibió camiones (recepciones)? */
            $check_recepcion=$this->ejecutarConsulta("SELECT usuario_id FROM recepcion WHERE usuario_id='$id' LIMIT 1");
            if($check_recepcion->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Acción Denegada","texto"=>"No se puede eliminar porque este usuario ha recibido MERCANCÍA en el almacén. Sugerencia: Proceda a Inhabilitarlo.","icono"=>"warning"]; return json_encode($alerta); exit(); }
            
            // Mantenemos el historial de auditoría pasándolo al usuario principal (Admin)
            $this->ejecutarConsulta("UPDATE bitacora SET usuario_id='1' WHERE usuario_id='$id'");

            $eliminarUsuario=$this->eliminarRegistro("usuario","usuario_id",$id);
            if($eliminarUsuario->rowCount()==1){
                if(is_file("../views/fotos/".$datos['usuario_foto'])){ chmod("../views/fotos/".$datos['usuario_foto'],0777); unlink("../views/fotos/".$datos['usuario_foto']); }
                $alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"Usuario eliminado del sistema","icono"=>"success"];
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo eliminar de la base de datos","icono"=>"error"];
            }
            return json_encode($alerta);
        }

        /*----------  Controlador inhabilitar usuario  ----------*/
        public function inhabilitarUsuarioControlador(){
            $id = $this->limpiarCadena($_POST['usuario_id']);

            if($id == 1 || $id == $_SESSION['id']){
                $alerta = ["tipo" => "simple", "titulo" => "Acción Denegada", "texto" => "No puedes inhabilitar tu propio usuario o al Administrador principal.", "icono" => "error"];
                return json_encode($alerta); exit();
            }

            $datos = $this->ejecutarConsulta("SELECT usuario_estado FROM usuario WHERE usuario_id='$id'");
            $datos = $datos->fetch();

            $nuevo_estado = ($datos['usuario_estado'] == "Activo") ? "Inhabilitado" : "Activo";

            $usuario_datos_up = [
                ["campo_nombre" => "usuario_estado", "campo_marcador" => ":Estado", "campo_valor" => $nuevo_estado]
            ];

            $condicion = ["condicion_campo" => "usuario_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

            if($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)){
                $alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "El estado del usuario se actualizó a: ".$nuevo_estado, "icono" => "success"];
            } else {
                $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo cambiar el estado", "icono" => "error"];
            }
            return json_encode($alerta);
        }
/*----------  Controlador actualizar usuario  ----------*/
        public function actualizarUsuarioControlador(){

            // 1. Capturamos el ID. Si el formulario no lo envía, asumimos que es el de la sesión actual
            $id = isset($_POST['usuario_id']) ? $this->limpiarCadena($_POST['usuario_id']) : $_SESSION['id'];
            
            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ 
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado.","icono"=>"error"]; 
                return json_encode($alerta); exit(); 
            }
            $datos=$datos->fetch();

            // 2. CAPTURA INTELIGENTE: Si el formulario no envía estos datos (como en el primer login), conservamos los que ya están en la BD
            $tipo_doc = isset($_POST['usuario_tipo_documento']) ? $this->limpiarCadena($_POST['usuario_tipo_documento']) : $datos['usuario_tipo_documento'];
            $dni = isset($_POST['usuario_dni']) ? $this->limpiarCadena($_POST['usuario_dni']) : $datos['usuario_dni'];
            $nombre = isset($_POST['usuario_nombre']) ? $this->limpiarCadena($_POST['usuario_nombre']) : $datos['usuario_nombre'];
            $apellido = isset($_POST['usuario_apellido']) ? $this->limpiarCadena($_POST['usuario_apellido']) : $datos['usuario_apellido'];
            $usuario = isset($_POST['usuario_usuario']) ? $this->limpiarCadena($_POST['usuario_usuario']) : $datos['usuario_usuario'];
            $email = isset($_POST['usuario_email']) ? $this->limpiarCadena($_POST['usuario_email']) : $datos['usuario_email'];
            $caja = isset($_POST['usuario_caja']) ? $this->limpiarCadena($_POST['usuario_caja']) : $datos['caja_id'];
            $rol = isset($_POST['usuario_rol']) ? $this->limpiarCadena($_POST['usuario_rol']) : $datos['rol_id'];
            
            $clave1 = isset($_POST['usuario_clave_1']) ? $this->limpiarCadena($_POST['usuario_clave_1']) : "";
            $clave2 = isset($_POST['usuario_clave_2']) ? $this->limpiarCadena($_POST['usuario_clave_2']) : "";

            // 3. CAPTURA DE PREGUNTAS
            $p1 = isset($_POST['usuario_pregunta_1']) ? $this->limpiarCadena($_POST['usuario_pregunta_1']) : $datos['usuario_pregunta_1'];
            $p2 = isset($_POST['usuario_pregunta_2']) ? $this->limpiarCadena($_POST['usuario_pregunta_2']) : $datos['usuario_pregunta_2'];
            $p3 = isset($_POST['usuario_pregunta_3']) ? $this->limpiarCadena($_POST['usuario_pregunta_3']) : $datos['usuario_pregunta_3'];

            // 4. CAPTURA DE RESPUESTAS (Se encriptan solo si el usuario escribió algo nuevo)
            if(isset($_POST['usuario_respuesta_1']) && trim($_POST['usuario_respuesta_1']) != ""){
                $r1 = password_hash(strtolower(trim($this->limpiarCadena($_POST['usuario_respuesta_1']))), PASSWORD_BCRYPT);
            } else {
                $r1 = $datos['usuario_respuesta_1'];
            }

            if(isset($_POST['usuario_respuesta_2']) && trim($_POST['usuario_respuesta_2']) != ""){
                $r2 = password_hash(strtolower(trim($this->limpiarCadena($_POST['usuario_respuesta_2']))), PASSWORD_BCRYPT);
            } else {
                $r2 = $datos['usuario_respuesta_2'];
            }

            if(isset($_POST['usuario_respuesta_3']) && trim($_POST['usuario_respuesta_3']) != ""){
                $r3 = password_hash(strtolower(trim($this->limpiarCadena($_POST['usuario_respuesta_3']))), PASSWORD_BCRYPT);
            } else {
                $r3 = $datos['usuario_respuesta_3'];
            }

            // 5. VALIDACIÓN DE NUEVA CONTRASEÑA
            if($clave1!="" || $clave2!=""){
                if (strlen($clave1) < 7) {
                    $alerta = ["tipo" => "simple", "titulo" => "Clave insegura", "texto" => "Por seguridad, la nueva contraseña debe tener al menos 7 caracteres.", "icono" => "error"];
                    return json_encode($alerta); exit();
                }
                if($clave1!=$clave2){ 
                    $alerta=["tipo"=>"simple","titulo"=>"Error en Clave","texto"=>"Las contraseñas que ingresaste no coinciden.","icono"=>"error"]; 
                    return json_encode($alerta); exit(); 
                }else{ 
                    $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]); 
                }
            }else{
                $clave=$datos['usuario_clave']; // Conserva la vieja si dejaron el campo vacío
            }

            // 6. ACTUALIZAR BASE DE DATOS
            $usuario_datos_up=[
                ["campo_nombre"=>"usuario_tipo_documento","campo_marcador"=>":Tipo","campo_valor"=>$tipo_doc],
                ["campo_nombre"=>"usuario_dni","campo_marcador"=>":Dni","campo_valor"=>$dni],
                ["campo_nombre"=>"usuario_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
                ["campo_nombre"=>"usuario_apellido","campo_marcador"=>":Apellido","campo_valor"=>$apellido],
                ["campo_nombre"=>"usuario_usuario","campo_marcador"=>":Usuario","campo_valor"=>$usuario],
                ["campo_nombre"=>"usuario_email","campo_marcador"=>":Email","campo_valor"=>$email],
                ["campo_nombre"=>"usuario_clave","campo_marcador"=>":Clave","campo_valor"=>$clave],
                ["campo_nombre"=>"caja_id","campo_marcador"=>":Caja","campo_valor"=>$caja],
                ["campo_nombre"=>"rol_id","campo_marcador"=>":Rol","campo_valor"=>$rol],
                ["campo_nombre"=>"usuario_pregunta_1","campo_marcador"=>":P1","campo_valor"=>$p1],
                ["campo_nombre"=>"usuario_respuesta_1","campo_marcador"=>":R1","campo_valor"=>$r1],
                ["campo_nombre"=>"usuario_pregunta_2","campo_marcador"=>":P2","campo_valor"=>$p2],
                ["campo_nombre"=>"usuario_respuesta_2","campo_marcador"=>":R2","campo_valor"=>$r2],
                ["campo_nombre"=>"usuario_pregunta_3","campo_marcador"=>":P3","campo_valor"=>$p3],
                ["campo_nombre"=>"usuario_respuesta_3","campo_marcador"=>":R3","campo_valor"=>$r3]
            ];

            $condicion=["condicion_campo"=>"usuario_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

            if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){
                if($id==$_SESSION['id']){
                    $_SESSION['nombre']=$nombre;
                    $_SESSION['apellido']=$apellido;
                    $_SESSION['usuario']=$usuario;
                    
                    // 1. QUITAMOS EL CANDADO DE SEGURIDAD
                    if(isset($_SESSION['seguridad_pendiente'])){
                        unset($_SESSION['seguridad_pendiente']);
                    }

                    // 2. LO REDIRIGIMOS DIRECTO AL DASHBOARD
                    $alerta=[
                        "tipo"=>"redireccionar",
                        "url"=> APP_URL."dashboard/",
                        "titulo"=>"¡Registro Exitoso!",
                        "texto"=>"Tu cuenta está configurada y protegida.",
                        "icono"=>"success"
                    ];
                } else {
                    // Si un admin está editando a otro usuario, solo recargamos
                    $alerta=["tipo"=>"recargar","titulo"=>"¡Actualizado!","texto"=>"El usuario se actualizó correctamente.","icono"=>"success"];
                }
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error BD","texto"=>"No se pudieron guardar los datos en el sistema.","icono"=>"error"];
            }
            return json_encode($alerta);
        }

        /*----------  Controladores de fotos  ----------*/
        public function actualizarFotoUsuarioControlador(){
            $id=$this->limpiarCadena($_POST['usuario_id']);
            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos=$datos->fetch();
            $img_dir="../views/fotos/";
            if(!isset($_FILES['usuario_foto']) || $_FILES['usuario_foto']['name']=="" && $_FILES['usuario_foto']['size']<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Seleccione una foto","icono"=>"error"]; return json_encode($alerta); exit(); }
            if(!file_exists($img_dir)){ if(!mkdir($img_dir,0777)){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error directorio","icono"=>"error"]; return json_encode($alerta); exit(); } }
            if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Formato incorrecto","icono"=>"error"]; return json_encode($alerta); exit(); }
            if($datos['usuario_foto']!=""){ $foto=explode(".", $datos['usuario_foto']); $foto=$foto[0]; }else{ $foto=str_ireplace(" ","_",$datos['usuario_nombre'])."_".rand(0,100); }
            switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){ case 'image/jpeg': $foto=$foto.".jpg"; break; case 'image/png': $foto=$foto.".png"; break; }
            chmod($img_dir,0777);
            if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al subir imagen","icono"=>"error"]; return json_encode($alerta); exit(); }
            if(is_file($img_dir.$datos['usuario_foto']) && $datos['usuario_foto']!=$foto){ chmod($img_dir.$datos['usuario_foto'], 0777); unlink($img_dir.$datos['usuario_foto']); }
            $usuario_datos_up=[["campo_nombre"=>"usuario_foto","campo_marcador"=>":Foto","campo_valor"=>$foto]];
            $condicion=["condicion_campo"=>"usuario_id","condicion_marcador"=>":ID","condicion_valor"=>$id];
            if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){ 
                if($id==$_SESSION['id']){ $_SESSION['foto']=$foto; }
                $alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"Foto actualizada","icono"=>"success"]; 
            }else{ $alerta=["tipo"=>"recargar","titulo"=>"Alerta","texto"=>"No se pudo actualizar BD","icono"=>"warning"]; }
            return json_encode($alerta);
        }

        public function eliminarFotoUsuarioControlador(){
            $id=$this->limpiarCadena($_POST['usuario_id']);
            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos=$datos->fetch();
            $img_dir="../views/fotos/";
            chmod($img_dir,0777);
            if(is_file($img_dir.$datos['usuario_foto'])){ chmod($img_dir.$datos['usuario_foto'],0777); if(!unlink($img_dir.$datos['usuario_foto'])){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al borrar archivo","icono"=>"error"]; return json_encode($alerta); exit(); } }
            $usuario_datos_up=[["campo_nombre"=>"usuario_foto","campo_marcador"=>":Foto","campo_valor"=>""]];
            $condicion=["condicion_campo"=>"usuario_id","condicion_marcador"=>":ID","condicion_valor"=>$id];
            if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){ 
                if($id==$_SESSION['id']){ $_SESSION['foto']="default.png"; }
                $alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"Foto eliminada","icono"=>"success"]; 
            }else{ $alerta=["tipo"=>"recargar","titulo"=>"Alerta","texto"=>"Foto borrada, error en BD","icono"=>"warning"]; }
            return json_encode($alerta);
        }
    }