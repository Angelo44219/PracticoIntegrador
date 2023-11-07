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
        $query = "UPDATE usuario SET certificacion = ?, fecha_vencimiento = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iss", $verificado, $fecha_verificacion, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Procesar la eliminación de un usuario, sus alquileres asociados y sus reseñas
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_usuario'])) {
        $user_id = $_POST['eliminar_usuario'];


        // Eliminar respuestas asociadas a las reseñas del usuario
        $query = "DELETE FROM respuesta_resena WHERE id_resena IN (SELECT id FROM resenia WHERE id_usuario = ?)";
        $stmt = mysqli_prepare($conexion, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }


        // Eliminar reseñas asociadas al usuario
        $query = "DELETE FROM resenia WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Eliminar alquileres asociados al usuario
        $query = "DELETE FROM publicacion WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Eliminar el usuario
        $query = "DELETE FROM usuario WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $query = "SELECT u.id,u.foto,u.nombre, u.apellido, u.email, u.certificacion, u.fecha_vencimiento, 
          CASE WHEN s.id_usuario IS NOT NULL THEN 1 ELSE 0 END AS tiene_solicitud
          FROM usuario u
          LEFT JOIN solicitud s ON u.id = s.id_usuario
          ORDER BY tiene_solicitud DESC, u.id ASC";
    $result = mysqli_query($conexion, $query);

    $fecha_manana = date("Y-m-d", strtotime("+1 day"));

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
<body>
    <?php
        include './cabecera.php';
    ?>

    <div class="container mt-5 table-responsive">
        <table class="table table-hover align-middle text-center text-capitalize table-bordered">
            <thead class="table table-dark table-active">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Certificacion</th>
                    <th>Fecha vencimiento</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><a href='perfil_usuario.php?id={$row['id']}'>{$row['id']}</a></td>";
                        echo "<td>{$row['nombre']}</td>";
                        echo "<td>{$row['apellido']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['certificacion']}</td>";
                        echo "<td>{$row['fecha_vencimiento']}</td>";
                        echo "<td>";
                        echo '<form method="post">';
                        echo '<input type="hidden" name="id_usuario" value="' . $row['id'] . '">';
                        echo '<select name="verificado" class="form-select">';
                        echo '<option value="0" ' . ($row['certificacion'] == 0 ? 'selected' : '') . '>No verificado</option>';
                        echo '<option value="1" ' . ($row['certificacion'] == 1 ? 'selected' : '') . '>Verificado</option>';
                        echo '</select>';
                        echo '<div class="mb-3">';
                        echo '<label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento:</label>';
                        echo '<input type="date" class="form-control" name="fecha_vencimiento" value="' . (empty($row['fecha_vencimiento']) ? $fecha_manana : $row['fecha_vencimiento']) . '" min="' . $fecha_manana . '">';
                        echo '</div>';
                        echo '<div class="text-center">';
                        echo '<button type="submit" class="btn btn-dark">Guardar</button>';
                        echo '</form>';
                        echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-userid="' . $row['id'] . '">Eliminar Usuario</button>';
                        
                        // Agregar botón para ver ofertas del usuario
                        echo '<a href="ofertas_usuario.php?id_usuario=' . $row['id'] . '" class="btn btn-info">Ver Ofertas</a>';
                        // Agregar el botón "Ver solicitud" aquí
                        $query_verificacion = "SELECT * FROM solicitud WHERE id_usuario = " . $row['id'];
                        $result_verificacion = mysqli_query($conexion, $query_verificacion);
                        if (mysqli_num_rows($result_verificacion) > 0) {
                            echo '<a href="solicitud_verificacion.php?id_usuario=' . $row['id'] . '" class="btn btn-warning">Ver Solicitud</a>';
                        } else {
                            // Si no hay solicitud, mostrar el botón en gris y deshabilitado
                            echo '<a href="#" class="btn btn-warning disabled">Ver Solicitud</a>';
                        }
                        echo '</div>';
                        echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
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

    <?php
        include './pie.php';
    ?>

    <!--[Scripts]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>