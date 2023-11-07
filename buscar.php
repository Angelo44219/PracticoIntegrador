<?php
  include './BD/conexion.php';
                
  if($_SERVER["REQUEST_METHOD"]=="GET" && isset($_GET['termino'])){
    $terminoBusqueda = $_GET['termino'];
    $buscador=!empty($_GET['buscador']) ? $_GET['buscador'] : null;
    if(empty($terminoBusqueda)){
      $consulta="SELECT p.id,p.titulo, p.descripcion, p.ubicacion,p.costo,p.fotos,p.etiqueta,u.nombre,u.apellido,p.fecha_subida FROM publicacion p INNER JOIN usuario u ON p.id_usuario=u.id  WHERE p.estado=1";
    }else{
      $consulta="SELECT p.id,p.titulo, p.descripcion, p.ubicacion,p.costo,p.fotos,p.etiqueta,u.nombre,u.apellido,p.fecha_subida FROM publicacion p INNER JOIN usuario u ON p.id_usuario=u.id  WHERE p.estado=1 
        AND p.titulo LIKE '%$terminoBusqueda%' OR p.descripcion LIKE '%$terminoBusqueda%' OR p.ubicacion LIKE '%$terminoBusqueda%' OR p.etiqueta LIKE '%$terminoBusqueda%';";
    }

    $resultado=mysqli_query($conexion,$consulta);

    if($resultado){
      echo "<br>";
      echo "<br>";
      if(mysqli_num_rows($resultado) > 0){
        while($fila=mysqli_fetch_assoc($resultado)){
          echo  "<div class='card mb-4'>";
            echo "<div class='row g-0'>";
              echo "<div class='col-md-4'>";
                echo  "<img src='".htmlspecialchars($fila['fotos'])."' class='img-thumbnail img-fluid' alt='...' width='100%' height='225'>";
              echo "</div>";

              echo "<div class='col-md-8'>";
                echo  "<div class='card-body'>";
                      echo  "<h4 class='card-title'><strong>".htmlspecialchars($fila['titulo'])."</strong></h4>";
                      echo  "<p class='card-text'>".htmlspecialchars($fila['descripcion']).".</p>";
                      echo  "<p class='card-text'><small class='text-bold'>Ubicacion: ".htmlspecialchars($fila['ubicacion'])."</small></p>";
                      echo  "<p class='card-text'><small class='text-bold'>Etiquetas: ".htmlspecialchars($fila['etiqueta'])."</small></p>";
                      echo  "<p class='card-text'><small class='text-bold'>Costo por dia: ".number_format($fila['costo'],2,'.',',')."</small></p>";
                      echo  "<p class='card-text'><small class='text-muted'>Publicado por : ".htmlspecialchars($fila['nombre'])."  ".htmlspecialchars($fila['apellido'])."</small></p>";
                      echo  "<p class='card-text'><small class='text-muted'>Subido el : ".date($fila['fecha_subida'])."</small></p>";
                      
                      echo  "<a href='detalles_publicacion.php?id=".$fila['id']."' class='btn btn-primary btn-sm'>Ver alquiler</a>";

                echo  "</div>";
              echo "</div>";
            echo  "</div>";
          echo  "</div>";
          }
        }else{
          echo "
          <div class='sin_resultados col'> Sin Resultados </div>";
                
        }
                    
      }else{
        echo '<div class="alert alert-danger" role="alert">Error en la búsqueda: ' . mysqli_error($conexion) . '</div>';
      }
    }


      include './BD/cerrar_conexion.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buscar</title>
</head>
<body>

</body>
</html>