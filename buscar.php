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
          echo '<div class="card mb-4">';
            echo '<div class="row g-0">';
              echo '<div class="col-md-4">';
                echo '<img src="' . json_decode($fila["fotos"])[0] . '" class="img-thumbnail" alt="Imagen del alquiler">';
              echo '</div>';
              echo '<div class="col-md-8">';
                echo '<div class="card-body">';
                  echo '<h3 class="card-title">' . htmlspecialchars($fila["titulo"]) . '</h3>';
                  echo '<p class="card-text">' . htmlspecialchars($fila["descripcion"]) . '</p>';
                  echo '<p class="card-text"><strong>Ubicación:</strong> ' . htmlspecialchars($fila["ubicacion"]) . '</p>';
                  $etiquetas = explode(',', $fila["etiqueta"]);
                  echo '<p><strong>Etiquetas:</strong> ';
                    foreach ($etiquetas as $q) {
                      $q = trim($q);
                      echo '<a href="Buscador.php?q=' . urlencode($q) . '" class="q">#' . htmlspecialchars($q) . '</a> ';
                    }
                  echo '</p>';
                  echo '<a href="detalles_publicacion.php?id=' . $fila["id"] . '" class="btn btn-primary">Ver Detalles</a>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
          echo '</div>';
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