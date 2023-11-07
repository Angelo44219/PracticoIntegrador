
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



var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-userid');
            var modalInput = deleteModal.querySelector('#deleteUserId');
            modalInput.value = userId;
        });
