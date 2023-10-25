<?php
    require_once './BD/conexion.php';
    $estado="";                        
    $consulta_publicacion = "select p.titulo,p.descripcion,p.ubicacion,a.estado,a.costo,i.imagen from publicacion p inner join alquiler a on p.id=a.id_publicacion inner join imagen i on p.id=i.id_publicacion WHERE a.estado=a.estado";
    $filtro="";
    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $buscador=!empty($_GET['buscador']) ? $_GET['buscador'] : null;
        $filtro=" AND p.titulo LIKE '%$buscador%' OR p.ubicacion LIKE '%$buscador%';";
        $consulta_publicacion=$consulta_publicacion.$filtro;
        $resultado=mysqli_query($conexion,$consulta_publicacion);
        

        echo "<br>";
        if(mysqli_num_rows($resultado) > 0){
            while($fila=mysqli_fetch_assoc($resultado)){
              echo "
                <div class='card'>

                    <img src='$fila[imagen]' class='card-img-top' alt='...'>
                    
                    <div class='card-body'>
                        <h5 class='card-title'>$fila[titulo]</h5>
                        <p class='card-text'>$fila[descripcion].</p>
                        <p class='card-text'><small class='text-bold'>Ubicacion: $fila[ubicacion]</small></p>";
                        if($fila['estado']==1){
                            $estado="Activo";
                        }else{
                            $estado="Inactivo";
                        }    
                        echo "<p class='card-text'><small class='text-success'> $estado </small></p>
                        <p class='card-text'><small class='text-bold'>Costo por dia: ".number_format($fila['costo'],2,'.',',')." \$</small></p>
                        <p class='card-text'><small class='text-muted'>Last updated 3 mins ago</small></p>
                        <a href='#' class='btn btn-primary btn-sm'>Solicitar Alquiler</a>
                    </div>
                    
                </div><br>";
            }
        }else{
            echo "<h3>No se han podido encontrar resultados :( </h3>";          
        }
          
    }        
      include './BD/cerrar_conexion.php';
?>