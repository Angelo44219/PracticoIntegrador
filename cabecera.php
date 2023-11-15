<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapiBnB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Lora:ital@0;1&family=Noto+Sans+Osmanya&family=Raleway:ital,wght@0,100;0,300;0,400;0,500;1,100&display=swap" rel="stylesheet">
</head>
<body>
    
    <main class="sticky-top">
            <nav class="navbar navbar-expand-lg navbar-dark p-3 header sticky-top">
                <div class="container-fluid">
                    <a class="navbar-brand logo" href="./Index.php"><i class="fa-solid fa-house-chimney"></i> RapiBnB</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
            

                    <div class=" collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ms-auto">

                        <a class="nav-link" href="./Buscador.php">
                            <i class="fa-solid fa-magnifying-glass"></i> Buscar alquiler
                        </a>
                        <?php

                            if (session_status() === PHP_SESSION_NONE) {
                                session_start(); // Inicia la sesión si no se encuentra activa
                            }
    
                            // Botón "Crear Ofertas de Alquiler" con icono de "+"
                            if (isset($_SESSION["id"])) {
                                echo '<li class="nav-item"><a class="nav-link" href="nueva_publicacion.php"><i class="fa-solid fa-plus"></i> Publicar oferta</a></li>';
                            }

                            
                        ?>
                        <?php
                                // Botón "Admin" con icono de una tuerca
                                if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
                                    echo '<li class="nav-item"><a class="nav-link" href="index_administrador.php"><i class="fa-solid fa-gears"></i> Pantalla de Administrador</a></li>';
                                }

                                // Botón de usuario (person) en el lado derecho
                                if (isset($_SESSION["id"])) {
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="perfil_usuario.php"><i class="fa-solid fa-user"></i> Mi Perfil</a>
                                    <a class="dropdown-item" href="editar_perfil.php"><i class="fa-solid fa-user-pen"></i> Editar Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="cerrar_sesion.php"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</a>
                                </div>
                            </li>
                            <?php
                                }else{
                                    echo '<li class="nav-item"><a class="nav-link" href="./iniciar_sesion.php"> <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a></li>';
                                    echo '<li class="nav-item"><a class="nav-link" href="./registrarse.php"> <i class="fa-solid fa-address-card"></i> Registrarse</a></li>';
                                }
                            ?>                      
                        </ul>
                    </div>
                </div>
            </nav>
            <?php
                require_once './BD/conexion.php';

                $fecha_actual = date("Y-m-d");
                $query = "UPDATE usuario SET certificacion = 0, fecha_vencimiento = NULL WHERE fecha_vencimiento = ?";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param("s", $fecha_actual);
                $stmt->execute();
                $stmt->close();
                
                // Actualizar el estado de los alquileres que no están dentro del rango de fechas a inactivos
                $sql = "UPDATE publicacion SET estado = 0 WHERE (CURDATE() NOT BETWEEN fecha_pub_inicio AND fecha_pub_fin) AND fecha_pub_inicio != '0000-00-00' AND fecha_pub_fin != '0000-00-00'";
                if (!mysqli_query($conexion, $sql)) {
                    echo "Error al actualizar alquileres fuera de rango: " . mysqli_error($conexion);
                }
                
                // Desactivar las ofertas de alquiler adicionales de usuarios regulares si ya tienen una oferta activa
                $sql_desactivar = "UPDATE publicacion p1 
                           INNER JOIN (
                               SELECT id_usuario, MAX(fecha_pub_inicio) as latest_start 
                               FROM publicacion 
                               WHERE estado = 1 
                               GROUP BY id_usuario 
                               HAVING COUNT(id) > 1
                           ) p2 
                           ON p1.id_usuario = p2.id_usuario 
                           SET p1.estado = 0 
                           WHERE p1.fecha_pub_inicio != p2.latest_start";
        
                if (!mysqli_query($conexion, $sql_desactivar)) {
                    echo "Error al desactivar ofertas adicionales de usuarios regulares: " . mysqli_error($conexion);
                }

                // Activar las ofertas de alquiler de usuarios verificados que están dentro del rango de fechas
                $sql = "UPDATE publicacion p INNER JOIN usuario u ON p.id_usuario = u.id SET p.estado = 1 WHERE u.certificacion = 1 AND CURDATE() BETWEEN p.fecha_pub_inicio AND p.fecha_pub_fin";
                if (!mysqli_query($conexion, $sql)) {
                    echo "Error al actualizar alquileres dentro de rango para usuarios verificados: " . mysqli_error($conexion);
                }
                
                // Activar las ofertas de alquiler de usuarios no verificados que han sido publicadas hace más de 3 días hábiles y están dentro del rango de fechas
                $fecha_hace_tres_dias = date('Y-m-d', strtotime("-3 weekdays"));
                $sql = "UPDATE publicacion p INNER JOIN usuario u ON p.id_usuario = u.id 
                    SET p.estado = 1 
                    WHERE u.certificacion = 0 
                    AND p.estado = 0 
                    AND DATEDIFF(CURDATE(), p.fecha_subida) >= 3 
                    AND CURDATE() BETWEEN p.fecha_pub_inicio AND p.fecha_pub_fin";
                
                $stmt = $conexion->prepare($sql);
                $stmt->execute();
                $stmt->close();

                // Eliminar las solicitudes de alquiler pendientes que han estado en ese estado durante más de 72 horas
                $fecha_hace_tres_dias = date('Y-m-d H:i:s', strtotime("-3 days"));
                $sql_eliminar = "DELETE FROM alquiler WHERE estado_alquiler = 'pendiente' AND fecha_aplicacion <= ?";
                $stmt_eliminar = $conexion->prepare($sql_eliminar);
                $stmt_eliminar->bind_param("s", $fecha_hace_tres_dias);
                $stmt_eliminar->execute();
                $stmt_eliminar->close();
                
                // Activar las ofertas de alquiler que no tienen fecha o tienen la fecha '0000-00-00' y pertenecen a usuarios verificados
                $sql = "UPDATE publicacion p INNER JOIN usuario u ON p.id_usuario = u.id SET p.estado = 1 WHERE u.certificacion = 1 AND (p.fecha_pub_inicio = '0000-00-00' OR p.fecha_pub_fin = '0000-00-00')";
                if (!mysqli_query($conexion, $sql)) {
                    echo "Error al actualizar alquileres sin fecha o con fechas '0000-00-00' de usuarios verificados: " . mysqli_error($conexion);
                }

                // Activar las ofertas de alquiler que no tienen fecha o tienen la fecha '0000-00-00' en usuarios regulares
                $sql = "UPDATE publicacion p 
                INNER JOIN usuario u ON p.id_usuario = u.id
                SET p.estado = 1 
                WHERE (p.fecha_pub_inicio = '0000-00-00' OR p.fecha_pub_fin = '0000-00-00') 
                AND DATEDIFF(CURDATE(), p.fecha_subida) >= 3
                AND u.certificacion = 0
                AND NOT EXISTS (
                    SELECT 1 FROM publicacion b 
                    WHERE b.id_usuario = p.id_usuario AND b.estado = 1 AND b.id != p.id
                )";
                if (!mysqli_query($conexion, $sql)) {
                    echo "Error al actualizar alquileres sin fecha o con fechas '0000-00-00': " . mysqli_error($conexion);
                }
            ?>        
    </main>
    <!--[Scripts JS y demas]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>