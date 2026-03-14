<?php

    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel{

        /*----------  Controlador registrar usuario  ----------*/
        public function registrarUsuarioControlador(){

            // Capturamos los datos (incluyendo tipo de documento y cédula)
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

            /*== Verificando campos obligatorios ==*/
            if($tipo_doc=="" || $dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2==""){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan campos obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Nombre (Solo letras, sin números ni caracteres raros) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $nombre)){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Nombre no válido",
                    "texto"=>"El nombre '$nombre' contiene caracteres no permitidos o es muy corto. Solo se permiten letras.",
                    "icono"=>"error"
                ]; 
                return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Apellido (Solo letras, sin números ni caracteres raros) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $apellido)){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Apellido no válido",
                    "texto"=>"El apellido '$apellido' contiene números o caracteres especiales. Por favor, use solo letras.",
                    "icono"=>"error"
                ]; 
                return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Cédula (Solo números, entre 7 y 10 caracteres) ==*/
            if(!preg_match("/^[0-9]{7,10}$/", $dni)){
                // Personalizamos el mensaje según lo que escribió el usuario
                $msj_dni = (is_numeric($dni)) 
                    ? "La cédula debe tener entre 7 y 10 dígitos (escribiste ".strlen($dni).")." 
                    : "La cédula '$dni' no es válida. No debe incluir letras, puntos ni guiones, solo números.";

                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Error en Documento",
                    "texto"=>$msj_dni,
                    "icono"=>"error"
                ]; 
                return json_encode($alerta); exit();
            }

            /*== Verificando DNI repetido ==*/
            $check_dni=$this->ejecutarConsulta("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
            if($check_dni->rowCount()>0){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El número de CÉDULA ya se encuentra registrado","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== Verificando Usuario repetido ==*/
            $check_usuario=$this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
            if($check_usuario->rowCount()>0){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El USUARIO ya se encuentra registrado","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== Verificando Email ==*/
            if($email!=""){
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $alerta = ["tipo" => "simple", "titulo" => "Email inválido", "texto" => "Formato de correo incorrecto", "icono" => "error"];
                    return json_encode($alerta); exit();
                }
                $check_email=$this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El EMAIL ya se encuentra registrado","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

            /*== Verificando Claves ==*/
            if(strlen($clave1) < 7){
                $alerta=["tipo"=>"simple","titulo"=>"Error en Clave","texto"=>"La contraseña debe tener al menos 7 caracteres.","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

            if($clave1!=$clave2){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Las contraseñas no coinciden","icono"=>"error"]; return json_encode($alerta); exit();
            }else{
                $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]);
            }

            /*== Gestión de Foto ==*/
            $img_dir="../views/fotos/";
            $foto="";
            if(isset($_FILES['usuario_foto']) && $_FILES['usuario_foto']['name']!="" && $_FILES['usuario_foto']['size']>0){
                if(!file_exists($img_dir)){ mkdir($img_dir,0777); }
                if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Formato de imagen no permitido","icono"=>"error"]; return json_encode($alerta); exit();
                }
                $foto=str_ireplace(" ","_",$nombre)."_".rand(0,100);
                $foto=(mime_content_type($_FILES['usuario_foto']['tmp_name'])=='image/jpeg') ? $foto.".jpg" : $foto.".png";
                if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo subir la imagen","icono"=>"error"]; return json_encode($alerta); exit();
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
                $alerta=["tipo"=>"redireccionar","titulo"=>"Éxito","texto"=>"Usuario registrado correctamente","icono"=>"success" ,"url" => APP_URL."userList/"];
            }else{
                if(is_file($img_dir.$foto)){ chmod($img_dir.$foto,0777); unlink($img_dir.$foto); }
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo registrar el usuario","icono"=>"error"];
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
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Nombres</th>
                            <th class="has-text-centered">Usuario</th>
                            <th class="has-text-centered">Email</th>
                            <th class="has-text-centered">Rol Asignado</th>
                            <th class="has-text-centered">Foto</th>
                            <th class="has-text-centered" colspan="3">Opciones</th>
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

                    // Lógica del botón de estado (Inhabilitar/Activar)
                    $estado = (isset($rows['usuario_estado'])) ? $rows['usuario_estado'] : "Activo";
                    if($estado == "Activo"){
                        $btn_estado = '<button type="submit" class="button is-warning is-rounded is-small" title="Inhabilitar"><i class="fas fa-user-slash"></i></button>';
                    } else {
                        $btn_estado = '<button type="submit" class="button is-dark is-rounded is-small" title="Activar"><i class="fas fa-user-check"></i></button>';
                    }

                    $tabla.='
                        <tr class="has-text-centered" >
                            <td>'.$contador.'</td>
                            <td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
                            <td>'.$rows['usuario_usuario'].'</td>
                            <td>'.$rows['usuario_email'].'</td>
                            <td>'.$rol_etiqueta.'</td>
                            <td>
                                <a href="'.APP_URL.'userPhoto/'.$rows['usuario_id'].'/" class="button is-info is-rounded is-small"><i class="fas fa-camera"></i></a>
                            </td>

                            <td>
                                <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >
                                    <input type="hidden" name="modulo_usuario" value="inhabilitar">
                                    <input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">
                                    '.$btn_estado.'
                                </form>
                            </td>

                            <td>
                                <a href="'.APP_URL.'userUpdate/'.$rows['usuario_id'].'/" class="button is-success is-rounded is-small"><i class="fas fa-sync"></i></a>
                            </td>
                            <td>
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

        /*----------  Controlador eliminar usuario  ----------*/
        public function eliminarUsuarioControlador(){
            $id=$this->limpiarCadena($_POST['usuario_id']);
            if($id==1){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se puede eliminar el usuario principal","icono"=>"error"]; return json_encode($alerta); exit(); }

            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos=$datos->fetch();

            $check_ventas=$this->ejecutarConsulta("SELECT usuario_id FROM venta WHERE usuario_id='$id' LIMIT 1");
            if($check_ventas->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se puede eliminar, tiene ventas asociadas","icono"=>"error"]; return json_encode($alerta); exit(); }

            
            // IMPORTANTE: Mantenemos el historial de auditoría pasándolo al usuario principal (Admin)
            $this->ejecutarConsulta("UPDATE bitacora SET usuario_id='1' WHERE usuario_id='$id'");

            $eliminarUsuario=$this->eliminarRegistro("usuario","usuario_id",$id);
            if($eliminarUsuario->rowCount()==1){
                if(is_file("../views/fotos/".$datos['usuario_foto'])){ chmod("../views/fotos/".$datos['usuario_foto'],0777); unlink("../views/fotos/".$datos['usuario_foto']); }
                $alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"Usuario eliminado","icono"=>"success"];
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo eliminar","icono"=>"error"];
            }
            return json_encode($alerta);
        }

        /*----------  Controlador inhabilitar usuario  ----------*/
        public function inhabilitarUsuarioControlador(){
            $id = $this->limpiarCadena($_POST['usuario_id']);

            if($id == 1 || $id == $_SESSION['id']){
                $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No puedes inhabilitar este usuario", "icono" => "error"];
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
                $alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Estado actualizado a: ".$nuevo_estado, "icono" => "success"];
            } else {
                $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo cambiar el estado", "icono" => "error"];
            }
            return json_encode($alerta);
        }

        /*----------  Controlador actualizar usuario  ----------*/
        public function actualizarUsuarioControlador(){

            $id=$this->limpiarCadena($_POST['usuario_id']);
            $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
            if($datos->rowCount()<=0){ 
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Usuario no encontrado","icono"=>"error"]; 
                return json_encode($alerta); exit(); 
            }
            $datos=$datos->fetch();

            // Capturamos los nuevos campos de identidad y los de siempre
            $tipo_doc=$this->limpiarCadena($_POST['usuario_tipo_documento']);
            $dni=$this->limpiarCadena($_POST['usuario_dni']);
            $nombre=$this->limpiarCadena($_POST['usuario_nombre']);
            $apellido=$this->limpiarCadena($_POST['usuario_apellido']);
            $usuario=$this->limpiarCadena($_POST['usuario_usuario']);
            $email=$this->limpiarCadena($_POST['usuario_email']);
            $clave1=$this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2=$this->limpiarCadena($_POST['usuario_clave_2']);
            $caja=$this->limpiarCadena($_POST['usuario_caja']);
            
            $rol = isset($_POST['usuario_rol']) ? $this->limpiarCadena($_POST['usuario_rol']) : $datos['rol_id'];

            /*== Verificando campos obligatorios ==*/
            if($tipo_doc=="" || $dni=="" || $nombre=="" || $apellido=="" || $usuario==""){
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan campos obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Nombre (Solo letras) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $nombre)){
                $alerta=["tipo"=>"simple","titulo"=>"Nombre no válido","texto"=>"El nombre '$nombre' contiene caracteres no permitidos. Solo se permiten letras.","icono"=>"error"];
                return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Apellido (Solo letras) ==*/
            if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}$/", $apellido)){
                $alerta=["tipo"=>"simple","titulo"=>"Apellido no válido","texto"=>"El apellido '$apellido' contiene caracteres no permitidos. Solo se permiten letras.","icono"=>"error"];
                return json_encode($alerta); exit();
            }

            /*== VALIDACIÓN: Cédula (Solo números y longitud) ==*/
            if(!preg_match("/^[0-9]{7,10}$/", $dni)){
                $msj_dni = (is_numeric($dni)) 
                    ? "La cédula debe tener entre 7 y 10 dígitos (escribiste ".strlen($dni).")." 
                    : "La cédula '$dni' no es válida. No debe incluir letras ni símbolos.";
                
                $alerta=["tipo"=>"simple","titulo"=>"Error en Documento","texto"=>$msj_dni,"icono"=>"error"];
                return json_encode($alerta); exit();
            }

            /*== Verificando DNI repetido (Excluyendo al usuario actual) ==*/
            if($datos['usuario_dni']!=$dni){
                $check_dni=$this->ejecutarConsulta("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
                if($check_dni->rowCount()>0){ 
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El número de CÉDULA ya se encuentra registrado","icono"=>"error"]; 
                    return json_encode($alerta); exit(); 
                }
            }

            /*== Verificando Usuario repetido ==*/
            if($datos['usuario_usuario']!=$usuario){
                $check_usuario=$this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
                if($check_usuario->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El USUARIO ya existe","icono"=>"error"]; return json_encode($alerta); exit(); }
            }

            /*== Verificando Email ==*/
            if($email!="" && $datos['usuario_email']!=$email){
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $alerta = ["tipo" => "simple", "titulo" => "Email inválido", "texto" => "Formato de correo incorrecto", "icono" => "error"];
                    return json_encode($alerta); exit();
                }
                $check_email=$this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El EMAIL ya existe","icono"=>"error"]; return json_encode($alerta); exit(); }
            }

            /*== Gestión de Claves ==*/
            if($clave1!="" || $clave2!=""){
                if (strlen($clave1) < 7) {
                    $alerta = ["tipo" => "simple", "titulo" => "Error en Clave", "texto" => "La nueva contraseña debe tener al menos 7 caracteres.", "icono" => "error"];
                    return json_encode($alerta); exit();
                }
                if($clave1!=$clave2){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Las contraseñas no coinciden","icono"=>"error"]; return json_encode($alerta); exit(); }
                else{ $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]); }
            }else{
                $clave=$datos['usuario_clave'];
            }

            /*== Preparando Array de Actualización ==*/
            $usuario_datos_up=[
                ["campo_nombre"=>"usuario_tipo_documento","campo_marcador"=>":Tipo","campo_valor"=>$tipo_doc],
                ["campo_nombre"=>"usuario_dni","campo_marcador"=>":Dni","campo_valor"=>$dni],
                ["campo_nombre"=>"usuario_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
                ["campo_nombre"=>"usuario_apellido","campo_marcador"=>":Apellido","campo_valor"=>$apellido],
                ["campo_nombre"=>"usuario_usuario","campo_marcador"=>":Usuario","campo_valor"=>$usuario],
                ["campo_nombre"=>"usuario_email","campo_marcador"=>":Email","campo_valor"=>$email],
                ["campo_nombre"=>"usuario_clave","campo_marcador"=>":Clave","campo_valor"=>$clave],
                ["campo_nombre"=>"caja_id","campo_marcador"=>":Caja","campo_valor"=>$caja],
                ["campo_nombre"=>"rol_id","campo_marcador"=>":Rol","campo_valor"=>$rol]
            ];

            $condicion=["condicion_campo"=>"usuario_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

            if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){
                if($id==$_SESSION['id']){
                    $_SESSION['nombre']=$nombre;
                    $_SESSION['apellido']=$apellido;
                    $_SESSION['usuario']=$usuario;
                }
                $alerta=["tipo"=>"redireccionar","titulo"=>"Éxito","texto"=>"Usuario actualizado correctamente","icono"=>"success","url" => APP_URL."userList/"];
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo actualizar el usuario","icono"=>"error"];
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