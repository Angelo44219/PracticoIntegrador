<?php
    require_once './BD/conexion.php';
//Lista de acciones a realizar segun lo que ingrese el usuario
if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
            //casos de registros

        case 'eliminar_publicacion':
            eliminar_publicacion();
            break;
        
        /*case 'eliminar_resena':
            eliminar_resena();
            break;*/
    }
}

function eliminar_publicacion()
{
    include './BD/conexion.php';
    session_start();
    $id_publicacion=$_POST['id'];
    

    $borrar_publicacion = "DELETE FROM publicacion WHERE id = '$id_publicacion' ";
        $eliminar=mysqli_query($conexion,$borrar_publicacion);
            if ($eliminar) {
                echo "
                <script src='./js/sweetAlert2.js'></script>
                    document.addEventListener('DOMContentLoaded',function() {
                        <script language='JavaScript'>
                            Swal.fire({
                                icon: 'success',
                                title: 'Publicacion eliminada!',
                                text: 'Se han borrado los datos de tu publicacion correctamente.',
                                showCancelButton: 'false',
                                ConfirmButtonText: 'Aceptar',
                                timer:2000
                            }).then(()=>{
                                location.assign('./Index.php');
                            });
                        </script>
                    })";
                exit();
            } else {
                echo "
                <script src='./js/sweetAlert2.js'></script>
                document.addEventListener('DOMContentLoaded', function(){
                
                    <script language= 'JavaScript'>
                        Swal.fire({
                            icon: 'error',
                            title: 'Ha ocurrido un error!',
                            text: 'No se han podido borrar los datos de tu publicacion:'".mysqli_error($conexion).",
                            showCancelButton: 'false',
                            ConfirmButtonText: 'Aceptar',
                            timer:2000
                        }).then(()=>{
                            location.assign('./detalles_publicacion.php');
                        });
                    
                    </script>
                })";
            }
        include './BD/cerrar_conexion.php';
}

    
?>
                                
                            
                         
                                

                     

    
