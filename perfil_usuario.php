<?php
    session_start();

    if (!isset($_SESSION["id"])) {
        header("location: iniciar_sesion.php");
        exit;
    }
    
    $id_mostrar_usuario= $_SESSION["id"];
    
    if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $id_mostrar_usuario= $_GET["id"];
    }
    
    include './BD/conexion.php';

    $consulta= "SELECT * FROM usuario WHERE id = ?";
    $preparar_consulta=mysqli_prepare($conexion,$consulta);
    mysqli_stmt_bind_param($preparar_consulta,"i", $id_mostrar_usuario);
    mysqli_stmt_execute($preparar_consulta);
    $resultado = mysqli_stmt_get_result($preparar_consulta);
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario) {
        $nombre = $usuario["nombre"];
        $apellido = $usuario["apellido"];
        $foto_perfil = $usuario["foto"];
        $dni=$usuario["documento"];
        $email=$usuario["email"];
        $intereses = $usuario["intereses"];
        $bio = $usuario["biografia"];
        $admin = $usuario["admin"];
    } else {
        echo "Usuario no encontrado";
        exit;
    }
    $activo= ($id_mostrar_usuario != $_SESSION["id"]) ? "AND p.estado = 1" : "";
    $publicaciones = "
        SELECT p.*,AVG(r.puntuacion) as avg_puntuacion
        FROM publicacion p
        LEFT JOIN resena r ON p.id = r.id_publicacion
        WHERE p.id_usuario= ? $activo
        GROUP BY p.id
        ORDER BY p.fecha_subida DESC";
    $buscar_publicaciones =mysqli_prepare($conexion,$publicaciones);
    mysqli_stmt_bind_param($buscar_publicaciones,"i", $id_mostrar_usuario);
    mysqli_stmt_execute($buscar_publicaciones);
    $publis = mysqli_stmt_get_result($buscar_publicaciones);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
    <?php include 'cabecera.php';?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil" class="img-fluid rounded-circle mb-3" width="150" height="150">
                        <h4><?php echo $nombre . ' ' . $apellido; ?></h4>
                        <?php 
                        echo ($usuario["certificacion"] == 1) ? '<img height="40" width="40" src="./Imagenes/verify_4458197.png" title="Usuario verificado">' : '';
                        echo ($admin == 1) ? ' <img height=40 width=40 src="./Imagenes/user_567902.png" title="Administrador">' : ''; 
                        ?>
                    </div>
                </div>
            <?php
                $consultar_verificacion = "SELECT * FROM solicitud WHERE id_usuario = ?";
                $solcitud_verificacion=mysqli_prepare($conexion,$consultar_verificacion);
                mysqli_stmt_bind_param($solcitud_verificacion,"i", $id_mostrar_usuario);
                mysqli_stmt_execute($solcitud_verificacion);
                $resultados_verificacion=mysqli_stmt_get_result($solcitud_verificacion);
                $resultado_verificacion=mysqli_num_rows($resultados_verificacion);
                $esperando_verificacion = (($resultado_verificacion)> 0);
                mysqli_stmt_close($solcitud_verificacion);
            ?>

            <!--[Verificacion]-->
            <?php if ($id_mostrar_usuario == $_SESSION["id"]): ?>
            <div class="cotainer contenedor_verificacion">
                <?php if ($usuario["certificacion"] == 0): ?>
                <?php if ($esperando_verificacion): ?>
                    <div class="text-center">
                        <div class="alert alert-info mt-4">Esperando verificación.</div>
                    </div>
                <?php else: ?>
                <div class="card mt-4 card_usuario text-center">
                    <div class="card-body card_usuario">
                        <button type="button"class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#Solicitar<?php echo $id_mostrar_usuario;?>">Solicitar Verificacion</button>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            <br>
                <?php
                     if (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
            
                    if (isset($_GET['mensaje'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['mensaje']) . '</div>';
                    }


                ?>
                <?php endif; ?>
            </div>
            <div class="col-md-12">
                <div class="card mb-3 card_usuario"> 
                    <div class="card-header bg-dark text-white">
                        <h2>Información general</h2>
                    </div>
                    <div class="card-body card_usuario">
                        <div class="mb-3">
                            <h5>Intereses</h5>
                            <p><?php echo $intereses; ?></p>
                        </div>
                        <div>
                            <h5>Biografía</h5>
                            <p><?php echo $bio; ?></p>
                        </div>
                        <div>
                            <h5>Dni</h5>
                            <p><?php echo $dni; ?></p>
                        </div>
                        <div>
                            <h5>Correo electronico</h5>
                            <p><?php echo $email; ?></p>
                        </div>
                    </div>
                    <div class="card card_usuario">
                        <div class="card-header text-white bg-personalizado">
                            <h2>Ofertas de Alquiler</h2>
                        </div>
                    <div class="card-body card_usuario"> 
                        <?php if ($publis->num_rows> 0):?>
                            <div class="list-group">
                                    <?php while($publicacion=$publis->fetch_assoc()): ?>
                                    <a href="detalles_publicacion.php?id=<?php echo $publicacion["id"]; ?>" class="list-group-item list-group-item-action mb-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?php echo $publicacion["titulo"]; ?>
                                                <?php echo "<small class='text'>".number_format($publicacion["costo"])." \$ </small>"; ?>
                                                <!-- Muestra el badge si la oferta está inactiva y el usuario es el dueño del perfil -->
                                                <?php if ($publicacion["estado"] == 0 && $id_mostrar_usuario == $_SESSION["id"]): ?>
                                                    <span class="badge bg-secondary">Inactiva</span>
                                                <?php endif; ?>
                                            </h5>
                                            <small>
                                                <?php 
                                                $estrellas = round($publicacion["avg_puntuacion"]);
                                                for ($i = 1; $i <= 5; $i++): 
                                                    if ($i <= $estrellas): ?>
                                                        <i class="fa-solid fa-star"></i>
                                                    <?php else: ?>
                                                        <i class="fa-regular fa-star"></i>
                                                    <?php endif; 
                                                endfor; ?>
                                            </small>
                                        </div>
                                    </a>
                                    <?php endwhile; ?>
                                </div>
                                <?php else: ?>
						            <p>No tiene ofertas de alquiler publicadas.</p>
					            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>      
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="Solicitar<?php echo $id_mostrar_usuario;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        <div class="modal-header text-white bg-personalizado">
            <h5 class="modal-title" id="exampleModalLabel">Solicitar verificacion de Cuenta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
            <div class="col-md-12">
                <form action="procesar_solicitud.php" method="post" enctype="multipart/form-data">
                    <div class="upload-box mb-3">
                        <label class="upload-label" for="dni_frente">Foto Frente DNI</label>
                        <span class=""><i class="fa-solid fa-camera"></i></span>
                        <input type="file" id="dni_frente" name="dni_frente" class="upload-input">
                    </div>
                                    
                    <div class="upload-box mb-3">
                        <label class="" for="dni_dorso">Foto Dorso DNI</label>
                        <span class=""><i class="fa-solid fa-camera"></i></span>
                        <input type="file" id="dni_dorso" name="dni_dorso" class="upload-input">
                    </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <input type="hidden" name="enviar_solicitud" value="<?php $id_mostrar_usuario?>">'
            <button type="submit" class="btn btn-primary">Enviar documentacion</button>
          </form>
        </div>
        </div>
    </div>
    </div>                               





    <?php include 'pie.php'?>
    <!--[Scripts]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>                      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>