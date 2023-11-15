<?php
    session_start();
    include './BD/conexion.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_usuario = $_SESSION["id"];

        // Verificar que los archivos estén presentes y no estén vacíos
        if (
            !isset($_FILES["dni_frente"]) || $_FILES["dni_frente"]["size"] == 0 ||
            !isset($_FILES["dni_dorso"]) || $_FILES["dni_dorso"]["size"] == 0
        ) {
            header("location: perfil_usuario.php?error=Por favor, selecciona los 2 archivos.");
            exit;
        }

        // Verificar tipos de archivo (en este ejemplo solo permitimos jpeg, jpg y png)
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        foreach (['dni_frente', 'dni_dorso'] as $filename) {
            if (!in_array($_FILES[$filename]["type"], $allowedTypes)) {
                header("location: perfil_usuario.php?error=Formato de archivo no permitido.");
                exit;
            }
        }

        // Procesar archivos
        $dni_frente = $_FILES["dni_frente"]["name"];
        $dni_dorso = $_FILES["dni_dorso"]["name"];

        // Mover los archivos a una carpeta del servidor (ej. uploads/)
        move_uploaded_file($_FILES["dni_frente"]["tmp_name"], "documentacion/" . $dni_frente);
        move_uploaded_file($_FILES["dni_dorso"]["tmp_name"], "documentacion/" . $dni_dorso);

        // Guardar las rutas en la base de datos
        $solicitud = "INSERT INTO solicitud (id_usuario, dni_frente, dni_dorso) VALUES (?, ?, ?)";
        $procesar_solicitud=mysqli_prepare($conexion,$solicitud);
        mysqli_stmt_bind_param($procesar_solicitud,"iss", $id_usuario, $dni_frente, $dni_dorso);
        mysqli_stmt_execute($procesar_solicitud);

        if (mysqli_stmt_affected_rows($procesar_solicitud) == 1) {
            header("location: perfil_usuario.php");
        } else {
            header("location: perfil_usuario.php?error=Hubo un error al enviar los documentos.");
        }
    }

?>