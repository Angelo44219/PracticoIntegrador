<?php
    include './BD/conexion.php';

    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $buscador=$_GET['buscador'];
        $consulta = "select p.id, p.titulo, p.descripcion,p.ubicacion,p.etiqueta,p.costo_alquiler, i.imagen from publicacion p inner join imagen i on p.id = i.id_publicacion WHERE p.titulo LIKE '%$buscador%' or p.ubicacion LIKE '%$buscador%' OR p.etiqueta LIKE '%$buscador%';";
        $resultado=mysqli_query($conexion,$consulta);
        

        if(mysqli_num_rows($resultado) > 0){
            while($fila=mysqli_fetch_row($resultado)){
                echo "
                  <div class='card mb-5 card-publicacion' style='max-width: 540px;'>
                    <div class='row g-0'>
                      <div class='col-md-4'>
                        <img src='$fila[6]' class='img-fluid rounded-start' alt='...'>
                      </div>
                      <div class='col-md-8'>
                        <div class='card-body'>
                          <h5 class='card-title'>$fila[1]</h5>
                          <p class='card-text'>$fila[2].</p>
                          <p class='card-text'><small class='text-bold'>Ubicacion: $fila[3]</small></p>
                          <p class='card-text'><small class='text-bold'>Etiquetas: $fila[4]</small></p>
                          <p class='card-text'><small class='text-bold'>Costo por dia: $fila[5] \$</small></p>
                          <p class='card-text'><small class='text-muted'>Last updated 3 mins ago</small></p>
                          <a href='#' class='btn btn-primary btn-sm'>Solicitar Alquiler</a>
                        </div>
                      </div>
                    </div>
                </div>";
            }
        }else{
            echo "Sin resultados";
        }
    }


    include './BD/cerrar_conexion.php';
?>