<?php 
    $error=array();
    include './BD/conexion.php';

    $regex="^[A-ZÑa-zñáéíóúÁÉÍÓÚ'° ]+$//^";
    $regex2=" /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/";
    if(isset($_POST['registrar'])){

    $nombre=mysqli_real_escape_string($conexion,$_POST['nombre']);
        $apellido=mysqli_real_escape_string($conexion,$_POST['apellido']);
        $documento=mysqli_real_escape_string($conexion,$_POST['dni']);
        $biografia=mysqli_real_escape_string($conexion,$_POST['bio']);
        $foto=mysqli_real_escape_string($conexion,$_POST['foto']);    
        $admin=0;
        $certificacion=0;
        $fecha="dd/mm/yy";
        $email=mysqli_real_escape_string($conexion,$_POST['email']);
        $contrasena=mysqli_real_escape_string($conexion,$_POST['contrasena']);


                                    
        $imagen='';
        if(isset($_FILES['foto'])){
          $archivo=$_FILES['foto'];
          $nombre=$_FILES['name'];
          $tipo=$_FILES['type'];
           $ruta_temporal=$_FILES['tmp_name'];
          $tamaño=$_FILES['size'];
          $dimensiones=getimagesize($ruta_temporal);
          $ancho=$dimensiones[0];
          $alto=$dimensiones[1];
          $directorio="Imagenes/";

          if($tipo!='image/jpg' && $tipo!='image/webp' && $tipo!='image/png' && $tipo!='image/JPG' && $tipo!='image/jpeg'
            && $tipo!='image/gif'){

            $error[]="El archivo subido no es una imagen.";

          }else if($tamaño > 3*1024*1024){

            $error[]="El maximo permitido de una imagen es de 3mb";

          }else{

            $src= $directorio.$nombre;
            move_uploaded_file($ruta_temporal,$src);
            $imagen="Imagenes/".$nombre;

          }
        }

        $contra_encr=password_hash($contrasena,PASSWORD_BCRYPT);
                                    
            
        $consulta="SELECT * FROM usuario WHERE email='$email';";
        $registro=mysqli_query($conexion,$consulta);
                                  
        if(!mysqli_num_rows($registro)>0){

            if($nombre=="" && $apellido=="" && $documento=="" && $email=="" && $contrasena==""){
              $error[]="Complete los campos obligatorios por favor.";

            }elseif(preg_match($regex,$nombre)){
              $error[]="El nombre que ha ingresado no es valido.";
            }elseif(preg_match($regex,$apellido)){
              $error[]="El apellido que ha ingresado no es valido.";
            }elseif($documento<10000000 || $documento>99999999){
              $error[]="El documento debe poseer solamente 8 digitos.";
            }elseif(preg_match($regex2,$email)< 0){
              $error[]="El email que ha ingresado no es valido.";
            }
            else{
              $consulta_reg ="INSERT INTO usuario (nombre,apellido,documento,biografia,foto,admin,email,contrasena) VALUES ('$nombre','$apellido',$documento,'$biografia','$imagen',$admin,'$email','$contra_encr');";
              $resultado=mysqli_query($conexion,$consulta_reg);
                                            
              if($resultado){
                 echo"<div class='alert alert-success' role='alert'>
                     El registro se ha realizado con exito!";
                echo"</div>"; 
               }else{
                echo"<div class='alert alert-danger' role='alert'>
                     Ha ocurrido un error: ".$consulta."<br>".mysqli_error($conexion);
                echo"</div>";
              }
            }

          }else{
             echo"<div class='alert alert-danger' role='alert'>
                 Este correo ya se encuentra registrado.";
            echo"</div>";

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
    <!----------[Navbar Rappi]------------>
    <nav class="navbar navbar-expand-lg navbar-dark p-3 header">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">RapiBNB</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class=" collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto ">
              <li class="nav-item link-header">
                <a class="nav-link mx-2 " aria-current="page" href="./Index.php">Inicio</a>
              </li>
              <li class="nav-item link-header">
                <a class="nav-link mx-2" href="./iniciar_sesion.php">Iniciar Sesion</a>
              </li>
              <li class="nav-item link-header">
                <a class="nav-link mx-2 active" href="./registrarse.php">Registrarse</a>
              </li>
              <li class="nav-item dropdown link-header">
                <a class="nav-link mx-2 dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Alquileres
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li><a class="dropdown-item" href="#">Buscar alquiler</a></li>
                  <li><a class="dropdown-item" href="#">Sobre nosotros</a></li>
                  <li><a class="dropdown-item" href="#">Contactanos</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        </nav>
        <!------------------------------------>
        <!----------[Contenido Registro]--------->
        <main>
          <div class="container">
              <section class="vh-50">
                    <div class="container py-5 h-390">
                      <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                          <div class="card bg-light text-black" style="border-radius: 1rem;">
                            <div class="card-body p-5 text-center">

                              <h2 class="mb-5">Registro</h2>  

                              <br>
                              <?php
                                  if(count($error)> 0){
                                    echo"<div class='alert alert-danger' role='alert'>";
                                    foreach($error as $err){
                                      echo "<li> ". $err . "<br>";
                                    }
                                    echo"</div>";  
                                  }
                              ?>
                              <br>     
                              <form class="mb-md-3 mt-md-4 pb-3" method="POST" action="./registrarse.php">

                                <div class="form-outline form-black mb-4">
                                  <input type="text" id="nombre" class="form-control form-control-lg" name="nombre"/>
                                  <label class="form-label" for="nombre">Nombre</label>
                                </div>

                                <div class="form-outline form-black mb-4">
                                  <input type="text" id="apellido" class="form-control form-control-lg" name="apellido"/>
                                  <label class="form-label" for="apellido">Apellido</label>
                                </div>


                                <div class="form-outline form-black mb-4">
                                  <input type="number" id="dni" class="form-control form-control-lg" name="dni"/>
                                  <label class="form-label" for="dni">Documento</label>
                                </div>


                                <div class="form-outline form-black mb-4">
                                  <textarea id="biografia" class="form-control form-control-md" name="bio">
                                  </textarea>
                                  <label class="form-label" for="dni">Biografia</label>
                                </div>


                                <div class="form-outline form-black mb-4">
                                  <input type="file" id="foto" class="form-control form-control-md" name="foto"/>
                                  <label class="form-label" for="dni">Fotografia de rostro</label>
                                </div> 

                                <div class="form-outline form-black mb-4">
                                  <input type="email" id="email" class="form-control form-control-lg" name="email"/>
                                  <label class="form-label" for="email">Email</label>
                                </div>

                                <div class="form-outline form-white mb-4">
                                  <input type="password" id="contraseña" class="form-control form-control-lg" name="contrasena"/>
                                  <label class="form-label" for="contraseña">Contraseña </label>
                                </div>

                                <!--<div class="form-outline form-white mb-4">
                                  <input type="password" id="ccontraseña" class="form-control form-control-lg" name="concontrasena"/>
                                  <label class="form-label" for="ccontraseña">Confirmar contraseña</label>
                                </div>-->

                                <input class="btn btn-outline-dark btn-lg px-5" type="submit" value="Registrarme" name="registrar">
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </section>
        </div>

        <footer class="footerClass">
            <div class="Redes-Sociales">
                <a href="https://www.twitter.com">
                  <i class="fa-brands fa-square-x-twitter"></i>
                </a>
                <a href="https://www.facebook.com">
                  <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="https://www.instagram.com">
                  <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
            <p class="derechos">Rapibnb @ 2023, Todos los derechos reservados</p>
        </footer> 
        </main>
                             
        <!------------------------------------>
        <!---------------------------------------------------------------------------------------->
      <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>                          
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
