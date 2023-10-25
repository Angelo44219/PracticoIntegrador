<?php
    include './BD/conexion.php';
    session_start();
    $email=$_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
    <main>
        <div class="Container perfil">
            <div class="row"> 
                <div class="col">
                    <?php
                        $consulta="SELECT foto,nombre,apellido FROM usuario WHERE email='$email'";
                        $resultado=mysqli_query($conexion,$consulta);

                        if(mysqli_num_rows($resultado)>0){
                            while($usuario=mysqli_fetch_assoc($resultado)){
                                echo "<img src='./Imagenes/".$usuario['foto']."' class='img_perfil'>";
                                echo "<h1>Hola ".$usuario['nombre']." ".$usuario['apellido']." !</h1>";
                            } 
                        }else{
                            echo"no se ha encontrado informacion de este usuario";
                        }
                            echo"<br><a href='cerrar_sesion.php'>Cerrar sesion</a>";
                        ?>
                </div>    
            </div>
        </div> 
    </main>
     
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>