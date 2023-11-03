
document.getElementById("buscador").addEventListener("input", function() {
    var terminoBusqueda = this.value;

    // Enviar solicitud AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "buscar.php?termino=" + terminoBusqueda, true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            document.getElementById("resultados_busqueda").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});

var paginaActual = 1;

function cargarElementos(pagina) {
    // Enviar solicitud AJAX para obtener los elementos de la página
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "paginacion.php?pagina=" + pagina, true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            var elementos = JSON.parse(xhr.responseText);

            // Mostrar los elementos en la página
            var elementosDiv = document.getElementById("elementos");
            elementosDiv.innerHTML = "";
            for (var i = 0; i < elementos.length; i++) {
                elementosDiv.innerHTML += elementos[i].nombre + "<br>";
            }

            // Actualizar la paginación
            var paginacionDiv = document.getElementById("paginacion");
            paginacionDiv.innerHTML = "";
            for (var i = 1; i <= pagina; i++) {
                paginacionDiv.innerHTML += "<button onclick='cargarElementos(" + i + ")'>" + i + "</button>";
            }
        }
    };
    xhr.send();
}

cargarElementos(paginaActual);

// Configurar el botón de eliminación de oferta
$(document).on("click", ".eliminar-oferta-button", function () {
    var id_publicacion = $(this).data('id');
    $("#confirmDeleteButton").attr("href", "detalles_publicacion.php?id=" + id_publicacion + "&action=delete");
});

// Configurar el botón de eliminación de reseña
$(document).on("click", ".eliminar-resena-button", function () {
    var resenaId = $(this).data('resenawid');
    $("#confirmDeleteReviewButton").attr("href", "detalles_publicacion.php?id=<?php echo $id_publicacion; ?>&action=deleteResena&resenaId=" + resenaId);
});
