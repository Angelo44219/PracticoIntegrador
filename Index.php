<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Practica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
  </head>
  <body>
    <!----------[Navbar Rappi]------------>
      <?php
        include './cabecera.php';
      ?>
        <!----------[Contenido Rapi]--------->
        <main>
           <div class="container">
                
                  <!--<div class="col">
                      <h3>Filtro de Busqueda</h3>
                      <br>
                      <form name="filtro" action="Index.php" method="get">
                          <h6>Etiquetas</h6>
                          <input type="checkbox" name="etiqueta_1" id="et1" value="1"> Niebla
                          <br>
                          <input type="checkbox" name="etiqueta_2" id="et1" value="2"> Montaña
                          <br> 
                          <input type="checkbox" name="etiqueta_3" id="et1" value="3"> Lago
                          <br>  
                          <input type="checkbox" name="etiqueta_4" id="et1" value="4"> Pileta
                          <br><br>

                          <label for="precio">
                            Precio:
                            <input type="number" name="precio" id="precio" class="form-control-sm">
                          </label> 
                          <br><br>
                          <h6>Por fechas</h6>
                          <label for="incio">
                            Fecha de inicio:<br> 
                            <input type="date" name="incio" id="inicio">
                          </label>
                          <br>
                          <label for="fin">
                            Fecha de fin:<br>
                            <input type="date" name="fin" id="fin">
                          </label>  
                          <br><br>

                      </form> 
                   </div>-->
                   
                  
                    <?php
                       include './ver_publicacion.php';
                    ?>
                    
                   
                </div>
        </main>
        <?php
            include './pie.php';
        ?>
        <!------------------------------------>
    <!---------------------------------------------------------------------------------------->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>