<?php
    require_once('config.php');
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_solicitud = $_GET['id'];
    
        // Obtener el ID de la oferta relacionada con la solicitud
        $consultar_alquiler= "SELECT id_publicacion FROM alquiler WHERE id = ?";
        $buscar_alquiler =mysqli_prepare($conexion,$consultar_alquiler);
        mysqli_stmt_bind_param($buscar_alquiler,"i", $id_solicitud);
        mysqli_stmt_execute($buscar_alquiler);
        $resultados_alquiler = mysqli_stmt_get_result($buscar_alquiler);
        $alquiler=mysqli_fetch_assoc($resultados_alquiler);
        $id_publicacion = $alquiler['id_publicacion'];
        
        mysqli_stmt_close($buscar_alquiler);
    
        // Eliminar la solicitud
        $eliminar_solicitud = "DELETE FROM alquiler WHERE id = ?";
        $realizar_eliminacion = mysqli_prepare($conexion,$eliminar_solicitud);
        mysqli_stmt_bind_param($realizar_eliminacion,"i", $id_solicitud);
        if ($stmt->execute()) {
            echo"<script src='./js/sweetAlert2.js'></script>
                <script>
                Swal.fire({
                    title: 'Solicitud eliminada',
                    text: 'se ha eliminado la solicitud de alquiler del usuario.',
                    icon: 'success'
                  });
                </script>
            ";
            header("Location: detalles_publicacion.php?id=" . $id_publicacion); // Redirige de nuevo a detalles_alquiler.php
            exit();
        } else {
                echo'
            <div class="container mt-5 mb-2 ">
                <div class="row justify-content-center align-content-center">
                    <div class="col-md-6 contenedor-cartel">
                        <div class="icono-cartel">
                        <box-icon name="error-alt" type="solid" class="icon_error" size="md"></box-icon>
                        </div>
                        <h2 class="titulo-cartel"> Ha ocurrido un error!</h2>
                        <p class="text-cartel">No se ha podido eliminar la solicitud del usuario</p>
                        <div class="boton-cartel">
                        <a class="enlace-cartel" href="./detalles_publicacion.php">Volver atras</a>
                        </div>
                    </div>
                </div>
            </div>';
        }
        mysqli_stmt_close($realizar_eliminacion);
    }
?>