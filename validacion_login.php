<?php
    require_once './BD/conexion.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $correo=$_POST['email'];
        $contrasena=$_POST['contrasena'];
    }

    $consulta="SELECT id,nombre,apellido,contrasena,rol,certificacion FROM usuarios WHERE email=?";

    if($stmt=mysqli_prepare($conexion,$consulta)){
        mysqli_stmt_bind_param($stmt,"s",$correo);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt)==1){
                mysqli_stmt_bind_result($stmt,$id,$nombre,$apellido,$contra_crip,$rol,$certificacion);
                if(mysqli_stmt_fetch($stmt)){
                    if (password_verify($contrasena, $contra_crip)) {
                        // Inicio de sesión exitoso
                        session_start();
                        $_SESSION["id"] = $id;
                        $_SESSION["nombre"] = $nombre;
                        $_SESSION["apellido"] = $apellido;
                        $_SESSION["rol"] = $rol;
                        $_SESSION["certificacion"] = $certificacion; // Establecer la variable de sesión 'verificado'
                        header("location: perfil.php");
                    }else{
                        echo "La contraseña que ha ingresado es incorrecta. <a href='iniciar_sesion.php'>Inténtelo nuevamente</a>";
                    }
                }
            }else{
                echo "Este correo no se encuentra registrado <a href='iniciar_sesion.php'>Ir a registrarse</a>";
            }
        }else{
            echo "Ha ocurrido un error al ejecutarse la consulta.";
        }
    }else{
        echo "Ocurrio un error en la preparacion de la consulta.";
    }

    include './BD/cerrar_conexion.php';
?>