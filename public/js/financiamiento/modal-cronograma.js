// public\js\financiamiento\modal-cronograma.js
// Funciones para gestionar el modal de cronograma de pagos

function vincularEventosCronograma() {
    $('.client-row').each(function () {
        // Cuando se hace clic en el botón "Ver Cronograma" (btn-primary)
        $(this).find('.btn-primary').off('click').on('click', function () {
            var tipo = $(this).closest('tr').data('tipo');  // Añadido: obtener el tipo
            var id = $(this).closest('tr').data('id');      // Añadido: obtener el id genérico

            // Llamar a la función para cargar el cronograma en el modal
            cargarCronograma(id);  // Modificado: pasar el id genérico
        });
    });
}

function cargarCronograma(idConductor) {
    // Determinar el tipo de ID basado en el elemento padre
    var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // ✅ CAMBIADO: usar idConductor en lugar de id
    var tipo = tr ? tr.getAttribute('data-tipo') : null;

    document.getElementById("selectBox").innerText = "Seleccionar un financiamiento ⬇";
    var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
    tablaFinanciamientos.innerHTML = "";
    var tablaCuotas = document.querySelector("#tablaCuotas tbody");
    tablaCuotas.innerHTML = "";
    document.getElementById("tablaCuotas").style.display = "none"; // ✅ Ocultar la tabla de cuotas
    document.getElementById("noCronogramaMessage").style.display = "none"; // ✅ Ocultar el mensaje de no cronograma)

    // Construir el parámetro según el tipo
    var param = tipo === 'conductor' ? 'id_conductor=' + idConductor : 'id=' + idConductor;  // ✅ CAMBIADO: usar idConductor en lugar de id

    $.ajax({
        url: '/obtenerCuotasPorCliente?' + param,
        dataType: 'json',
        success: function (data) {
            //console.log("Datos recibidos del servidor:", data);

            // if (data.financiamientos === null) {  // ✅ Verificar si "financiamientos" es null
            //     return; // ✅ Detener la ejecución si no hay financiamientos
            // }
            if (!data.financiamientos || data.financiamientos.length === 0) {
                // Mostrar mensaje cuando no hay financiamientos
                document.getElementById("noCronogramaMessage").style.display = "block";
                return;
            }



            var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
            tablaFinanciamientos.innerHTML = ""; // Limpiar la tabla antes de llenarla

            data.financiamientos.forEach(financiamiento => {
                var fila = document.createElement("tr");
                fila.onclick = function () { seleccionarFila(this, financiamiento); }; // Pasar el objeto financiamiento

                fila.innerHTML = `
                    <td>${financiamiento.producto.nombre}</td>
                    <td>${financiamiento.grupo_financiamiento}</td>
                    <td>${financiamiento.cantidad_producto}</td>
                    <td>${financiamiento.monto_total}</td>
                    <td>${financiamiento.producto.categoria}</td>
                `;
                tablaFinanciamientos.appendChild(fila);
            });

        },
        error: function () {
            alert("Error al cargar el cronograma de pagos");
        }
    });
}
