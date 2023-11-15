<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Alquiler</title>
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
     <!--[Insertar cabecera]-->
     <?php
        require './BD/conexion.php';
        require './cabecera.php';
    ?>
    <div class="container search-container">
      <div class="col-mb-6">
        <div class="busqueda">
          
          <!--<div class="boton_busqueda">
              
          </div>-->
          <div class="busqueda input-group">
              <input type="text" class="form-control" placeholder="Por favor ingrese su Busqueda" name="buscador" id="buscador">
              <span class="input-group-text bg-secondary text-white"><i class="fa-solid fa-magnifying-glass"></i></span>
          </div>
        </div>            
      </div>
      <br><br>
      <div class="container">
            <div class="row" id="resultados_busqueda">
                <div class="col">

                </div>
            </div>
      </div>
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