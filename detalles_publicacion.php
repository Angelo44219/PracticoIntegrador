<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Alquiler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../Estilos/Estilo.css" type="text/css">
</head>
<body>

<?php
require_once('./BD/conexion.php');
require_once('./cabecera.php');
$id_publicacion = null;

// Obtenemos el ID del alquiler desde el parámetro GET
$id_alquiler = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(isset($_SESSION['id'])) {
	$id_usuario = $_SESSION['id'];
	// Consultamos si el usuario está verificado, si tiene una oferta activa y las fechas de inicio y fin del alquiler
	$query = "SELECT u.certificacion, p.fecha_subida, p.fecha_pub_inicio, p.fecha_pub_fin, p.estado 
			FROM publicacion p 
			JOIN usuario u ON p.id_usuario = u.id 
			WHERE p.id = $id_alquiler";
	
	$resultado = mysqli_query($conexion, $query);
	
	if ($resultado && mysqli_num_rows($resultado) > 0) {
		$fila = mysqli_fetch_assoc($resultado);
		
		// Si el usuario es regular
		if ($fila['certificacion'] == 0) {
			// Verificamos si el usuario ya tiene una oferta activa
			$queryOfertaActiva = "SELECT COUNT(*) as total_activas FROM publicacion WHERE id_usuario = $id_usuario AND estado = 1";
			$resultadoOfertaActiva = mysqli_query($conexion, $queryOfertaActiva);
			$filaOfertaActiva = mysqli_fetch_assoc($resultadoOfertaActiva);
			
			if ($filaOfertaActiva['total_activas'] > 0 && $fila['estado'] == 0) {
				echo "<div class='alert alert-danger text-center'>La oferta de alquiler está inactiva porque ya tienes una oferta de alquiler activa.</div>";
			}
		}
		
		$fecha_publicacion = new DateTime($fila['fecha_subida']);
		$fecha_actual = new DateTime();
		$diferencia = $fecha_actual->diff($fecha_publicacion);
		
		$fecha_inicio = isset($fila['fecha_inicio']) ? new DateTime($fila['fecha_inicio']) : null;
		$fecha_fin = isset($fila['fecha_fin']) ? new DateTime($fila['fecha_fin']) : null;
	
		if ($diferencia->days < 3 && $fila['certificacion'] == 0) {
			echo "<div class='alert alert-warning text-center'>Tu alquiler está inactivo porque aún no han pasado 3 días desde su fecha de publicación.</div>";
		} elseif ($fila['estado'] == 0 && $fecha_inicio && $fecha_fin && ($fecha_actual < $fecha_inicio || $fecha_actual > $fecha_fin)) {
		echo "<div class='alert alert-secondary text-center'>Tu oferta de alquiler está inactiva porque su rango de actividad no coincide con el de hoy.</div>";
	}
	} else {
		echo "Error al obtener la información del alquiler o del usuario: " . mysqli_error($conexion);
	}
}
// Función para mostrar las estrellas
function Estrellas($puntuacion) {
    $estrellas = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $puntuacion) {
            $estrellas .= '<box-icon type="solid"   name="star" color="yellow"></box-icon>';
        } else {
            $estrellas .= '<box-icon type="regular" name="star" color="yellow"></box-icon>';
        }
    }
    return $estrellas;
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id_publicacion = $_GET["id"];
    $yaHaResenado = false;

    // Verificar si el usuario ya ha realizado una reseña en esta oferta
    $sql_verificar_resena = "SELECT COUNT(*) FROM resena r 
    INNER JOIN usuario ON r.id_usuario=usuario.id
    WHERE id_publicacion = ? AND id_usuario = ? AND usuario.certificacion=1";
    if ($stmt_verificar_resena = mysqli_prepare($conexion, $sql_verificar_resena)) {
        mysqli_stmt_bind_param($stmt_verificar_resena, "ii", $id_publicacion, $_SESSION['id']);
        if (mysqli_stmt_execute($stmt_verificar_resena)) {
            mysqli_stmt_bind_result($stmt_verificar_resena, $num_resenas);
            mysqli_stmt_fetch($stmt_verificar_resena);
            mysqli_stmt_close($stmt_verificar_resena);

            if ($num_resenas > 0) {
                // El usuario ya ha realizado una reseña, mostrar un mensaje
                $yaHaResenado = true;
            }
        }
    }


    // Verificar si el usuario ya ha realizado una reseña en esta oferta y si está verificado
    $sql_verificar_resena_y_usuario = "SELECT COUNT(resena.id) 
    FROM resena 
    INNER JOIN usuario ON resena.id_usuario = usuario.id 
    WHERE resena.id_publicacion = ? AND resena.id_usuario = ? AND usuario.certificacion = 1";

    $yaHaResenado = false;
    $puedeResenar = false;
    if ($stmt_verificar_resena_y_usuario = mysqli_prepare($conexion, $sql_verificar_resena_y_usuario)) {
        mysqli_stmt_bind_param($stmt_verificar_resena_y_usuario, "ii", $id_publicacion, $_SESSION['id']);
        if (mysqli_stmt_execute($stmt_verificar_resena_y_usuario)) {
            mysqli_stmt_bind_result($stmt_verificar_resena_y_usuario, $num_resenas);
            mysqli_stmt_fetch($stmt_verificar_resena_y_usuario);
            if ($num_resenas > 0) {
                // El usuario ya ha realizado una reseña, mostrar un mensaje
                $yaHaResenado = true;
            }
            mysqli_stmt_close($stmt_verificar_resena_y_usuario);
        }
    }



	// Verificar si el usuario puede dejar una reseña basado en la fecha de finalización y si está verificado
    if (!$yaHaResenado) {
        $query_puede_resenar = "SELECT COUNT(*) 
            FROM alquiler 
            INNER JOIN usuario ON alquiler.id_usuario = usuario.id 
            WHERE alquiler.id_usuario = ? AND alquiler.id_publicacion = ? 
            AND alquiler.fecha_fin <= CURDATE() AND usuario.certificacion = 1";

        if ($stmt_puede_resenar = mysqli_prepare($conexion, $query_puede_resenar)) {
            mysqli_stmt_bind_param($stmt_puede_resenar, "ii", $_SESSION['id'], $id_publicacion);
            if (mysqli_stmt_execute($stmt_puede_resenar)) {
                mysqli_stmt_bind_result($stmt_puede_resenar, $num_aplicaciones);
                mysqli_stmt_fetch($stmt_puede_resenar);
                // Si hay al menos una aplicación que cumpla las condiciones, el usuario puede dejar una reseña
                $puedeResenar = ($num_aplicaciones > 0);
                mysqli_stmt_close($stmt_puede_resenar);
            }
        }
    }

    // Si se presiona el botón "Eliminar Oferta"
    if (isset($_GET["action"]) && $_GET["action"] == "delete") {
        $sql_delete_offer = "DELETE FROM publicacion WHERE id = ?";
        if ($stmt_delete_offer = mysqli_prepare($conexion, $sql_delete_offer)) {
            mysqli_stmt_bind_param($stmt_delete_offer, "i", $id_publicacion);
            if (mysqli_stmt_execute($stmt_delete_offer)) {
                echo '<script>window.location.href = "Index.php";</script>';
                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al eliminar la oferta: ' . mysqli_error($conexion) . '</div>';
            }
            mysqli_stmt_close($stmt_delete_offer);
        }
    }

	// Código para calcular la puntuación general
    $sql_puntuacion = "SELECT AVG(puntuacion) as promedio FROM resena WHERE id_publicacion = ?";
    $puntuacion_general = 0;
    if ($stmt_puntuacion = mysqli_prepare($conexion, $sql_puntuacion)) {
        mysqli_stmt_bind_param($stmt_puntuacion, "i", $id_publicacion);
        if (mysqli_stmt_execute($stmt_puntuacion)) {
            $resultado_puntuacion = mysqli_stmt_get_result($stmt_puntuacion);
            $fila_puntuacion = mysqli_fetch_assoc($resultado_puntuacion);
            $puntuacion_general = round($fila_puntuacion['promedio']);
        }
        mysqli_stmt_close($stmt_puntuacion);
    }

    // Código para mostrar detalles de la oferta y reseñas
    $sql = "SELECT p.*, u.id AS id_usuario FROM publicacion p
            INNER JOIN usuario u ON p.id_usuario = u.id
            WHERE p.id = ?";
    if ($stmt = mysqli_prepare($conexion, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_publicacion);
        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($resultado) == 1) {
                $fila = mysqli_fetch_assoc($resultado);

                $esPropietario = false;
                if (isset($_SESSION['id']) && $_SESSION['id'] == $fila['id_usuario']) {
                    $esPropietario = true;
                }

				// Verificar si la oferta está inactiva y el usuario no es el propietario
				if ($fila['estado'] == 0 && !$esPropietario) {
					echo '<div class="container mt-4">';
					echo '<div class="alert alert-danger" role="alert">Esta oferta de alquiler a la que intentas acceder está inactiva.</div>';
					echo '</div>';
					include('pie.php');
					exit(); // Termina la ejecución del script aquí
				}

                echo '<div class="container mt-4">';
				echo '<h1>' . htmlspecialchars($fila["titulo"]) . '</h1>';
				echo '<p><strong>Puntuación general:</strong> ' . Estrellas($puntuacion_general) . '</p>'; // Mostrar puntuación general
				echo '<p><strong>Descripción:</strong> ' . htmlspecialchars($fila["descripcion"]) . '</p>';
                echo '<p><strong>Ubicación:</strong> ' . htmlspecialchars($fila["ubicacion"]) . '</p>';
                $etiquetas = explode(',', $fila["etiqueta"]);
				echo '<p><strong>Etiquetas:</strong> ';
				foreach ($etiquetas as $q) {
					$q = trim($q);
					echo '<a href="Buscador.php?q=' . urlencode($q) . '" class="q">#' . htmlspecialchars($q) . '</a> ';
				}
				echo '</p>';
                echo '<p><strong>Costo de Alquiler por Día:</strong> $' . number_format($fila["costo"], 2) . '</p>';
                echo '<p><strong>Tiempo Mínimo de Permanencia:</strong> ' . $fila["tiempo_minimo"] . ' días</p>';
                echo '<p><strong>Tiempo Máximo de Permanencia:</strong> ' . $fila["tiempo_maximo"] . ' días</p>';
                echo '<p><strong>Cupo de Personas:</strong> ' . $fila["cupo"] . '</p>';
                echo '<p><b>Fecha de inicio :</b> ' . $fila["fecha_pub_inicio"] . '</p>';
                echo '<p><b>Fecha de fin:</b> ' . $fila["fecha_pub_fin"] . '</p>';
                echo '<p><b>Publicado el:</b> ' . $fila["fecha_subida"] . '</p>';
				// Mostrar servicios incluidos
                $servicios_incluidos = json_decode($fila['servicio'], true);
                echo '<h3>Servicios incluidos:</h3>';
                echo '<ul>';
                if (!empty($servicios_incluidos)) {
                    foreach ($servicios_incluidos as $servicio) {
                        echo "<li>" . htmlspecialchars($servicio) . "</li>";
                    }
                } else {
                    echo "<li>No hay servicios incluidos.</li>";
                }
                echo '</ul>';

				// Verificar si el usuario actual es el propietario de la oferta
				if ($esPropietario) {
					// Obtener las solicitudes pendientes relacionadas con este alquiler
					$query_solicitudes = "SELECT alquiler.id, usuario.nombre AS nombre_solicitante,alquiler.fecha_aplicacion, alquiler.fecha_inicio, alquiler.fecha_fin FROM alquiler 
										INNER JOIN usuario ON alquiler.id_usuario = usuario.id 
										WHERE alquiler.id_publicacion = ? AND alquiler.estado_alquiler = 'pendiente'";
					$stmt_solicitudes = $conexion->prepare($query_solicitudes);
					$stmt_solicitudes->bind_param("i", $id_publicacion);
					$stmt_solicitudes->execute();
					$result_solicitudes = $stmt_solicitudes->get_result();
				
					echo "<h2>Solicitudes pendientes</h2>";
				
					if ($result_solicitudes->num_rows > 0) {
						echo "<table class='table'>";
						echo "<thead><tr><th>Nombre del solicitante</th><th>Fecha de solicitud</th><th>Fecha de ingreso</th><th>Fecha de salida</th><th>Acciones</th></tr></thead>";
						echo "<tbody>";
				
						while ($row_solicitud = $result_solicitudes->fetch_assoc()) {
							echo "<tr>";
							echo "<td>" . htmlspecialchars($row_solicitud['nombre_solicitante']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_aplicacion']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_inicio']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_fin']) . "</td>";
							echo "<td>";
							echo "<a href='solicitud_aceptada.php?id=" . $row_solicitud['id'] . "' class='btn btn-success'>Aceptar</a> ";
							echo "<a href='solicitud_rechazada.php?id=" . $row_solicitud['id'] . "' class='btn btn-danger'>Rechazar</a>";
							echo "</td>";
							echo "</tr>";
						}
				
						echo "</tbody>";
						echo "</table>";
					} else {
						echo "<p>No hay solicitudes pendientes.</p>";
					}
				
					$stmt_solicitudes->close();
				}
				
				
				// Verificar si el usuario actual es el propietario de la oferta
				if ($esPropietario) {
					// Obtener las solicitudes aceptadas relacionadas con este alquiler
					$query_solicitudes = "SELECT alquiler.id, usuario.nombre AS nombre_solicitante, alquiler.fecha_aplicacion, alquiler.fecha_inicio, alquiler.fecha_fin FROM alquiler 
										INNER JOIN usuario ON alquiler.id_usuario = usuario.id 
										WHERE alquiler.id_publicacion = ? AND alquiler.estado_alquiler = 'aceptado'";
					$stmt_solicitudes = $conexion->prepare($query_solicitudes);
					$stmt_solicitudes->bind_param("i", $id_publicacion);
					$stmt_solicitudes->execute();
					$result_solicitudes = $stmt_solicitudes->get_result();
				
					echo "<h2>Solicitudes aceptadas</h2>";
				
					if ($result_solicitudes->num_rows > 0) {
						echo "<table class='table'>";
						echo "<thead class='bg-dark'><tr><th>Nombre del solicitante</th><th>Fecha de solicitud</th><th>Fecha de ingreso</th><th>Fecha de salida</th></tr></thead>";
						echo "<tbody>";
				
						while ($row_solicitud = $result_solicitudes->fetch_assoc()) {
							echo "<tr>";
							echo "<td>" . htmlspecialchars($row_solicitud['nombre_solicitante']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_aplicacion']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_inicio']) . "</td>";
							echo "<td>" . htmlspecialchars($row_solicitud['fecha_fin']) . "</td>";
							echo "</tr>";
						}
				
						echo "</tbody>";
						echo "</table>";
					} else {
						echo "<p>No hay solicitudes pendientes.</p>";
					}
				
					$stmt_solicitudes->close();
				}


                echo '</div>';
                echo '</div>';
				
				

				
				$id_usuario = $_SESSION['id'];
				$alquiler_id = $id_publicacion;

				
				// Verificar si el usuario está logueado y no es el propietario de la oferta
				if (isset($_SESSION['id']) && !$esPropietario) {
					
					// Verificar si el usuario ya ha solicitado una reserva para este alquiler
					$query = "SELECT id, estado_alquiler, fecha_fin FROM alquiler WHERE id_usuario = ? AND id_publicacion = ? AND (estado_alquiler = 'pendiente' OR estado_alquiler = 'aceptado')";
					$stmt_reserva = $conexion->prepare($query);
					$stmt_reserva->bind_param("ii", $id_usuario, $alquiler_id);
					$stmt_reserva->execute();
					$reserva_existente = $stmt_reserva->get_result()->fetch_assoc();
					$stmt_reserva->close();
					
					echo '<div class="mt-4 text-center">';
					
					if (!$reserva_existente) {
						// Muestra el botón "Reservar"
						echo '<a href="reservar_alquiler.php?id=' . $id_publicacion . '" class="btn btn-success">Reservar</a>';
					} else {
						if ($reserva_existente["estado_alquiler"] == "pendiente") {
							echo '<div class="alert alert-warning">Tu alquiler está pendiente de aceptación.</div>';
						} elseif ($reserva_existente["estado_alquiler"] == "aceptado") {
							echo '<div class="alert alert-success">Tu reserva fue aceptada.</div>';
						}
					}
				}
					echo '</div>';


				
                echo '<div class="container mt-4 text-center">';
                echo '<div class="btn-group" role="group" aria-label="Botones">';

                if ($esPropietario) {
                    echo '<a href="modificar_publicacion.php?id=' . $id_publicacion . '" class="btn btn-primary"><i class="fa-solid fa-pencil"></i> Modificar Oferta</a>';
                    echo '<button type="button" class="btn btn-danger eliminar-oferta-button" data-bs-toggle="modal" data-bs-target="#delete'.$id_publicacion.'"><i class="fa-solid fa-trash"></i> Eliminar Oferta</button>';
            
                }

                echo '<a href="perfil_usuario.php?id=' . $fila['id_usuario'] . '" class="btn bg-personalizado text-white"><i class="fa-solid fa-user"></i> Visitar Perfil del Usuario</a>';
                echo '</div>';
                echo '</div>';

                $galeria_imagenes = json_decode($fila["fotos"]);
                if (!empty($galeria_imagenes)) { ?>
                    <div class="container mt-4">
    <h2>Galería de Fotos</h2>
    <div id="fotoCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $primer = true;
            foreach ($galeria_imagenes as $key => $img) {
                echo '<div class="carousel-item';
                if ($primer) {
                    echo ' active';
                    $primer = false;
                }
                echo '">';
                echo '<img src="' . htmlspecialchars($img) . '" class="d-block w-100" alt="Foto">';
                echo '</div>';
            }
            ?>
        </div>
        <a class="carousel-control-prev" href="#fotoCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#fotoCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </a>
    </div>
</div>
                <?php }

                echo '<div id="reseñas" class="container mt-4">';
                echo '<h2>Reseñas</h2>';

                $consultar_resenas= "SELECT r.*, u.nombre, u.foto FROM resena r
                                INNER JOIN usuario u ON r.id_usuario = u.id
                                WHERE r.id_publicacion = ?";
                if ($buscar_resenas = mysqli_prepare($conexion, $consultar_resenas)) {
                    mysqli_stmt_bind_param($buscar_resenas, "i", $id_publicacion);
                    if (mysqli_stmt_execute($buscar_resenas)) {
                        $resultado_resenas = mysqli_stmt_get_result($buscar_resenas);
                        if (mysqli_num_rows($resultado_resenas) > 0) {
                            while ($resenas = mysqli_fetch_assoc($resultado_resenas)) {
                                echo '<div class="card mb-3">';
                                echo '<div class="card-header">';
								echo '<img src="' . htmlspecialchars($resenas["foto"]) . '" alt="Foto de perfil" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">';
                                echo '<strong><a href="perfil_usuario.php?id=' . $resenas["id_usuario"] . '">' . htmlspecialchars($resenas["nombre"]) . '</a></strong>';
                                echo ' - Puntuación: ' . Estrellas($resenas["puntuacion"])."&nbsp"."&nbsp";
                                echo '</div>';
                                echo '<div class="card-body">';
                                echo '<p class="card-text">' . htmlspecialchars($resenas["comentario"]) . '</p>';
                                echo '</div>';
								$sql_respuesta = "SELECT re.*, u.foto, u.nombre, u.apellido FROM respuesta_resena re INNER JOIN usuario u ON re.id_usuario=u.id WHERE re.id_resena = ?";
								if ($stmt_respuesta = mysqli_prepare($conexion, $sql_respuesta)) {
									mysqli_stmt_bind_param($stmt_respuesta, "i", $resenas['id']);
									if (mysqli_stmt_execute($stmt_respuesta)) {
										$resultado_respuesta = mysqli_stmt_get_result($stmt_respuesta);
										if ($fila_respuesta = mysqli_fetch_assoc($resultado_respuesta)) {
                                            echo "<ul style='list style: none;'>
                                                    <li style='list style: none;'>
                                                        <div class='card-header'>
                                                        <img src='" . htmlspecialchars($fila_respuesta["foto"]) . "' alt='Foto de perfil' style='width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;'>
                                                        <strong><a href='perfil_usuario.php?id=". $fila_respuesta['id_usuario'] . "'> " . htmlspecialchars($fila_respuesta["nombre"]) . "</a></strong>
                                                        </div>
                                                        <div class='card-body'>
                                                            <p><strong>Respuesta: </strong>".$fila_respuesta['respuesta']."</p>
                                                        </div>
                                                    </li>
                                                 </ul>";
											
										} else {
											if (isset($_SESSION['id']) && $_SESSION['id'] == $fila['id_usuario']) {
												echo "
												<div class='mt-3 border rounded p-3'>  <!-- Añade estas clases para el estilo de cuadro -->
													<h5>Responder a esta reseña:</h5>
													<form method='POST' class='form-inline' action='./insertar_respuesta.php'>
														<input type='hidden' name='id_resena' value='" . $resenas['id'] . "'>
														<input type='hidden' name='id_usuario' value='" . $_SESSION['id'] . "'>
														<input type='hidden' name='id_publicacion' value='" . $id_publicacion . "'>
														<textarea name='respuesta' placeholder='Escribe tu respuesta...' class='form-control mr-2' rows='2'></textarea>
														<br>
														<div class='text-center'><button type='submit' class='btn btn-primary'>Responder</button></div>
													</form>
													<div id='respuestaDisplay'></div>
												</div>
												";
											}
										}
									}
									mysqli_stmt_close($stmt_respuesta);
								}
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No hay reseñas para esta oferta.</p>';
                        }
                        mysqli_stmt_close($buscar_resenas);
                    }
                }

                if (isset($_SESSION['id']) && !$esPropietario && !$yaHaResenado && $puedeResenar) {
                    echo '<div class="container mt-4">';
                    echo '<h3>Deja tu reseña</h3>';
                        echo '<form method="POST" action="./insertar_resena.php">';
                        echo '<div class="form-group">';
                        echo '<label for="puntuacion">Puntuación (1-5 estrellas):</label>';
                        echo '<input type="number" class="form-control" id="puntuacion" name="puntuacion" min="1" max="5" required>';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label for="comentario">Comentario:</label>';
                        echo '<textarea class="form-control" id="comentario" name="comentario" rows="4" required></textarea>';
                        echo '</div>';
                        echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '">';
                        echo '<input type="hidden" name="id_usuario" value="' . $_SESSION['id'] . '">';
                        echo '<div class="form-group text-center">'; // Centra solo el botón "Publicar Reseña"
                        echo '<button type="submit" class="btn btn-primary">Publicar Reseña</button>';
                        echo '</div>';
                        echo '</form>';
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
} else {
    echo '<div class="container mt-4">';
    echo '<div class="alert alert-danger" role="alert">ID de oferta inválido.</div>';
    echo '</div>';
}

mysqli_close($conexion);
?>

    <?php include './eliminar_publicacion1.php';?>
    
    <?php
        include('./pie.php');
    ?>
    <script src="./js/sweetAlert2.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
