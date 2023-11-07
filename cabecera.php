<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapiBnB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Estilos/Estilo.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Lora:ital@0;1&family=Noto+Sans+Osmanya&family=Raleway:ital,wght@0,100;0,300;0,400;0,500;1,100&display=swap" rel="stylesheet">
</head>
<body>
    
    <main class="sticky-top">
            <nav class="navbar navbar-expand-lg navbar-dark p-3 header sticky-top">
                <div class="container-fluid">
                    <a class="navbar-brand logo" href="./Index.php"><i class="fa-solid fa-house-chimney"></i> RapiBNB</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
            

                    <div class=" collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ms-auto">

                        <a class="nav-link" href="./Buscador.php">
                            <i class="fa-solid fa-magnifying-glass"></i> Buscar alquiler
                        </a>
                        <?php
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start(); // Inicia la sesión si no se encuentra activa
                            }
    
                            // Botón "Crear Ofertas de Alquiler" con icono de "+"
                            if (isset($_SESSION["id"])) {
                                echo '<li class="nav-item"><a class="nav-link" href="nueva_publicacion.php"><i class="fa-solid fa-plus"></i> Nueva publicacion</a></li>';
                            }

                            
                        ?>
                        <?php
                                // Botón "Admin" con icono de una tuerca
                                if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
                                    echo '<li class="nav-item"><a class="nav-link" href="index_administrador.php"><i class="fa-solid fa-gears"></i> Panel de Administración</a></li>';
                                }

                                // Botón de usuario (person) en el lado derecho
                                if (isset($_SESSION["id"])) {
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="perfil_usuario.php"><i class="fa-solid fa-user"></i> Mi Perfil</a>
                                    <a class="dropdown-item" href="editar_perfil.php"><i class="fa-solid fa-user-pen"></i> Editar Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="cerrar_sesion.php"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</a>
                                </div>
                            </li>
                            <?php
                                }else{
                                    echo '<li class="nav-item"><a class="nav-link" href="./iniciar_sesion.php"> <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a></li>';
                                    echo '<li class="nav-item"><a class="nav-link" href="./registrarse.php"> <i class="fa-solid fa-address-card"></i> Registrarse</a></li>';
                                }
                            ?>                      
                        </ul>
                    </div>
                </div>
            </nav>        
    </main>
    <!--[Scripts JS y demas]-->
    <script src="./js/script.js"></script>
    <script src="https://kit.fontawesome.com/91e1aa86a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>