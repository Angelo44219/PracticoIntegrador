<?php 
    include './BD/conexion.php';
?>

<!-- Modal para confirmar la eliminación de la oferta -->
<div class="modal fade" id="delete<?php echo $id_publicacion;?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 mt-5 align-center">
                            <h3>Esta seguro de eliminar esta publicacion?</h3>
                            <br>
                            <p>una vez que realice este proceso ya no podra recuperar los datos de su publicacion.</p>
                            <form action="./acciones.php" method="post">
                                <input type="hidden" name="accion" value="eliminar_publicacion">
                                <input type="hidden" name="id" value="<?php echo $id_publicacion; ?>">    
                            
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">Eliminar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>