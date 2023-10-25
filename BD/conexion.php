<?php
    $servidor="localhost:3306";
    $usuario="root";
    $contraseña='';
    $base="rappibnb";

    $conexion=
    mysqli_connect($servidor,$usuario,$contraseña,$base);

    if(!$conexion){
        die("Conexion Fallida: ".mysqli_connect_error());
    }


?>