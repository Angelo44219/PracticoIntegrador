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
    <nav class="navbar navbar-expand-lg navbar-dark p-3 header">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">RapiBNB</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        

          <div class=" collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">

               <form action="Index.php" method="GET">
                  <input type="text" id="buscador" name="buscador" placeholder="Ingrese su busqueda"  class="form-control-md"/>
                  <input type="submit" value="Buscar" class="btn btn-dark">
              </form>  
              

              <li class="nav-item link-header">
                <a class="nav-link mx-2 active" aria-current="page" href="./Index.php">Inicio</a>
              </li>

              <li class="nav-item link-header">
                <a class="nav-link mx-2" href="./iniciar_sesion.php">Iniciar Sesion</a>
              </li>
              
              <li class="nav-item link-header">
                <a class="nav-link mx-2" href="./registrarse.php">Registrarse</a>
              </li>

              <li class="nav-item dropdown link-header">
                <a class="nav-link mx-2 dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Sobre nosotros
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li><a class="dropdown-item" href="#">Sobre nosotros</a></li>
                  <li><a class="dropdown-item" href="#">Contactanos</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        </nav>
        <!------------------------------------>
        <!----------[Contenido Rapi]--------->
        <main>
           <!--<div class="container contenedor_inicio">
             <div class="row row-cols-1">
                  
              </div>
           </div>-->
           <br>
           <div class="container">
                <div class="row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                  <div class="col-5">
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
                   </div>
                   <div class="col">
                        <?php
                          include './ver_publicacion.php';
                        ?>
                   </div>
                </div>
           </div>
           <footer class="footerClass">
              <div class="Redes-Sociales">
                <a href="https://www.twitter.com">
                  <i class="fa-brands fa-square-x-twitter"></i>
                </a>
                <a href="https://www.facebook.com">
                  <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="https://www.instagram.com">
                  <i class="fa-brands fa-instagram"></i>
                </a>
              </div>
              <p class="derechos">Rapibnb @ 2023, Todos los derechos reservados</p>
            </footer>
        </main>
        <!------------------------------------>
    <!---------------------------------------------------------------------------------------->
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>