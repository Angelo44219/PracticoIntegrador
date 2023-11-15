<?php
    session_start();
    require_once './BD/conexion.php'; // Incluye el archivo de configuración de la base de datos
    
    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION["id"])) {
        header("Location: iniciar_sesion.php"); // Redirige al usuario a la página de inicio de sesión si no ha iniciado sesión
        exit();
    }
    
    // Verificar si se proporcionó un ID de alquiler válido en la URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $idPublicacion = $_GET['id'];
        
        // Consulta SQL para verificar si el usuario actual es el propietario del alquiler
        $sql = "SELECT id_usuario FROM publicacion WHERE id = ?";
    
        // Preparar la consulta
        if ($stmt = mysqli_prepare($conexion, $sql)) {
            // Vincular parámetros a la consulta
            mysqli_stmt_bind_param($stmt, "i", $idPublicacion);
    
            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                $resultado = mysqli_stmt_get_result($stmt);
    
                if ($fila = mysqli_fetch_assoc($resultado)) {
                    $idUsuarioAlquiler = $fila['id_usuario'];
    
                    // Verificar si el usuario actual es el propietario del alquiler
                    if ($_SESSION['id'] === $idUsuarioAlquiler) {
                        // Eliminar el alquiler de la base de datos
                        $sqlEliminar = "DELETE FROM publicacion WHERE id = ?";
                        if ($stmtEliminar = mysqli_prepare($conexion, $sqlEliminar)) {
                            // Vincular parámetros a la consulta de eliminación
                            mysqli_stmt_bind_param($stmtEliminar, "i", $idPublicacion);
    
                            // Ejecutar la consulta de eliminación
                            if (mysqli_stmt_execute($stmtEliminar)) {
                                // Redirigir al usuario a una página de éxito o a la página principal
                                header("Location: Index.php");
                                exit();
                            } else {
                                echo '<div class="container mt-5"><p>Error al eliminar el alquiler: ' . mysqli_error($conexion) . '</p></div>';
                            }
                        }
                    } else {
                        echo '<div class="container mt-5"><p>No tienes permiso para eliminar este alquiler.</p></div>';
                    }
                } else {
                    echo '<div class="container mt-5"><p>El alquiler especificado no existe.</p></div>';
                }
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo '<div class="container mt-5"><p>Parámetro de ID de alquiler no válido.</p></div>';
    }
    
    // Incluye el pie de página
    require_once('pie.php');
?>
