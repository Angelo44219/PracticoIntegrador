<?php
    require_once './BD/conexion.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_solicitud= $_GET['id'];
    
        // Obtener el ID de la oferta relacionada con la solicitud
        $query_oferta = "SELECT id_publicacion FROM alquiler WHERE id = ?";
        $stmt_oferta = $conexion->prepare($query_oferta);
        $stmt_oferta->bind_param("i", $id_solicitud);
        $stmt_oferta->execute();
        $result_oferta = $stmt_oferta->get_result();
        $row_oferta = $result_oferta->fetch_assoc();
        $id_publicacion = $row_oferta['id_publicacion'];
        $stmt_oferta->close();
    
        $query = "UPDATE alquiler SET estado_alquiler = 'aceptado' WHERE id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_solicitud);
        if ($stmt->execute()) {
            header("Location: detalles_publicacion.php?id=" . $id_publicacion); // Redirige de nuevo a detalles_alquiler.php
            exit();
        } else {
            echo "Error al aceptar la solicitud.";
        }
        $stmt->close();
    }
?>