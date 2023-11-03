<?php
    require_once './BD/conexion.php';
    $estado="";                        
    $consulta_publicacion = "SELECT p.id,p.titulo, p.descripcion, p.ubicacion,p.etiqueta,p.costo,p.fotos,u.nombre,u.apellido,p.fecha_subida FROM publicacion p INNER JOIN usuario u ON p.id_usuario=u.id WHERE p.estado=1";
    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $resultado=mysqli_query($conexion,$consulta_publicacion);
        

        echo "<br>";
        if(mysqli_num_rows($resultado) > 0){
            echo"<div class='row'>";
                echo "<div class='col-md-5'>";
                    while($fila=mysqli_fetch_assoc($resultado)){
                    echo "
                        <div class='card shadow-sm'>

                            <img src='".$fila['fotos']."' class='card-img-top' alt='...' width='100%' height='225'>
                            
                            <div class='card-body'>
                            <h5 class='card-title'>".htmlspecialchars($fila['titulo'])."</h5>
                                 <p class='card-text'>".htmlspecialchars($fila['descripcion']).".</p>
                                 <p class='card-text'><small class='text-bold'>Ubicacion: ".htmlspecialchars($fila['ubicacion'])."</small></p>
                                 <p class='card-text'><small class='text-bold'>Etiquetas: ".htmlspecialchars($fila['etiqueta'])."</small></p>
                                 <p class='card-text'><small class='text-bold'>Costo por dia: ".number_format($fila['costo'],2,'.',',')."</small></p>
                                 <p class='card-text'><small class='text-bold'>Publicado por :  ".htmlspecialchars($fila['nombre'])."  ".htmlspecialchars($fila['apellido'])."</small></p>
                                 <p class='card-text'><small class='text-bold'>Subido el : ".date($fila['fecha_subida'])."</small></p>
                                 <div class='container text-center'>
                                    <a href='detalles_publicacion.php?id=".$fila['id']."' class='btn btn-primary btn-sm'>Ver alquiler</a>
                                 </div>
                            </div>
                            
                        </div><br>";
                    }
                echo"</div>";
            echo"</div>";
        }else{
            echo "<h3>No se han podido encontrar resultados :( </h3>";          
        }
          
    }        
      include './BD/cerrar_conexion.php';
?>