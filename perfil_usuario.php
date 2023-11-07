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
    
    require_once './BD/conexion.php';

    $consulta= "SELECT * FROM usuario WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $id_mostrar_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario) {
        $nombre = $usuario["nombre"];
        $apellido = $usuario["apellido"];
        $foto_perfil = $usuario["foto"];
        $intereses = $usuario["intereses"];
        $bio = $usuario["biografia"];
        $admin = $usuario["admin"];
    } else {
        echo "Usuario no encontrado";
        exit;
    }
    $activo= ($id_mostrar_usuario != $_SESSION["id"]) ? "AND p.estado = 1" : "";
    $publicaciones = "
        SELECT *
        FROM publicacion p
        WHERE p.id_usuario= ? $activo
        GROUP BY p.id
        ORDER BY p.fecha_subida DESC";
    $stmt_alquileres = $conexion->prepare($publicaciones);
    $stmt_alquileres->bind_param("i", $id_mostrar_usuario);
    $stmt_alquileres->execute();
    $publis = $stmt_alquileres->get_result();

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
    <?php include 'cabecera.php'?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil" class="img-fluid rounded-circle mb-3" width="150" height="150">
                        <h4><?php echo $nombre . ' ' . $apellido; ?></h4>
                        <?php 
                        echo ($usuario["certificacion"] == 1) ? '<img height=40 width=40 src="./Imagenes/verify_4458197.png" title="Usuario verificado">' : '';
                        echo ($admin == 1) ? ' <img height=40 width=40 src="./Imagenes/user_567902.png" title="Administrador">' : ''; 
                        ?>
                    </div>
                </div>
            <?php
                $consultar_verificacion = "SELECT * FROM solicitud WHERE id_usuario = ?";
                $stmt_verificacion = $conexion->prepare($consultar_verificacion);
                $stmt_verificacion->bind_param("i", $id_mostrar_usuario);
                $stmt_verificacion->execute();
                $result_verificacion = $stmt_verificacion->get_result();
                $esperando_verificacion = ($result_verificacion->num_rows > 0);
                $stmt_verificacion->close();
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
                <div class="card mt-4 card_usuario">
                    <div class="card-header text-white text-center bg-personalizado">
                        <b>Verifica tu cuenta</b>
                    </div>
                    <div class="card-body card_usuario">
                        <form action="procesar_solicitud.php" method="post" enctype="multipart/form-data">
                            <div class="upload-box mb-3">
                                <label class="upload-label" for="dni_frente">Foto Frente DNI</label>
                                <span class="upload-icon"><i class="fa-solid fa-camera"></i></span>
                                <input type="file" id="dni_frente" name="dni_frente" class="upload-input">
                            </div>
                            
                            <div class="upload-box mb-3">
                                <label class="upload-label" for="dni_dorso">Foto Dorso DNI</label>
                                <span class="upload-icon"><i class="fa-solid fa-camera"></i></span>
                                <input type="file" id="dni_dorso" name="dni_dorso" class="upload-input">
                            </div>
        
                            <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-enviar">Enviar Documentación</button>
                            </div>
                        </form>
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
                <div class="card mb-4 card_usuario"> 
                    <div class="card-header bg-dark text-white">
                        <h2>Perfil de Usuario</h2>
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
                                            <!--<small>
                                                <?php 
                                                $estrellas = round($oferta["avg_puntuacion"]);
                                                for ($i = 1; $i <= 5; $i++): 
                                                    if ($i <= $estrellas): ?>
                                                        <img src="estrella.png" alt="Estrella">
                                                    <?php else: ?>
                                                        <img src="noestrella.png" alt="No Estrella">
                                                    <?php endif; 
                                                endfor; ?>
                                            </small>-->
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

    <?php include 'pie.php'?>
    <!--[Scripts]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>                      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>