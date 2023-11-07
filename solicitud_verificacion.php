<?php
    include('./BD/conexion.php');

    // Verificar si el usuario es administrador
    session_start();
    if (!isset($_SESSION['id']) || $_SESSION['admin'] != 1) {
        header("Location: Index.php");
        exit();
    }
    
    $id_usuario = $_GET['id_usuario'];
    
    // Obtener la solicitud de verificación del usuario
    $query = "SELECT * FROM solicitud WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $solicitud = $result->fetch_assoc();
    $stmt->close();
    
    // Procesar la aceptación de la solicitud
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aceptar'])) {
        $fecha_verificacion = $_POST['fecha_vencimiento'];
    
        // Actualizar el estado de verificación y la fecha de verificación del usuario en la tabla 'usuarios'
        $query_update = "UPDATE usuario SET certificacion = 1, fecha_vencimiento = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($query_update);
        $stmt_update->bind_param("si", $fecha_verificacion, $id_usuario);
        $stmt_update->execute();
        $stmt_update->close();
    
        // Eliminar la solicitud de verificación de la tabla 'verificaciones'
        $query_delete = "DELETE FROM solicitud WHERE id_usuario = ?";
        $stmt_delete = $conexion->prepare($query_delete);
        $stmt_delete->bind_param("i", $id_usuario);
        $stmt_delete->execute();
        $stmt_delete->close();
    
        // Redirigir al panel de administración con un mensaje de éxito
        header("Location: index_administrador.php?success=1");
        exit();
    }
    
    
    // Obtener el nombre y apellido del usuario
    $query_nombre = "SELECT nombre, apellido FROM usuario WHERE id = ?";
    $stmt_nombre = $conexion->prepare($query_nombre);
    $stmt_nombre->bind_param("i", $id_usuario);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    if ($result_nombre->num_rows > 0) {
        $usuario = $result_nombre->fetch_assoc();
    } else {
        die("El usuario no existe o hubo un error al obtener los datos.");
    }
    $stmt_nombre->close();
    
    
    // Procesar el rechazo de la solicitud
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rechazar'])) {
        // Eliminar la solicitud de verificación de la tabla 'verificaciones'
        $query_delete = "DELETE FROM solicitud WHERE id_usuario = ?";
        $stmt_delete = $conexion->prepare($query_delete);
        $stmt_delete->bind_param("i", $id_usuario);
        $stmt_delete->execute();
        $stmt_delete->close();
    
        // Redirigir al panel de administración con un mensaje de éxito
        header("Location: index_administrador.php?rejected=1");
        exit();
    }
    
    $fecha_manana = date("Y-m-d", strtotime("+1 day"));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver solicitud de verificación</title>
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

    <div class="container mt-4 text-center">
        <h2>Solicitud de Verificación para <?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></h2>
        <div class="row justify-content-center">
            <div class="col-md-3">
                <h5>DNI Frente</h5>
                <img src="documentacion/<?php echo $solicitud['dni_frente']; ?>" alt="DNI Frente" class="img-fluid mb-3">
            </div>
            <div class="col-md-3">
                <h5>DNI Dorso</h5>
                <img src="documentacion/<?php echo $solicitud['dni_dorso']; ?>" alt="DNI Dorso" class="img-fluid mb-3">
            </div>
        </div>
        <div class="mt-4">
            <form method="post">
                <div class="mb-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento:</label>
                    <input type="date" class="form-control" name="fecha_vencimiento" value="<?php echo $fecha_manana; ?>" min="<?php echo $fecha_manana; ?>">
                </div>
                <button type="submit" name="aceptar" class="btn btn-success" onclick="setRequiredFechaVerificacion(true)">Aceptar Solicitud</button>
				<button type="submit" name="rechazar" class="btn btn-danger" onclick="setRequiredFechaVerificacion(false)">Rechazar Solicitud</button>
            </form>
        </div>
    </div>


    <?php
        include './pie.php';
    ?>
    <!--[Scripts]-->
    <script>
        function setRequiredFechaVerificacion(required) {
            document.querySelector('input[name="fecha_verificacion"]').required = required;
        }
    </script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>