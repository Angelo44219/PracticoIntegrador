<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar alquiler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Lora:ital@0;1&family=Noto+Sans+Osmanya&family=Raleway:ital,wght@0,100;0,300;0,400;0,500;1,100&display=swap" rel="stylesheet">
</head>
<body>
    <?php
        session_start();

        require_once './BD/conexion.php';
        include './cabecera.php';

        $id_usuario = $_SESSION["id"];
        $id_publicacion = $_GET['id'];

        // Verificar si el usuario está verificado
        $query_usuario = "SELECT certificacion FROM usuario WHERE id = ?";
        $stmt_usuario = $conexion->prepare($query_usuario);
        $stmt_usuario->bind_param("i", $id_usuario);
        $stmt_usuario->execute();
        $resultado = $stmt_usuario->get_result();
        $usuario = $resultado->fetch_assoc();
        $es_verificado = $usuario['certificacion'];
        $stmt_usuario->close();

        // Obtener detalles del alquiler
        $query = "SELECT * FROM publicacion WHERE id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $alquiler = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Obtener la fecha de mañana
        $hoy = date('Y-m-d');

        $fecha_inicio_min = (!empty($alquiler["fecha_inicio"]) && $alquiler["fecha_inicio"] > $hoy) ? $alquiler["fecha_inicio"] : $hoy;
        $fecha_fin_max = $alquiler["fecha_fin"] ?? '2099-12-31'; // Puedes usar una fecha muy lejana como valor predeterminado si no hay fecha fin

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fecha_inicio = $_POST["fecha_inicio"];
            $fecha_fin = $_POST["fecha_fin"];
            $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400; // 86400 segundos en un día
            
            // Verifica colisiones con reservas existentes
            $query_colision = "SELECT * FROM alquiler WHERE id_usuario = ? AND (estado_alquiler = 'aceptado' OR estado_alquiler = 'pendiente') AND ((fecha_inicio <= ? AND fecha_fin >= ?) OR (fecha_inicio <= ? AND fecha_fin >= ?) OR (fecha_inicio >= ? AND fecha_fin <= ?))";
            $stmt_colision = $conexion->prepare($query_colision);
            $stmt_colision->bind_param("issssss", $id_usuario, $fecha_inicio, $fecha_inicio, $fecha_fin, $fecha_fin, $fecha_inicio, $fecha_fin);
            $stmt_colision->execute();
            $resultado_colision = $stmt_colision->get_result();

            // Verificar si el usuario ya tiene una reserva pendiente o aceptada y no está verificado
            if ($es_verificado == 0) { // Si el usuario no está verificado
                $query_reserva = "SELECT * FROM alquiler WHERE id_usuario = ? AND (estado_alquiler = 'aceptado' OR estado_alquiler = 'pendiente')";
                $stmt_reserva = $conexion->prepare($query_reserva);
                $stmt_reserva->bind_param("i", $id_usuario);
                $stmt_reserva->execute();
                $resultado_reserva = $stmt_reserva->get_result();
            
                if ($resultado_reserva->num_rows > 0) {
                    // El usuario ya tiene una reserva pendiente o aceptada
                    echo '<div class="container mt-4">';
                    echo '<div class="alert alert-danger text-center" role="alert">Ya aplicaste a una oferta de alquiler. Solo puedes aplicar a una oferta de alquiler a la vez por ser usuario regular.</div>';
                    echo '<div class="text-center"><a href="reservar_alquiler.php?id=' . $id_publicacion . '" class="btn btn-primary">Volver a reservar</a></div>';
                    echo '</div>';
                    include 'pie.php';
                    exit;
                }
                $stmt_reserva->close();
            }


            if ($resultado_colision->num_rows > 0) {
                // Hay una colisión
                echo '<div class="container mt-4">';
                echo '<div class="alert alert-danger text-center" role="alert">Ya tienes una reserva para esas fechas. Por favor, selecciona otras fechas.</div>';
                echo '<div class="text-center"><a href="reservar_alquiler.php?id=' . $id_publicacion . '" class="btn btn-primary">Volver a reservar</a></div>';
                echo '</div>';
            } else {
                // No hay colisión
            
                // Control de tiempo mínimo y máximo
                if ($dias < $alquiler["tiempo_minimo"] || $dias > $alquiler["tiempo_maximo"]) {
                    echo '<div class="container mt-4">';
                    echo '<div class="alert alert-danger text-center" role="alert">La duración de la reserva no cumple con los requisitos de permanencia.</div>';
                    echo '<div class="text-center" role="alert"><a href="detalles_publicacion.php?id=' . $id_publicacion . '" class="btn btn-primary">Volver a los detalles del alquiler</a></div>';
                    echo '</div>';
                } else {
                    $costo_total = $dias * $alquiler["costo"];

                    $estado = $es_verificado ? 'aceptado' : 'pendiente';

                    $query = "INSERT INTO alquiler (id_usuario, id_publicacion, fecha_inicio, fecha_fin, estado_alquiler) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("iisss", $id_usuario, $id_publicacion, $fecha_inicio, $fecha_fin, $estado);
                    if ($stmt->execute()) {
                        echo '<div class="container mt-4">';
                        echo '<div class="alert alert-success text-center" role="alert">Tu reserva ha sido enviada exitosamente.</div>';
                        echo '<div class="text-center"><a href="detalles_publicacion.php?id=' . $id_publicacion . '" class="btn btn-primary">Volver a los detalles del alquiler</a></div>';
                        echo '</div>';
                    } else {
                        echo '<div class="container mt-4">';
                        echo '<div class="alert alert-danger" role="alert">Hubo un error al enviar tu reserva. Por favor, inténtalo de nuevo.</div>';
                        echo '<a href="detalles_publicacion.php?id=' . $id_publicacion . '" class="btn btn-primary">Volver a los detalles del alquiler</a>';
                        echo '</div>';
                    }
                    $stmt->close();
                }
            }

        } else {
            echo '<div class="container mt-4">';
            echo '<h1 class="mb-4">Reservar alquiler</h1>';
            echo '<form action="reservar_alquiler.php?id=' . $id_publicacion . '" method="post">';
            echo '<div class="mb-3">';
            echo '<label for="fecha_inicio" class="form-label">Fecha de inicio</label>';
            echo '<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" min="' . $fecha_inicio_min . '" max="' . $fecha_fin_max . '" required>';
            echo '</div>';
            echo '<div class="mb-3">';
            echo '<label for="fecha_fin" class="form-label">Fecha de fin</label>';
            echo '<input type="date" class="form-control" id="fecha_fin" name="fecha_fin" min="' . $fecha_inicio_min . '" max="' . $fecha_fin_max . '" required>';
            echo '</div>';
            echo '<div id="precio_total" class="text-center"><b>Precio total: $0</div></b><br>'; // Lugar donde se mostrará el precio total
            echo '<div class="text-center"><button type="submit" class="btn btn-primary">Confirmar reserva</button></div>';
            echo '</form>';
            echo '</div>';
        }

        include './pie.php';
    ?>
    <!--[Scripts]-->
    <script>
        $(document).ready(function() {
        var costoPorDia = <?php echo $alquiler["costo"]; ?>;

        function calcularPrecioTotal() {
            var fechaInicio = new Date($('#fecha_inicio').value());
            var fechaFin = new Date($('#fecha_fin').value());

            var diferencia = (fechaFin - fechaInicio) / (1000 * 60 * 60 * 24) + 1;

            if (!isNaN(diferencia) && diferencia > 0) {
                var precioTotal = diferencia * costoPorDia;
                $('#precio_total').text('Precio total: $' + precioTotal.toFixed(2));
            } else {
                $('#precio_total').text('Precio total: $0');
            }
        }

        $('#fecha_inicio, #fecha_fin').change(calcularPrecioTotal);
        });
    </script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>