<?php
    require_once './BD/conexion.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_solicitud= $_GET['id'];
    
        // Obtener el ID de la oferta relacionada con la solicitud
        $consultar_alquiler = "SELECT id_publicacion FROM alquiler WHERE id = ?";
        $buscar_alquiler = mysqli_prepare($conexion,$consultar_alquiler);
        mysqli_stmt_bind_param($buscar_alquiler,"i", $id_solicitud);
        mysqli_stmt_execute($buscar_alquiler);
        $resultado_alquiler =mysqli_stmt_get_result($buscar_alquiler);
        $alquiler = mysqli_fetch_assoc($resultado_alquiler);
        $id_publicacion = $alquiler['id_publicacion'];
        
        mysqli_stmt_close($buscar_alquiler);
    
        $consultar_solicitud = "UPDATE alquiler SET estado_alquiler = 'aceptado' WHERE id = ?";
        $aceptar_solicitud = mysqli_prepare($conexion,$consultar_solicitud);
        mysqli_stmt_bind_param($aceptar_solicitud,"i", $id_solicitud);
        if (mysqli_stmt_execute($aceptar_solicitud)) {
            header("Location: detalles_publicacion.php?id=" . $id_publicacion); // Redirige de nuevo a detalles_alquiler.php
            exit();
        } else {
            echo "Error al aceptar la solicitud.";
        }
        mysqli_stmt_close($aceptar_solicitud);
    }
?>