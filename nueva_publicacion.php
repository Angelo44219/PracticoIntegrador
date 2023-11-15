<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva publicacion</title>
</head>
<body>
    <?php
        require_once './BD/conexion.php';
        include './cabecera.php';
        

        function verificarExtensionImagen($img) {
            $formatosPermitidos = ['jpg', 'jpeg', 'png', 'avif', 'webp'];
    
            foreach ($img["name"] as $nombre) {
                $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                if (!in_array($extension, $formatosPermitidos)) {
                    return false;
                }
            }
            return true;
        }

        $fecha_actual = date("Y-m-d");

        if (!isset($_SESSION["id"])) {
            header("Location: iniciar_sesion.php");
            exit();
        }

        if($_SERVER["REQUEST_METHOD"]=='POST'){
            if(!verificarExtensionImagen($_FILES['fotos'])){
                echo '<div class="alert alert-danger text-center" role="alert">Por favor, sube imágenes en formatos válidos: jpg, jpeg, png, avif o webp.</div>';
            }else{
                $titulo = $_POST["titulo"];
                $descripcion = $_POST["descripcion"];
                $ubicacion = $_POST["ubicacion"];
                $etiquetas = $_POST["etiqueta"];
                $costo_alquiler = $_POST["costo"];
                $tiempo_minimo = $_POST["tiempo_minimo"];
                $tiempo_maximo = $_POST["tiempo_maximo"];
                $cupo = $_POST["cupo"];
                $fecha_pub_inicio = $_POST["fecha_pub_inicio"];
                $fecha_pub_fin = $_POST["fecha_pub_fin"];

                $id_usuario = $_SESSION["id"];

                $galeria_imagenes= array();

                if (!empty($_FILES["fotos"]["name"][0])) {
                    $total = count($_FILES["fotos"]["name"]);
                    for ($i = 0; $i < $total; $i++) {
                        $nombre_archivo = $_FILES["fotos"]["name"][$i];
                        $ruta_temporal = $_FILES["fotos"]["tmp_name"][$i];
                        $ruta_archivo = "galeria/" . uniqid() . "_" . $nombre_archivo;

                        if (move_uploaded_file($ruta_temporal, $ruta_archivo)) {
                            $galeria_imagenes[] = $ruta_archivo;
                        }
                    }
                }

                $galeria_json=json_encode($galeria_imagenes);

                $consulta_verificado = "SELECT certificacion FROM usuario WHERE id = $id_usuario";
                $resultado_verificado = mysqli_query($conexion, $consulta_verificado);
                $usuario = mysqli_fetch_assoc($resultado_verificado);

                
                if ($usuario['certificacion'] == 0) {
                    $consulta_oferta_activa = "SELECT COUNT(*) as total FROM publicacion WHERE id_usuario= $id_usuario AND estado = 1";
                    $resultado_oferta_activa = mysqli_query($conexion, $consulta_oferta_activa);
                    $oferta_activa = mysqli_fetch_assoc($resultado_oferta_activa);

                    if ($oferta_activa['total'] > 0) {
                        echo '<div class="alert alert-danger mt-4" role="alert">';
                        echo "<div class='text-center'>Ya tienes una oferta activa. No puedes crear otra hasta que la oferta actual expire o la desactives.</div>";
                        echo '</div>';
                        require_once('./pie.php');
                        exit;
                    }
                    $sql= "INSERT INTO publicacion (id_usuario, titulo, descripcion, ubicacion, etiqueta, fotos, costo, tiempo_minimo, tiempo_maximo, cupo, fecha_pub_inicio, fecha_pub_fin, estado) 
                    VALUES ('$id_usuario', '$titulo', '$descripcion', '$ubicacion', '$etiquetas', '$galeria_json','$costo_alquiler','$tiempo_minimo', '$tiempo_maximo', '$cupo', '$fecha_pub_inicio', '$fecha_pub_fin', 0)";
                }else {
                    $sql= "INSERT INTO publicacion (id_usuario, titulo, descripcion, ubicacion, etiqueta, fotos, costo, tiempo_minimo, tiempo_maximo, cupo, fecha_pub_inicio, fecha_pub_fin, estado) 
                    VALUES ('$id_usuario', '$titulo', '$descripcion', '$ubicacion', '$etiquetas', '$galeria_json','$costo_alquiler','$tiempo_minimo', '$tiempo_maximo', '$cupo', '$fecha_pub_inicio', '$fecha_pub_fin', 1)";
                }

                if (mysqli_query($conexion, $sql)) {
                    $id_publicacion = mysqli_insert_id($conexion);
                    $servicios_seleccionados = isset($_POST["servicios"]) ? $_POST["servicios"] : [];
                    $servicios_json = json_encode($servicios_seleccionados);
    
                    $sql = "UPDATE publicacion SET servicio = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($conexion, $sql)) {
                        mysqli_stmt_bind_param($stmt, "si", $servicios_json, $id_publicacion);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
    
                    echo '<div class="alert alert-success text-center" role="alert">La oferta se ha creado exitosamente.</div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error al crear la oferta: ' . mysqli_error($conexion) . '</div>';
                }
            }
        }

        
    ?>
    <div class="container mt-4">
        <h1 class="text">Crear una nueva publicacion</h1>
        <br>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Titulo: </label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <br> 
            <div class="form-group">
                <label for="descripcion">Descripcion: </label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            <br>
            <div class="form-group">
                <label for="ubicacion">Ubicacion: </label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
            </div>
            <br> 
            <div class="form-group">
                <label for="etiqueta">Etiquetas (Separadas por comas):</label>
                <input type="text" class="form-control" id="etiqueta" name="etiqueta" required>
            </div>
            <br>
            <div class="form-group">
                <label for="fotos">Fotos: </label>
                <input type="file" class="form-control-file" id="fotos" name="fotos[]" accept="image/jpeg, image/png, image/avif, image/webp, image/jpg" multiple required>
            </div>
            <br>
            <div class="form-group">
                <label for="costo">Costo de alquiler por dia: </label>
                <input type="number" min="1" class="form-control" id="costo" name="costo" required>
            </div>
            <br>
            <div class="form-group">
                <label for="tiempo_minimo">Tiempo minimo de permanencia (dias):</label>
                <input type="number" min="1" class="form-control" id="tiempo_minimo" name="tiempo_minimo" required>
            </div>
            <br>
            <div class="form-group">
                <label for="tiempo_maximo">Tiempo maximo de permanencia (dias):</label>
                <input type="number" min="1" class="form-control" id="tiempo_maximo" name="tiempo_maximo" required>
            </div>
            <br>
            <div class="form-group">
                <label for="cupo">Cupo:</label>
                <input type="number" min="1 class="form-control" id="cupo" name="cupo" required>
            </div>
            <br>
            <div class="form-group">
                <label for="fecha_pub_inicio">Fecha de Inicio (opcional):</label>
                <input type="date" class="form-control" id="fecha_pub_inicio" name="fecha_pub_inicio" min="<?php echo $fecha_actual; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="fecha_pub_fin">Fecha de Inicio (opcional):</label>
                <input type="date" class="form-control" id="fecha_pub_fin" name="fecha_pub_fin" min="<?php echo $fecha_actual; ?>">
            </div>
            <br>
            <div class="form-group">
                <label>Servicios</label><br><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="cocina" name="servicios[]" value="Cocina">
                        <label class="form-check-label" for="cocina">Cocina</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="piscina" name="servicios[]" value="Piscina">
                        <label class="form-check-label" for="piscina">Piscina</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="spa" name="servicios[]" value="Patio">
                        <label class="form-check-label" for="patio">Patio</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="aire_acondicionado" name="servicios[]" value="Aire acondicionado">
                        <label class="form-check-label" for="aire_acondicionado">Aire acondicionado</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="limpieza" name="servicios[]" value="Limpieza">
                        <label class="form-check-label" for="limpieza">Limpieza</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="Internet" name="servicios[]" value="Internet">
                        <label class="form-check-label" for="internet">Internet</label>
                    </div>
                    
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="agua" name="servicios[]" value="Agua">
                        <label class="form-check-label" for="agua">Agua</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="calefaccion" name="servicios[]" value="Calefaccion">
                        <label class="form-check-label" for="calefaccion">Calefaccion</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="camaras" name="servicios[]" value="Camaras de seguridad">
                        <label class="form-check-label" for="camaras">Camaras de seguridad</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="baño" name="servicios[]" value="Baño">
                        <label class="form-check-label" for="baño">Baño</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="baño" name="servicios[]" value="Baño">
                        <label class="form-check-label" for="baño">Living</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="baño" name="servicios[]" value="Baño">
                        <label class="form-check-label" for="baño">Comedor</label>
                    </div>
                    
                    <br><br>
                    <div class="text-center">
                        <button type="submit" class="btn btn-dark btn-create-offer btn-block">Crear Oferta</button>
                    </div>
            </div>  
        </form>
    </div>
    <?php include './pie.php'?>
</body>
</html>