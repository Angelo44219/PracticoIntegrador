<?php
    include('./BD/conexion.php');

    // Verificar si el usuario es un administrador
    session_start();
    if (!isset($_SESSION['id']) || $_SESSION['admin'] != 1) {
        // Si no es un administrador, redirigir a otra página o mostrar un mensaje de error
        header("Location: Index.php");
        exit();
    }
    
    // Obtener el ID del usuario de la URL
    if (isset($_GET['id_usuario'])) {
        $user_id = $_GET['id_usuario'];
        
        // Consulta para obtener las ofertas de alquiler del usuario
        $query = "SELECT id, titulo, ubicacion , estado FROM publicacion WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }
    } else {
        // Si no se proporciona un ID de usuario válido en la URL, redirigir a otra página
        header("Location: index_administrador.php");
        exit();
    }
    
    // Procesar la eliminación de una oferta de alquiler
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_publicacion'])) { 
        $id_publicacion = $_POST['eliminar_publicacion'];
        
        // Consulta para eliminar la oferta de alquiler
        $consultar_eliminacion = "DELETE FROM publicacion WHERE id = ?";
        $realizar_eliminacion = mysqli_prepare($conexion, $consultar_eliminacion);
        
        if ($realizar_eliminacion) {
            mysqli_stmt_bind_param($realizar_eliminacion, "i", $id_publicacion);
            mysqli_stmt_execute($realizar_eliminacion);
            mysqli_stmt_close($realizar_eliminacion);
        }
        
        // Redirigir de nuevo a la página de ver_ofertas.php
        header("Location: ofertas_usuario.php?user_id=" . $user_id);
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_publicacion'])) { 
        $id_publicacion = $_POST['editar_publicacion'];
        $titulo=$_POST['titulo'];
        $ubicacion=$_POST['ubicacion'];
        $estado=$_POST['estado'];
        
        // Consulta para eliminar la oferta de alquiler
        $consultar_edicion= "UPDATE publicacion SET titulo=?, ubicacion=?, estado=? WHERE id = ?";
        if($realizar_edicion= mysqli_prepare($conexion, $consultar_edicion)){
            mysqli_stmt_bind_param($realizar_edicion, "ssii",$titulo,$ubicacion,$estado,$id_publicacion);
            if(mysqli_stmt_execute($realizar_edicion)){

                echo "<script src='./js/sweetAlert2.js'></script>
                document.addEventListener('DOMContentLoaded', function(){
                
                    <script language= 'JavaScript'>
                        Swal.fire({
                            icon: 'success',
                            title: 'Cambios realizados!',
                            text: 'se han editado los datos de tu publicacion correctamente.',
                            showCancelButton: 'false',
                            ConfirmButtonText: 'Aceptar',
                            timer:2000
                        }).then(()=>{
                            location.assign('./ofertas_usuario.php?id=".$id_publicacion."');
                        });
                    
                    </script>
                })";
            }else{
                echo "<script src='./js/sweetAlert2.js'></script>
                document.addEventListener('DOMContentLoaded', function(){
                
                    <script language= 'JavaScript'>
                        Swal.fire({
                            icon: 'error',
                            title: 'Ha ocurrido un error!',
                            text: 'No se han podido editar los datos de tu publicacion:'".mysqli_error($conexion).",
                            showCancelButton: 'false',
                            ConfirmButtonText: 'Aceptar',
                            timer:2000
                        }).then(()=>{
                            location.assign('./ofertas_usuario.php?id='".$id_publicacion."');
                        });
                    
                    </script>
                })";
            }
            mysqli_stmt_close($realizar_edicion);
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones del usuario</title>
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

    <div class="container mt-4 table-responsive">
        <table class="table table-bordered table-sm text-center align-middle text-capitalize table-hover">
            <thead class="table-active table-dark">
                <tr>
                    <th>Id</th>
                    <th>Titulo</th>
                    <th>Ubicacion</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
            <?php
                    while ($publicacion = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$publicacion['id']}</td>";
                        echo "<td>{$publicacion['titulo']}</td>";
                        echo "<td>{$publicacion['ubicacion']}</td>";
                        echo "<td>";
                        
                        //boton para editar la publicacion de alquiler con ventana de confirmacion
                        echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal' . $publicacion['id'] . '"><i class="fa-solid fa-pencil"></i> Editar Oferta</button>&nbsp;';
                        //botón para eliminar la publicacion de alquiler con ventana modal de confirmación
                        echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal' . $publicacion['id'] . '"><i class="fa-solid fa-trash"></i> Eliminar Oferta</button>';
                        
                        // Ventana modal de confirmación
                        echo '<div class="modal fade" id="deleteModal' . $publicacion['id'] . '" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">';
                        echo '<div class="modal-dialog">';
                        echo '<div class="modal-content">';
                        echo '<div class="modal-header">';
                        echo '<h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>';
                        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                        echo '</div>';
                        echo '<div class="modal-body">';
                        echo '¿Estás seguro de que deseas eliminar esta oferta de alquiler?';
                        echo '</div>';
                        echo '<div class="modal-footer">';
                        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>';
                        
                        // Formulario para enviar la solicitud de eliminación
                        echo '<form method="post">';
                        echo '<input type="hidden" name="eliminar_publicacion" value="' . $publicacion['id'] . '">';
                        echo '<button type="submit" class="btn btn-danger">Eliminar</button>';
                        echo '</form>';
                        
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        

                        //ventana modal para editar el usuario
                        echo '<div class="modal fade" id="editModal' . $publicacion['id'] . '" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">';
                        echo '<div class="modal-dialog modal-xl">';
                        echo '<div class="modal-content">';
                        echo '<div class="modal-header text-center bg-primary text-white">';
                        echo '<h5 class="modal-title align-content-center" id="deleteModalLabel">Editar datos</h5>';
                        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                        echo '</div>';
                        echo '<div class="modal-body">';
                            echo '<h2>Editar datos del alquiler</h2>';
                            echo '<form method="post">';
                                    echo '<div class="form-group mb-3">';
                                    echo '<label for="titulo">Título: </label>';
                                    echo '<input type="text" class="form-control" id="titulo" name="titulo" value="' . htmlspecialchars($publicacion["titulo"]) . '" required>';
                                    echo '</div>';
                                    echo '<div class="form-group mb-3">';
                                    echo '<label for="ubicacion">Ubicación: </label>';
                                    echo '<input type="text" class="form-control" id="ubicacion" name="ubicacion" value="' . htmlspecialchars($publicacion["ubicacion"]) . '" required>';
                                    echo '</div>';
                                    echo '<div class="form-group mb-3">';
                                    echo '<label for="estado">Estado de la publicacion: </label>';
                                    echo '<input type="number" class="form-control" id="estado" name="estado" value="' . htmlspecialchars($publicacion["estado"]) . '" required>';
                                    echo '</div>';
                                    
                                
                                echo '<div class="modal-footer">';
                                
                                //formulario para solicitar la edicion de la publicacion
                                    echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>';
                                    echo '<input type="hidden" name="editar_publicacion" value="' . $publicacion['id'] . '">';
                                    echo '<button type="submit" class="btn btn-success">Guardar cambios</button>';
                                echo '</form>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
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