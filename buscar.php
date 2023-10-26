<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buscar alquiler</title>
  <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body class="body_buscar">
    <!--[Insertar cabecera]-->
    <?php
        require './BD/conexion.php';
        require './cabecera.php';
    ?>
    <div class="container search-container">
      <div class="col-mb-6">
        <form method="get" action="buscar.php">
              <input type="text" placeholder="Por favor ingrese su Busqueda" name="buscador" id="buscador">
              <div class="input-group-append">
                  <button type="submit" class="btn">Realizar busqueda</button>
              </div>
        </form>
      </div>
        <?php
          include './BD/conexion.php';
          $contador=0;
          if($_SERVER["REQUEST_METHOD"]=="GET"){
              $buscador=!empty($_GET['buscador']) ? $_GET['buscador'] : null;
              if(empty($buscador)){
                $consulta="SELECT p.id,p.titulo, p.descripcion, p.ubicacion,e.etiqueta,a.costo,i.imagen,u.nombre,u.apellido,p.fecha_subida FROM publicacion p INNER JOIN alquiler a ON p.id=a.id_publicacion INNER JOIN imagen i ON p.id=i.id_publicacion INNER JOIN usuario u ON p.id_usuario=u.id INNER JOIN etiqueta e ON p.id=e.id_etiqueta WHERE a.estado=1";
              }else{
                $consulta="SELECT p.id,p.titulo, p.descripcion, p.ubicacion,e.etiqueta,a.costo,i.imagen, u.nombre ,u.apellido,p.fecha_subida FROM publicacion p INNER JOIN alquiler a ON p.id=a.id_publicacion INNER JOIN imagen i ON p.id=i.id_publicacion INNER JOIN usuario u ON p.id_usuario=u.id INNER JOIN etiqueta e ON p.id=e.id_etiqueta WHERE a.estado=1 
                AND p.titulo LIKE '%$buscador%' OR p.descripcion LIKE '%$buscador%' OR p.ubicacion LIKE '%$buscador%';";
              }

              $resultado=mysqli_query($conexion,$consulta);

              if($resultado){
              echo "<br>";
              echo "<h3>Resultados de la Busqueda</h3>";
              echo "<br>";
              if(mysqli_num_rows($resultado) > 0){
                  while($fila=mysqli_fetch_assoc($resultado)){
                      echo  "<div class='card mb-5' style='max-width: 540px;'>";
                        echo  "<img src='".htmlspecialchars($fila['imagen'])."' class='card-img-top' alt='...'>";
                        echo  "<div class='card-body'>";
                          echo  "<h5 class='card-title'>".htmlspecialchars($fila['titulo'])."</h5>";
                          echo  "<p class='card-text'>".htmlspecialchars($fila['descripcion']).".</p>";
                          echo  "<p class='card-text'><small class='text-bold'>Ubicacion: ".htmlspecialchars($fila['ubicacion'])."</small></p>";
                          echo  "<p class='card-text'><small class='text-bold'>Etiquetas: ".htmlspecialchars($fila['etiqueta'])."</small></p>";
                          echo  "<p class='card-text'><small class='text-bold'>Costo por dia: ".number_format($fila['costo'],2,'.',',')."</small></p>";
                          echo  "<p class='card-text'><small class='text-muted'>Publicado por : ".htmlspecialchars($fila['nombre'])."  ".htmlspecialchars($fila['apellido'])."</small></p>";
                          echo  "<p class='card-text'><small class='text-muted'>Subido el : ".date($fila['fecha_subida'])."</small></p>";
                          echo  "<div class='container text-center'>";
                            echo  "<a href='detalle_alquiler.php?id?='".htmlspecialchars($fila['id'])."'' class='btn btn-primary btn-sm'>Ver alquiler</a>";
                          echo  "</div>";
                        echo  "</div>";
                      echo  "</div>";

                  }
              }else{
                  echo "Sin resultados";
              }
              
            }else{
              echo '<div class="alert alert-danger" role="alert">Error en la búsqueda: ' . mysqli_error($conexion) . '</div>';
            }
          }


          include './BD/cerrar_conexion.php';
        ?>

    </div>
    <?php
      include './pie.php';
    ?>
</body>
</html>