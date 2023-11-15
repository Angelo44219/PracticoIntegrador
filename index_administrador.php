<?php
    include './BD/conexion.php';

    session_start();

    // Verificar si la sesión está activa y si el usuario es administrador
    
    if (!isset($_SESSION['id']) || $_SESSION['admin'] != 1) {
        // Si no es un administrador, redirigir a otra página o mostrar un mensaje de error
        header("Location: Index.php");
        exit();
    }

    // Procesar la modificación de estado de verificación
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario']) && isset($_POST['certificacion'])) {
        $user_id = $_POST['id_usuario'];
        $verificado = $_POST['certificacion'];
        $fecha_verificacion = null; // Inicializar la fecha de verificación

        // Si se establece como verificado (1), obtener la fecha de vencimiento
        if ($verificado == 1 && isset($_POST['fecha_vencimiento'])) {
            $fecha_verificacion = $_POST['fecha_vencimiento'];
        }

        // Actualizar el estado de verificación y la fecha de verificación del usuario en la base de datos
        $consultar_actualizacion = "UPDATE usuario SET certificacion = ?, fecha_vencimiento = ? WHERE id = ?";
        $actualizar_verificacion= mysqli_prepare($conexion, $query);

        if ($actualizar_verificacion) {
            mysqli_stmt_bind_param($actualizar_verificacion, "iss", $verificado, $fecha_verificacion, $user_id);
            mysqli_stmt_execute($actualizar_verificacion);
            mysqli_stmt_close($actualizar_verificacion);
        }
    }

    // Procesar la eliminación de un usuario, sus alquileres asociados y sus reseñas
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_usuario'])) {
        $user_id = $_POST['eliminar_usuario'];


        // Eliminar respuestas asociadas a las reseñas del usuario
        $consultar_respuesta = "DELETE FROM respuesta_resena WHERE id_resena IN (SELECT id FROM resena WHERE id_usuario = ?)";
        $eliminar_respuesta = mysqli_prepare($conexion, $consultar_respuesta);
        if ($eliminar_respuesta) {
            mysqli_stmt_bind_param($eliminar_respuesta, "i", $user_id);
            mysqli_stmt_execute($eliminar_respuesta);
            mysqli_stmt_close($eliminar_respuesta);
        }


        // Eliminar reseñas asociadas al usuario
        $consultar_resena = "DELETE FROM resena WHERE id_usuario = ?";
        $eliminar_resena= mysqli_prepare($conexion, $consultar_resena);
        
        if ($eliminar_resena) {
            mysqli_stmt_bind_param($eliminar_resena, "i", $user_id);
            mysqli_stmt_execute($eliminar_resena);
            mysqli_stmt_close($eliminar_resena);
        }

        // Eliminar publicaciones asociados al usuario
        $consultar_publicaciones = "DELETE FROM publicacion WHERE id_usuario = ?";
        $eliminar_publicaciones = mysqli_prepare($conexion, $consultar_publicaciones);
        
        if ($eliminar_publicaciones) {
            mysqli_stmt_bind_param($eliminar_publicaciones, "i", $user_id);
            mysqli_stmt_execute($eliminar_publicaciones);
            mysqli_stmt_close($eliminar_publicaciones);
        }

        // Eliminar el usuario
        $consultar_usuario = "DELETE FROM usuario WHERE id = ?";
        $eliminar_usuario = mysqli_prepare($conexion, $consultar_usuario);
        
        if ($eliminar_usuario) {
            mysqli_stmt_bind_param($eliminar_usuario, "i", $user_id);
            mysqli_stmt_execute($eliminar_usuario);
            mysqli_stmt_close($eliminar_usuario);
        }
    }

    $consultar_solicitudes = "SELECT u.id,u.foto,u.nombre, u.apellido, u.email, u.certificacion, u.fecha_vencimiento,u.admin,
          CASE WHEN s.id_usuario IS NOT NULL THEN 1 ELSE 0 END AS tiene_solicitud
          FROM usuario u
          LEFT JOIN solicitud s ON u.id = s.id_usuario
          ORDER BY tiene_solicitud DESC, u.id ASC";
    $resultados_solicitudes = mysqli_query($conexion, $consultar_solicitudes);

    $fecha_siguiente = date("Y-m-d", strtotime("+1 day"));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Lora:ital@0;1&family=Noto+Sans+Osmanya&family=Raleway:ital,wght@0,100;0,300;0,400;0,500;1,100&display=swap" rel="stylesheet">
