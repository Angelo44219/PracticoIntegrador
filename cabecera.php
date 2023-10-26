<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapiBnB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
</head>
<body>
    
    <main>
            <nav class="navbar navbar-expand-lg navbar-dark p-3 header">
                <div class="container-fluid">
                    <a class="navbar-brand" href="./Index.php">RapiBNB</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
            

                    <div class=" collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ms-auto">

                        <a class="nav-link" href="./buscar.php">
                            <i class="fa-solid fa-magnifying-glass"></i> Buscar alquiler
                        </a>
                        <?php
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start(); // Inicia la sesión si no se encuentra activa
                            }
    
                            // Botón "Crear Ofertas de Alquiler" con icono de "+"
                            if (isset($_SESSION["id"])) {
                                echo '<li class="nav-item"><a class="nav-link" href="crear_publicacion.php"><i class="bi bi-plus"></i> Crear Oferta</a></li>';
                            }

                            
                        ?>
                        <?php
                                // Botón "Admin" con icono de una tuerca
                                if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
                                    echo '<li class="nav-item"><a class="nav-link" href="administrador.php"><i class="bi bi-gear"></i> Panel de Administración</a></li>';
                                }

                                // Botón de usuario (person) en el lado derecho
                                if (isset($_SESSION["id"])) {
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="perfil.php">Mi Perfil</a>
                                    <a class="dropdown-item" href="editar_perfil.php">Editar Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a>
                                </div>
                            </li>
                            <?php
                                }else{
                                    echo '<li class="nav-item"><a class="nav-link" href="./iniciar_sesion.php">Iniciar sesión</a></li>';
                                    echo '<li class="nav-item"><a class="nav-link" href="./registrarse.php">Registrarse</a></li>';
                                }
                            ?>                      
                        </ul>
                    </div>
                </div>
            </nav>        
    </main>
    <!--[Scripts JS y demas]-->
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>