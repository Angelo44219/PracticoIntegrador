<?php
    include('./BD/conexion.php');

    // Verificar si el usuario es un administrador
    session_start();
    if (!isset($_SESSION['id']) || $_SESSION['admin'] != 1) {
        // Si no es un administrador, redirigir a otra página o mostrar un mensaje de error
        header("Location: Index.php");
        exit();
    }
    
    // Obtener el ID del usuario de la URL
    if (isset($_GET['id_usuario'])) {
        $user_id = $_GET['id_usuario'];
        
        // Consulta para obtener las ofertas de alquiler del usuario
        $query = "SELECT id, titulo, ubicacion FROM publicacion WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }
    } else {
        // Si no se proporciona un ID de usuario válido en la URL, redirigir a otra página
        header("Location: index_administrador.php");
        exit();
    }
    
    // Procesar la eliminación de una oferta de alquiler
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_oferta'])) { 
        $id_publicacion = $_POST['eliminar_oferta'];
        
        // Consulta para eliminar la oferta de alquiler
        $query = "DELETE FROM publicacion WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_publicacion);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Redirigir de nuevo a la página de ver_ofertas.php
        header("Location: ofertas_usuario.php?user_id=" . $user_id);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones del usuario</title>
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

    <div class="container mt-4 table-responsive">
        <table class="table table-bordered table-sm text-center align-middle text-capitalize table-hover">
            <thead class="table-active table-dark">
                <tr>
                    <th>Id</th>
                    <th>Titulo</th>
                    <th>Ubicacion</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
            <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['titulo']}</td>";
                        echo "<td>{$row['ubicacion']}</td>";
                        echo "<td>";
                        
                        // Agregar botón para eliminar oferta de alquiler con ventana modal de confirmación
                        echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row['id'] . '">Eliminar Oferta</button>';
                        
                        // Ventana modal de confirmación
                        echo '<div class="modal fade" id="deleteModal' . $row['id'] . '" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">';
                        echo '<div class="modal-dialog">';
                        echo '<div class="modal-content">';
                        echo '<div class="modal-header">';
                        echo '<h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>';
                        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                        echo '</div>';
                        echo '<div class="modal-body">';
                        echo '¿Estás seguro de que deseas eliminar esta oferta de alquiler?';
                        echo '</div>';
                        echo '<div class="modal-footer">';
                        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>';
                        
                        // Formulario para enviar la solicitud de eliminación
                        echo '<form method="post">';
                        echo '<input type="hidden" name="eliminar_oferta" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn btn-danger">Eliminar</button>';
                        echo '</form>';
                        
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
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