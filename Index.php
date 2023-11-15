<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RapiBnB</title>
  <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
  <?php
    session_start();
    require_once './BD/conexion.php';
    require_once './cabecera.php';

    $sql_update = "UPDATE alquiler SET estado_alquiler = 'completado' WHERE fecha_fin = CURDATE() AND estado_alquiler = 'aceptado'";
    mysqli_query($conexion, $sql_update);

    // Obtener el ID del usuario logueado
    if (isset($_SESSION["id"])) {
        $id_usuario = $_SESSION["id"];

        // Consulta combinada para verificar la finalización del alquiler y si el usuario ha dejado una reseña,
        // además de verificar si el usuario está verificado.
        $consultar_resena = "SELECT a.id_publicacion, p.titulo, 
                  (SELECT r.id FROM resena r WHERE r.id_usuario = a.id_usuario AND r.id_publicacion = a.id_publicacion LIMIT 1) AS id_resena,
                  u.certificacion
              FROM alquiler a
              INNER JOIN publicacion p ON a.id_publicacion = p.id
              INNER JOIN usuario u ON a.id_usuario = u.id
              WHERE a.id_usuario = ? AND a.fecha_fin >= CURDATE() AND a.estado_alquiler = 'completado' AND u.certificacion = 1
              LIMIT 1";
        $preparar_resena=mysqli_prepare($conexion,$consultar_resena);
        mysqli_stmt_bind_param($preparar_resena,"i", $id_usuario);
        mysqli_stmt_execute($preparar_resena);
        $resultado_resena = mysqli_stmt_get_result($preparar_resena);

        if ($resena = mysqli_fetch_assoc($resultado_resena)) {
            $alquiler_id = $resena['id_publicacion'];
            $titulo_alquiler = $resena['titulo'];
            $resena_id = $resena['id_resena'];
            $verificado = $resena['certificacion'];

            // Si el usuario no ha dejado una reseña y está verificado, se muestra la opción de dejar una reseña
            if (empty($resena_id) && $verificado) {
                /*$mensaje_resena = "
                <div class='text-center'>
                  ¿Qué te ha parecido <b>{$titulo_alquiler}</b>? 
                  <a href='detalles_publicacion.php?id={$alquiler_id}#reseñas'>
                  ¡Haz clic aquí para dejar una reseña!</a>
                </div>";

                echo "<div class='alert alert-info'>{$mensaje_resena}</div>";*/
                echo"
                <script src='./js/sweetAlert2.js'></script>
                document.addEventListener('DOMContentLoaded', function(){
                    <script language= 'JavaScript'>
                        Swal.fire({
                          title: '<strong>¿Qué te ha parecido <b>{$titulo_alquiler}</b>?</strong>',
                          icon: 'info',
                          html: `
                            <a href='detalles_publicacion.php?id={$alquiler_id}'>¡Haz clic aquí para dejar una reseña!</a>,
                            and other HTML tags
                          `,
                          showCloseButton: true,
                          showCancelButton: true,
                          focusConfirm: false,
                          confirmButtonText: `
                            <i class='fa fa-thumbs-up'></i> De acuerdo!
                          `,
                          confirmButtonAriaLabel: 'Dejarlo para mas tarde',
                          cancelButtonText: `
                            <i class='fa fa-thumbs-down'></i>
                          `,
                          cancelButtonAriaLabel: 'Thumbs down'
                        });
                    </script>
                })";
                
            }
        }

        mysqli_stmt_close($preparar_resena);
    }

    include './ver_publicacion.php';
    require_once('./pie.php');
  ?>
  <!--[Scripts]-->
  <script src="./js/script.js"></script>
  <script src="./js/sweetAlert2.js"></script>
  <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>