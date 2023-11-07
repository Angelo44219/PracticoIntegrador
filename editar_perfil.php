<?php
    require_once './BD/conexion.php';

    session_start();
    
    if (!isset($_SESSION["id"])) {
        header("location: iniciar_sesion.php");
        exit;
    }
    
    $mensaje = "";
    $mensaje_clase = "";

    $id_usuario = $_SESSION['id']; // Asumiendo que el ID del usuario está almacenado en la sesión
    $sql = "SELECT certificacion FROM usuario WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $esVerificado = $row['certificacion'];
    $stmt->close();

    $sql = "SELECT nombre, apellido, email, foto, biografia, intereses FROM usuario WHERE id = ?";
    if ($stmt = mysqli_prepare($conexion, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $nombre_actual, $apellido_actual, $email_actual, $foto_actual, $bio_actual, $intereses_actual);
                mysqli_stmt_fetch($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }

    function uploadProfilePicture($file) {
        $directory = "galeria/";
        $filename = basename($file["name"]);
        $file_path = $directory . uniqid() . '-' . $filename;
        $file_type = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            return false;
        }

        if (file_exists($file_path)) {
            return false;
        }

        if ($file["size"] > 2000000) {
            return false;
        }

        if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif" ) {
            return false;
        }

        if (move_uploaded_file($file["tmp_name"], $file_path)) {
            return $file_path;
        } else {
            return false;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_nuevo = $_POST["nombre"];
        $apellido_nuevo = $_POST["apellido"];
        $email_nuevo = $_POST["email"];
        $bio_nueva = $_POST["biografia"];
        $intereses_nuevos = $_POST["intereses"];
        
        $contra_nueva = $_POST["contrasena"];
        $confirmar_contra = $_POST["confirmar_contrasena"];
    
        // Verifica si se proporcionó una nueva contraseña y si coincide con la confirmación.
        if (!empty($contra_nueva) && $contra_nueva == $confirmar_contra) {
            $contra_encriptada = password_hash($contra_nueva, PASSWORD_DEFAULT);
    
            $sql = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, contrasena = ?, biografia = ?, intereses = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conexion, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $nombre_nuevo, $apellido_nuevo, $email_nuevo, $contra_encriptada, $bio_nueva, $intereses_nuevos, $id_usuario);
                if (mysqli_stmt_execute($stmt)) {
                    $mensaje = "Información de perfil actualizada exitosamente.";
                    $mensaje_clase = "success";
    
                    $_SESSION["nombre"] = $nombre_nuevo;
                    $_SESSION["apellido"] = $apellido_nuevo;
                    $_SESSION["email"] = $email_nuevo;
                    // Establecer el estado de verificación del usuario a "no verificado" y la fecha de vencimiento a null
                    $sql_verificacion = "UPDATE usuario SET certificacion = 0, fecha_vencimiento = NULL WHERE id = ?";
                    if ($stmt_verificacion = mysqli_prepare($conexion, $sql_verificacion)) {
                        mysqli_stmt_bind_param($stmt_verificacion, "i", $id_usuario);
                        mysqli_stmt_execute($stmt_verificacion);
                        mysqli_stmt_close($stmt_verificacion);
                    }
                } else {
                    $mensaje = "Hubo un problema al actualizar la información de perfil. Inténtalo nuevamente.";
                    $mensaje_clase = "error";
                }
                mysqli_stmt_close($stmt);
            }
        } elseif (empty($contra_nueva) && empty($confirmar_contra)) {
            // Si no se proporciona una nueva contraseña y no se confirma, no actualices la contraseña.
            $sql = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, biografia = ?, intereses = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conexion, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssi", $nombre_nuevo, $apellido_nuevo, $email_nuevo, $bio_nueva, $intereses_nuevos, $id_usuario);
                if (mysqli_stmt_execute($stmt)) {
                    $mensaje = "Información de perfil actualizada exitosamente.";
                    $mensaje_clase = "success";
    
                    $_SESSION["nombre"] = $nombre_nuevo;
                    $_SESSION["apellido"] = $apellido_nuevo;
                    $_SESSION["email"] = $email_nuevo;
                    // Establecer el estado de verificación del usuario a "no verificado" y la fecha de vencimiento a null
                    $sql_verificacion = "UPDATE usuario SET certificacion = 0, fecha_vencimiento = NULL WHERE id = ?";
                    if ($stmt_verificacion = mysqli_prepare($conexion, $sql_verificacion)) {
                        mysqli_stmt_bind_param($stmt_verificacion, "i", $id_usuario);
                        mysqli_stmt_execute($stmt_verificacion);
                        mysqli_stmt_close($stmt_verificacion);
                    }
    
                } else {
                    $mensaje = "Hubo un problema al actualizar la información de perfil. Inténtalo nuevamente.";
                    $mensaje_clase = "error";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $mensaje = "Las contraseñas no coinciden.";
            $mensaje_clase = "error";
        }
    
        // Procesamiento de la imagen de perfil si se proporciona una.
        if ($_FILES["foto_perfil"]["error"] == UPLOAD_ERR_OK) {
            $new_picture_path = uploadProfilePicture($_FILES["foto_perfil"]);
            if ($new_picture_path) {
                $sql = "UPDATE usuario SET foto = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conexion, $sql)) {
                    mysqli_stmt_bind_param($stmt, "si", $new_picture_path, $id_usuario);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $mensaje = "Hubo un problema al subir la foto de perfil. Asegúrate de que sea una imagen válida.";
                $mensaje_clase = "error";
            }
        }
    }


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Lora:ital@0;1&family=Noto+Sans+Osmanya&family=Raleway:ital,wght@0,100;0,300;0,400;0,500;1,100&display=swap" rel="stylesheet">
</head>
<body>
    <?php
        include './cabecera.php';
    ?>

    <div class="container mt-5">
        <h1 class="mb-4">Editar Perfil</h1>
        <?php
        if (!empty($mensaje)) {
            echo '<div class="alert alert-' . $mensaje_clase . '">' . $mensaje . '</div>';
        }
        ?>
        <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre_actual; ?>">
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido_actual; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email_actual; ?>">
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Biografía</label>
                <textarea class="form-control" id="bio" name="biografia"><?php echo $bio_actual; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="intereses" class="form-label">Intereses</label>
                <input type="text" class="form-control" id="intereses" name="intereses" value="<?php echo $intereses_actual; ?>">
            </div>
            <div class="mb-3">
                <label for="foto_perfil" class="form-label">Foto de perfil</label>
                <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena">
            </div>
            <div class="mb-3">
                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena">
            </div>
			<?php if($esVerificado == 1): ?>
			<div class="alert alert-warning mt-3" role="alert">
				Si modificas algún dato, deberás volver a realizar el proceso de verificación.
			</div>
			<?php endif; ?>
			<div class="text-center">
                <button type="submit" class="btn btn-dark">Actualizar</button>
			</div>
        </form>
    </div>

    <?php
        include './pie.php';
    ?>
    <!--[Scripts]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>