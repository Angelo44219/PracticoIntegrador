<?php
    require_once './BD/conexion.php';

    // Definir la cantidad de alquileres por página
    $alquileresPorPagina = 4;

    // Obtener la página actual
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    // Calcular el inicio de la consulta para la paginación
    $inicioConsulta = ($paginaActual - 1) * $alquileresPorPagina;

    // Consulta SQL para obtener el alquiler destacado
    $sqlDestacado = "SELECT p.*, u.nombre, u.apellido, u.foto 
                    FROM publicacion p
                    INNER JOIN usuario u ON p.id_usuario = u.id
                    WHERE p.estado= 1 AND u.certificacion = 1
                    ORDER BY RAND()
                    LIMIT 1, 2";
    $resultadoDestacado = mysqli_query($conexion, $sqlDestacado);
    $alquilerDestacado = mysqli_fetch_assoc($resultadoDestacado);

    // Consulta SQL para obtener el alquiler recomendado
    $Recomendados = "SELECT p.*, u.nombre, u.apellido, u.foto, AVG(r.puntuacion) as promedio
                    FROM publicacion p
                    INNER JOIN usuario u ON p.id_usuario = u.id
                    LEFT JOIN resena r ON p.id = r.id_publicacion
                    WHERE p.estado = 1
                    GROUP BY p.id
                    HAVING promedio >= 4.0
                    ORDER BY RAND()
                    LIMIT 1";
    $resultadoRecomendado = mysqli_query($conexion, $Recomendados);
    $alquilerRecomendado = mysqli_fetch_assoc($resultadoRecomendado);

    // Consulta SQL para obtener los alquileres ordenados por fecha
    $sql = "SELECT p.*, u.nombre, u.apellido, u.foto 
    FROM publicacion p
    INNER JOIN usuario u ON p.id_usuario = u.id
    WHERE p.estado = 1
    ORDER BY p.fecha_subida DESC
    LIMIT ?, ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $inicioConsulta, $alquileresPorPagina);
    mysqli_stmt_execute($stmt);
    $resultados = mysqli_stmt_get_result($stmt);

    echo '<div class="container">';
        echo '<h1 class="titulo">Últimos alquileres publicados</h1>';
        echo '<br>';
        
        echo '<div class="row row-cols-1 row-cols-md-3 g-4 mb-5">';
            // Función para mostrar alquiler
            function mostrarAlquiler($fila, $badge = null) {
                $titulo = $fila['titulo'];
                $descripcion = $fila['descripcion'];
                $ubicacion = $fila['ubicacion'];
                $etiquetas = explode(',', $fila["etiqueta"]);
                $nombreUsuario = $fila['nombre'] . ' ' . $fila['apellido'];
                $galeriaFotos = json_decode($fila['fotos']);
                $fotoperfil=$fila['foto'];
                $idAlquiler = $fila['id'];

                echo '<div class="col">';
                 echo '<div class="card h-100 shadow-sm text-center publicacion">';
                        if ($badge) {
                                echo '<span class="badge ' . $badge . ' position-absolute top-0 end-0 mt-2 me-2">' . ucfirst(str_replace('badge-', '', $badge)) . '</span>';
                        }

                        echo '<img src="'.json_decode($fila['fotos'])[0].'" class="card-img-top imagen_publicacion" height="220px">';

                        echo '<div class="card-body">';
                            echo '<h4 class="card-title">' . $titulo . '</h4>';
                            echo '<p class="card-text text-clamp texto">' . $descripcion . '</p>';
                            echo '<p class="card-text"><strong>Ubicación:</strong> ' . $ubicacion . '</p>';
                            echo '<p><strong>Etiquetas:</strong> ';
                                foreach ($etiquetas as $q) {
                                    $q = trim($q);
                                    echo '<a href="Buscador.php?q=' . urlencode($q) . '" class="q"><span class="badge rounded-pill bg-primary">' . htmlspecialchars($q) . '</span> </a> ';
                                }
                                echo '</p>';
                            echo '<p class="card-text"><img src="'.$fotoperfil.'" class="img-fluid" style="width: 45px; height: 45px; border-radius: 50%; margin-right: 10px; object-fit:cover;">' . $nombreUsuario . '</p>';
                            
                            echo '<a href="detalles_publicacion.php?id=' . $idAlquiler . '" class="btn btn-primary boton">Ver alquiler</a>';
                        echo '</div>';
                 echo '</div>';
                echo '</div>';
                
            }

            // Mostrar alquiler destacado
            if ($alquilerDestacado) {
                mostrarAlquiler($alquilerDestacado, 'badge-destacado');
            }

            // Mostrar alquiler recomendado
            if ($alquilerRecomendado) {
                mostrarAlquiler($alquilerRecomendado, 'badge-recomendado');
            }

            // Mostrar los demás alquileres
            while ($fila = mysqli_fetch_assoc($resultados)) {
                mostrarAlquiler($fila);
            }

        echo '</div>';


        // Calcular la cantidad total de páginas
        $sqlTotal = "SELECT COUNT(*) as total FROM publicacion WHERE estado = 1";
        $resultTotal = mysqli_query($conexion, $sqlTotal);
        $filaTotal = mysqli_fetch_assoc($resultTotal);
        $totalAlquileres = $filaTotal['total'];
        $totalPaginas = ceil($totalAlquileres / $alquileresPorPagina);

        // Mostrar la paginación
        echo '<nav aria-label="Navegación de páginas">';
            echo '<ul class="pagination justify-content-center">';
            for ($i = 1; $i <= $totalPaginas; $i++) {
                echo '<li class="page-item';
                if ($i == $paginaActual) {
                    echo ' active';
                }
                echo '"><a class="page-link" href="Index.php?pagina=' . $i . '">' . $i . '</a></li>';
            }
            echo '</ul>';
        echo '</nav>';
    echo '</div>';

    require_once './BD/cerrar_conexion.php';
?>