<?php 
    $error=array();
    include './BD/conexion.php';

    // Verificar si el usuario ya ha iniciado sesión, en cuyo caso redirige al perfil
    if (isset($_SESSION["id"])) {
      header("location: perfil_usuario.php");
      exit;
    }


    $mensaje='';
    $mensaje_excitoso='';

    //Almacenar los valores de los siguientes campos
    $nombre_reg = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $apellido_reg = isset($_POST["apellido"]) ? $_POST["apellido"] : "";
    $dni_reg = isset($_POST["dni"]) ? $_POST["dni"] : "";
    $email_reg = isset($_POST["email"]) ? $_POST["email"] : "";
    $intereses_reg = isset($_POST["intereses"]) ? $_POST["intereses"] : "";
    $biografia_reg = isset($_POST["bio"]) ? $_POST["bio"] : "";
    $correo_reg = isset($_POST["email"]) ? $_POST["email"] : "";
    if($_SERVER["REQUEST_METHOD"]=='POST'){
       // Obtener datos del formulario
      $nombre = $_POST["nombre"];
      $apellido = $_POST["apellido"];
      $email = $_POST["email"];
      $documento=$_POST["dni"];
      $contrasena = $_POST["contrasena"];
      $confirmar_contrasena = $_POST["conf_contra"];
      $intereses = $_POST["intereses"];
      $biografia = $_POST["bio"];
      $admin = 0; // Valor por defecto para la columna admin
      $certificacion= null;// Valor por defecto para la columna certificacion

      function contieneNumeros($cadena) {
        // Verificar si la cadena contiene números
        return preg_match('/\d/', $cadena);
      }

      //Verificar que no se ingresen numeros en el nombre y apellido.
      if (contieneNumeros($nombre) && contieneNumeros($apellido)) {
        $error[]= "No se permiten números en los campos de nombre y apellido.";
      } 
      
      //Verificar que el documento no posea mas de 8 digitos.
      if($documento<10000000 || $documento>99999999){
        $error[]="El documento debe poseer solamente 8 digitos.";
      }//verificar que el correo ingresado sea valido.
      
      

      // Verificar que las contraseñas coincidan
      if($contrasena !== $confirmar_contrasena) {
          $error[] = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
      } else {
          // Verificar y manejar la carga de la foto de perfil
          $foto = $_FILES["foto"];

          // Directorio donde se guardarán las imágenes
          $directorio_destino = "galeria/";

          // Nombre de archivo generado aleatoriamente
          $nombre_archivo = uniqid() . "_" . basename($foto["name"]);
          $ruta_archivo = $directorio_destino . $nombre_archivo;

          // Verificar si es una imagen válida
          $tipo_archivo = pathinfo($ruta_archivo, PATHINFO_EXTENSION);
          $extensiones_permitidas = array("jpg", "jpeg", "png", "webp");

          if (in_array(strtolower($tipo_archivo), $extensiones_permitidas)) {
              // Mover el archivo cargado al directorio de imágenes
              if (move_uploaded_file($foto["tmp_name"], $ruta_archivo)) {
                  // Insertar nuevos datos de usuario en la base de datos
                  $contrasena_encriptada = password_hash($contrasena, PASSWORD_BCRYPT);
                  $sql = "INSERT INTO usuario (nombre, apellido, documento, certificacion, email, contrasena, intereses, foto, biografia, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                  if ($stmt = mysqli_prepare($conexion, $sql)) {
                      mysqli_stmt_bind_param($stmt, "sssssssssb", $nombre, $apellido, $documento, $certificacion, $email, $contrasena_encriptada, $intereses, $ruta_archivo, $biografia, $admin);
                      if (mysqli_stmt_execute($stmt)) {
                          $mensaje = "El Registro se ha realizado excitosamente, ahora puedes <a href='./iniciar_sesion.php'>Iniciar sesion.</a>";
                          $mensaje_excitoso = "alert-success";
                      } else {
                          $error[] = "Hubo un problema al registrar el usuario. Inténtalo nuevamente.";
                      }
                  }
              } else {
                  $error[] = "Hubo un problema al cargar la foto de perfil."; 
              }
          } else {
              $error[] = "Formato de archivo no válido. Por favor, elige una imagen válida (jpg, jpeg, png o webp).";
          }
      }
    
    }  
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Practica</title>
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
  </head>
  <body>
      <?php
         include './cabecera.php';
      ?>
      <!----------[Contenido Registro]--------->
      <main>
          <div class="container mt-5 contenido_registro">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card_registro">
                            <div class="card-header text-center"><h2> Registrarse </h2></div>
                            <div class="card-body">
                              <br>
                               <?php
                                  if(count($error)> 0){
                                      echo"<div class='alert alert-danger' role='alert'>";
                                      foreach($error as $err){
                                        echo "<li> ". $err . "</li>";
                                        echo "<br>";
                                      }
                                      echo"</div>";  
                                  }else{
                                      echo"<div class='alert $mensaje_excitoso' role='alert'>$mensaje</div>";
                                  }
                                ?>
                              <form class="mb-md-3 mt-md-4 pb-3" method="POST" action="./registrarse.php" enctype="multipart/form-data">

                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="nombre">Nombre: </label>
                                      <input type="text" id="nombre" class="form-control form-control-md" name="nombre"  value="<?php echo $nombre_reg;?>" required/>
                                    </div>

                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="apellido">Apellido: </label>
                                      <input type="text" id="apellido" class="form-control form-control-md" name="apellido"  value="<?php echo $apellido_reg;?>"required/>
                                    </div>  

                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="dni">Documento: </label>
                                      <input type="number" id="dni" class="form-control form-control-md" name="dni"  value="<?php echo $dni_reg;?>"required/>
                                    </div>


                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="bio">Biografia: </label>
                                      <textarea id="biografia" class="form-control form-control-md" name="bio" rows="5" >
                                        <?php echo $biografia_reg;?>
                                      </textarea>
                                    </div>

                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="intereses">Intereses: </label>
                                      <textarea id="intereses" class="form-control form-control-md" name="intereses" rows="4">
                                          <?php echo $intereses_reg;?>
                                      </textarea>
                                    </div>

                                    <div class="form-outline form-black mb-4 text-center mx-auto">
                                      <label class="form-label" for="foto">Fotografia de rostro: </label>
                                      <input type="file" id="foto" class="form-control form-control-md" name="foto"/>
                                    </div> 

                                    <div class="form-outline form-black mb-4">
                                      <label class="form-label" for="email">Email: </label>
                                      <input type="email" id="email" class="form-control form-control-md" name="email" required value="<?php echo $correo_reg;?>"/>
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                      <label class="form-label" for="contraseña">Contraseña: </label>
                                      <input type="password" id="contraseña" class="form-control form-control-md" name="contrasena" required/>
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                      <label class="form-label" for="ccontraseña">Confirmar contraseña: </label>
                                      <input type="password" id="ccontraseña" class="form-control form-control-md" name="conf_contra" required/>
                                    </div>

                                    <div class="form-check form-check-inline mb-4">
                                        <input class="form-check-input" type="checkbox" id="terminos" name="terminos" value="Aceptar" required>
                                        <label class="form-check-label" for="terminos"> Acepto los terminos y condiciones.</label>
                                    </div>
                                    
                                    <div class="form-outline form-white mb-4 text-center">
                                        <input class="btn btn-outline-dark btn-lg px-5" type="submit" value="Registrarme" name="registrar">
                                    </div>
                              </form>
                            </div>
                        </div>
                    </div>
                </div>
                              

                              

                                
                                    
                                
                             

          </div>
        </main>
        <?php
          include './pie.php';
        ?>                     
        <!------------------------------------>
        <!---------------------------------------------------------------------------------------->
      <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>                          
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
