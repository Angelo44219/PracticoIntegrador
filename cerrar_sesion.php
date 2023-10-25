<?php
    include './BD/conexion.php';
    session_start();
    
    session_unset();
    session_destroy();
    setcookie('contrasena','',time()+(60*60*24*365));
    $_SESSION['email']='';
    setcookie('email','',time()+(60*60*24*365));
    header('location: iniciar_sesion.php');

?>