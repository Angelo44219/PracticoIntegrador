<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar publicacion</title>
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
    <?php
        require_once('./BD/conexion.php');
        require_once('./cabecera.php');
        
        $hoy = date('Y-m-d');
        
        if (!isset($_SESSION["id"])) {
            header("Location: iniciar_sesion.php");
            exit();
        }


        if(isset($_GET["id"]) && is_numeric($_GET["id"])){
            $id_publicacion=$_GET['id'];

            $sql = "SELECT p.*, u.id AS id_usuario FROM publicacion p
            INNER JOIN usuario u ON p.id_usuario = u.id
            WHERE p.id = ?";
            if ($stmt = mysqli_prepare($conexion, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $id_publicacion);
                if (mysqli_stmt_execute($stmt)) {
                    $resultado = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($resultado) == 1) {
                        $fila = mysqli_fetch_assoc($resultado);

                        if (isset($_SESSION['id']) && $_SESSION['id'] == $fila['id_usuario']) {
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $titulo = $_POST["titulo"];
                                $descripcion = $_POST["descripcion"];
                                $ubicacion = $_POST["ubicacion"];
                                $etiquetas = $_POST["etiqueta"];
                                $costo_alquiler = $_POST["costo"];
                                $tiempo_minimo = $_POST["tiempo_minimo"];
                                $tiempo_maximo = $_POST["tiempo_maximo"];
                                $cupo = $_POST["cupo"];
                                $fecha_inicio = $_POST["fecha_pub_inicio"];
                                $fecha_fin = $_POST["fecha_pub_fin"];

                                // Procesar y guardar los servicios modificados
                                $servicios = $_POST['servicio'];
                                $servicios_json = json_encode($servicios);

                                // Procesar y guardar las nuevas fotos del alquiler
                                $fotosSubidas = false;
                                $rutas_fotos = json_decode($fila['fotos'], true) ?: [];
                                function es_extension_permitida($filename) {
                                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                    $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif', 'avif', 'webp');
                                    return in_array(strtolower($ext), $extensiones_permitidas);
                                }
                                if (!empty($_FILES['fotos']['name'][0])) {
                                    for ($i = 0; $i < count($_FILES['fotos']['name']); $i++) {
                                        if (es_extension_permitida($_FILES['fotos']['name'][$i])) {
                                            $nombre_archivo = basename($_FILES['fotos']['name'][$i]);
                                            $ruta_destino = "galeria/" . $nombre_archivo;
                                            if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $ruta_destino)) {
                                                $rutas_fotos[] = $ruta_destino;
                                                $fotosSubidas = true;
                                            }
                                        } else {
                                            // Puedes añadir un mensaje para informar al usuario que ciertas imágenes no tienen una extensión válida.
                                            echo '<div class="alert alert-danger text-center">El archivo ' . $_FILES['fotos']['name'][$i] . ' no tiene una extensión válida. Se omitió.</div>';
                                        }
                                    }
                                }


                                if ($fotosSubidas) {
                                    $galeria_imagenes_json = json_encode($rutas_fotos);
                                    $sql_update = "UPDATE publicacion SET 
                                            titulo = ?, 
                                            descripcion = ?, 
                                            ubicacion = ?, 
                                            etiqueta = ?, 
                                            costo = ?, 
                                            tiempo_minimo = ?, 
                                            tiempo_maximo = ?, 
                                            cupo = ?, 
                                            fecha_pub_inicio = ?, 
                                            fecha_pub_fin = ?, 
                                            servicio = ?, 
                                            fotos = ? 
                                            WHERE id = ?";
                                    if ($stmt_update = mysqli_prepare($conexion, $sql_update)) {
                                        mysqli_stmt_bind_param($stmt_update, "ssssssssssssi", $titulo, $descripcion, $ubicacion, $etiquetas, $costo_alquiler, $tiempo_minimo, $tiempo_maximo, $cupo, $fecha_inicio, $fecha_fin, $servicios_json, $galeria_imagenes_json, $id_publicacion);
                                        if (mysqli_stmt_execute($stmt_update)) {
                                            echo '<div class="container mt-4">';
                                            echo '<div class="alert alert-success text-center" role="alert">La oferta se ha actualizado exitosamente.</div>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="container mt-4">';
                                            echo '<div class="alert alert-danger" role="alert">Error al actualizar la oferta: ' . mysqli_error($conexion) . '</div>';
                                            echo '</div>';
                                        }
                                    }
                                } else {
                                    $sql_update = "UPDATE publicacion SET 
                                            titulo = ?, 
                                            descripcion = ?, 
                                            ubicacion = ?, 
                                            etiqueta = ?, 
                                            costo = ?, 
                                            tiempo_minimo = ?, 
                                            tiempo_maximo = ?, 
                                            cupo = ?, 
                                            fecha_pub_inicio = ?, 
                                            fecha_pub_fin = ?, 
                                            servicio = ? 
                                            WHERE id = ?";
                                    if ($stmt_update = mysqli_prepare($conexion, $sql_update)) {
                                        mysqli_stmt_bind_param($stmt_update, "sssssssssssi", $titulo, $descripcion, $ubicacion, $etiquetas, $costo_alquiler, $tiempo_minimo, $tiempo_maximo, $cupo, $fecha_inicio, $fecha_fin, $servicios_json, $id_publicacion);
                                        if (mysqli_stmt_execute($stmt_update)) {
                                            echo '<div class="container mt-4">';
                                            echo '<div class="alert alert-success" role="alert">La oferta se ha actualizado exitosamente.</div>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="container mt-4">';
                                            echo '<div class="alert alert-danger" role="alert">Error al actualizar la oferta: ' . mysqli_error($conexion) . '</div>';
                                            echo '</div>';
                                        }
                                    }
                                }
                            } else {
                                echo '<div class="container mt-4">';
                                echo '<h1>Modificar Publicacion de Alquiler</h1>';
                                echo '<form method="POST" enctype="multipart/form-data">';
                                echo '<div class="form-group">';
                                echo '<label for="titulo">Título</label>';
                                echo '<input type="text" class="form-control" id="titulo" name="titulo" value="' . htmlspecialchars($fila["titulo"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="descripcion">Descripción</label>';
                                echo '<textarea class="form-control" id="descripcion" name="descripcion" required>' . htmlspecialchars($fila["descripcion"]) . '</textarea>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="ubicacion">Ubicación</label>';
                                echo '<input type="text" class="form-control" id="ubicacion" name="ubicacion" value="' . htmlspecialchars($fila["ubicacion"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="etiquetas">Etiquetas</label>';
                                echo '<input type="text" class="form-control" id="etiqueta" name="etiqueta" value="' . htmlspecialchars($fila["etiqueta"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="costo_alquiler">Costo de Alquiler por Día</label>';
                                echo '<input type="number" class="form-control" id="costo_alquiler" name="costo" value="' . htmlspecialchars($fila["costo"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="tiempo_minimo">Tiempo Mínimo de Permanencia (días)</label>';
                                echo '<input type="number" class="form-control" id="tiempo_minimo" name="tiempo_minimo" value="' . htmlspecialchars($fila["tiempo_minimo"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="tiempo_maximo">Tiempo Máximo de Permanencia (días)</label>';
                                echo '<input type="number" class="form-control" id="tiempo_maximo" name="tiempo_maximo" value="' . htmlspecialchars($fila["tiempo_maximo"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="cupo">Cupo</label>';
                                echo '<input type="number" class="form-control" id="cupo" name="cupo" value="' . htmlspecialchars($fila["cupo"]) . '" required>';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="fecha_pub_inicio">Fecha de Inicio</label>';
                                echo '<input type="date" class="form-control" id="fecha_pub_inicio" name="fecha_pub_inicio" value="' . htmlspecialchars($fila["fecha_pub_inicio"]) . '" min="' . $hoy . '">';
                                echo '</div>';
                                echo '<div class="form-group">';
                                echo '<label for="fecha_pub_fin">Fecha de Finalización</label>';
                                echo '<input type="date" class="form-control" id="fecha_pub_fin" name="fecha_pub_fin" value="' . htmlspecialchars($fila["fecha_pub_fin"]) . '" min="' . $hoy . '">';
                                echo '</div>';
                                echo '<br>';
                                // Campo para modificar servicios
                                echo '<div class="form-group">';
                                echo '<label for="servicio">Servicios incluidos:</label>';
                                $servicios_incluidos = json_decode($fila['servicio'], true);
                                if (!is_array($servicios_incluidos)) {
                                    $servicios_incluidos = [];
                                }
                                $servicios = ["Cocina", "Piscina", "Spa", "Aire acondicionado", "Limpieza", "Internet", "Desayuno", "Merienda" , "Cena" , "Patio" , "Camaras de seguridad" , "Agua" ,"Garage", "Luz" ,"Baño" ,"Calefaccion"];
                                foreach ($servicios as $servicio) {
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" name="servicio[]" value="' . $servicio . '"' . (in_array($servicio, $servicios_incluidos) ? ' checked' : '') . '>';
                                    echo '<label class="form-check-label" for="' . $servicio . '">' . $servicio . '</label>';
                                    echo '<br>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                echo '<br>';
                                // Campo para modificar fotos
                                echo '<div class="form-group mb-4">';
                                echo '<label for="fotos">Fotos del alquiler:</label>';
                                echo '<input type="file" class="form-control" id="fotos" name="fotos[]" multiple>'; // Aceptar múltiples archivos
                                echo '</div>';

                                echo '<div class="text-center"><button type="submit" class="btn btn-dark">Guardar Cambios</button></div>';
                                echo '</form>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="container mt-4">';
                            echo '<div class="alert alert-danger" role="alert">No tienes permiso para modificar esta oferta.</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="container mt-4">';
                        echo '<div class="alert alert-danger" role="alert">Oferta no encontrada.</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="container mt-4">';
                    echo '<div class="alert alert-danger" role="alert">Error en la consulta: ' . mysqli_error($conexion) . '</div>';
                    echo '</div>';
                }
                mysqli_stmt_close($stmt);
            }
        
        }else {
            echo '<div class="container mt-4">';
            echo '<div class="alert alert-danger" role="alert">ID de oferta no válido.</div>';
            echo '</div>';
        }


        include './pie.php';
    ?>

</body>
</html>