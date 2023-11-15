<?php
    include './BD/conexion.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_resena = $_POST['id_resena'];
        $id_usuario = $_POST['id_usuario'];
        $respuesta = $_POST['respuesta'];
    
        // Verificar si ya existe una respuesta para esta reseña por parte del usuario
        $verificar_respuesta = "SELECT * FROM respuesta_resena WHERE id_resena = ? AND id_usuario = ?";
        $buscar_respuesta = mysqli_prepare($conexion,$verificar_respuesta);
        mysqli_stmt_bind_param($buscar_respuesta,"ii", $id_resena, $id_usuario);
        mysqli_stmt_execute($buscar_respuesta);
        $resultados_respuestas =mysqli_stmt_get_result($buscar_respuesta);
        $respuesta=mysqli_num_rows($resultados_respuestas);
    
        if (($respuesta)> 0) {
            echo "<div class='alert alert-danger' role='alert'>Ya has respondido a esta reseña.</div>";
        } else {
            $agregar = "INSERT INTO respuesta_resena (id_resena, id_usuario, respuesta) VALUES (?, ?, ?)";
            $insertar_respuesta = mysqli_prepare($conexion,$agregar);
            mysqli_stmt_bind_param($insertar_respuesta,"iis", $id_resena, $id_usuario, $respuesta);
            if (mysqli_stmt_execute($insertar_respuesta)) {
                echo "<div class='alert alert-success' role='alert'>La respuesta se ha enviado correctamente.</div>";
                exit();
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error al enviar la respuesta.</div>";
            }
        }
        mysqli_stmt_close($buscar_respuesta);
        mysqli_stmt_close($insertar_respuesta);

        include './BD/cerrar_conexion.php';
    }
?>