</head>
<body class="contenido_perfiles">
    <?php
        include './cabecera.php';
    ?>

    <div class="container mt-5 pagina-perfiles">
        <div class="row">
            <h1 class="text-center text-white mt-2 mb-4">Usuarios Registrados</h1>
            <br>
            <?php
                while ($solicitud = mysqli_fetch_assoc($resultados_solicitudes)) {
                    echo "<div class=''col-md-12>";
                        echo "<div class='card cabecera-perfil'>";
                           echo "<div class='card-body cuerpo-perfil'>";
                                echo "<div class='row'>";
                                    echo "
                                        <div class='col-md-4 col-12'>
                                            <div class='perfil-imagen mr-4'><img src='".$solicitud['foto']."'></div>
                                        </div>";
                                        echo "<div class='col-md-8 col-12'>
                                            <p><strong>Id del usuario: </strong><a href='perfil_usuario.php?id={$solicitud['id']}'>{$solicitud['id']}</a></p>
                                            <h4 class='m-t-0 m-b-0'><strong>".$solicitud['nombre']." ".$solicitud['apellido']."</strong></h4>
                                            <p><strong>Correo electronico: </strong>".$solicitud['email']."</p>
                                            <p><strong>Certificacion: </strong>".$solicitud['certificacion']."</p>
                                            <p><strong>Administrador: </strong>".$solicitud['admin']."</p>
                                            <p><strong>Fecha de vencimiento: </strong>".$solicitud['fecha_vencimiento']."</p>
                                            <div class='mr-10'>";
                                                echo '<form method="post">';
                                                    echo '<input type="hidden" name="id_usuario" value="' . $solicitud['id'] . '">';
                                                    echo '<select name="verificado" class="form-select">';
                                                    echo '<option value="0" ' . ($solicitud['certificacion'] == 0 ? 'selected' : '') . '>No verificado</option>';
                                                    echo '<option value="1" ' . ($solicitud['certificacion'] == 1 ? 'selected' : '') . '>Verificado</option>';
                                                    echo '</select>';
                                                    echo '<div class="mb-3">';
                                                    echo '<label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento:</label>';
                                                    echo '<input type="date" class="form-control" name="fecha_vencimiento" value="' . (empty($solicitud['fecha_vencimiento']) ? $fecha_siguiente : $solicitud['fecha_vencimiento']) . '" min="' . $fecha_siguiente . '">';
                                                    echo '</div>';
                                                    echo '<div class="text-center">';
                                                        echo '<button type="submit" class="btn btn-dark"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>';
                                                    echo '</div>';
                                                echo '</form>';
                                                echo '<br>';
                                                echo '<div class="text-center pr-4">';
                                                    echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-userid="' . $solicitud['id'] . '"><i class="fa-solid fa-trash"></i> Eliminar</button>&nbsp;';
                                    
                                                    // Agregar botón para ver ofertas del usuario
                                                    echo '<a href="ofertas_usuario.php?id_usuario=' . $solicitud['id'] . '" class="btn btn-info"><i class="fa-solid fa-magnifying-glass"></i> Publicaciones</a>&nbsp;';
                                                    // Agregar el botón "Ver solicitud" aquí
                                                    $ver_solicitud = "SELECT * FROM solicitud WHERE id_usuario = ". $solicitud['id'];
                                                    $resultado_solicitud = mysqli_query($conexion, $ver_solicitud);
                                                    if (mysqli_num_rows($resultado_solicitud) > 0) {
                                                                echo '<a href="solicitud_verificacion.php?id_usuario=' . $solicitud['id'] . '" class="btn btn-warning"><i class="fa-solid fa-list-check"></i> Solicitud</a>&nbsp;';
                                                    } else {
                                                                // Si no hay solicitud, mostrar el botón en gris y deshabilitado
                                                                echo '<a href="#" class="btn btn-warning disabled"><i class="fa-solid fa-list-check"></i> Solicitud</a>&nbsp;';
                                                    }
                                                echo '</div>';
                                            echo"</div>";
                                        "</div>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
            ?>
        </div>
       
    </div>
    <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar esta cuenta de usuario?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form method="post">
                            <input type="hidden" name="eliminar_usuario" id="deleteUserId">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
       <!--[Modal edicion]-->
    <?php
        include './pie.php';
    ?>

    <!--[Scripts]-->
    <script>
    var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-userid');
            var modalInput = deleteModal.querySelector('#deleteUserId');
            modalInput.value = userId;
        });
    </script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>