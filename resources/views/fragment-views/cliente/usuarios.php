<?php

require_once "app/models/Cliente.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
if (!isset($_SESSION['id_rol'])) {
    header("Location: /arequipago/login"); // Redirige al login si no está autenticado
    exit();
}

// Verificamos que el usuario tenga el rol adecuado
if ($_SESSION['id_rol'] != 3) { // Solo DIRECTOR (id_rol = 3) puede ver esta página
    header("Location: /arequipago/"); // Redirige a la página principal si no tiene permiso
    exit();
}

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">DATOS DE USUARIOS</h6>

        </div>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" id="add-user" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <!-- MODAL CONFIRMAR DATOS -->
                    <div class="modal fade" id="modal-lista-clientes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog  modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Lista de clientes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm table-bordered text-center" id="tablaImportarCliente">
                                        <thead>
                                            <tr>
                                                <th>Documento</th>
                                                <th>Datos</th>
                                                <th>Direccion</th>
                                                <th>Direccion 2</th>
                                                <th>Telefono</th>
                                                <th>Telefon 2</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyImportar">
                                            <!--  <tr id="trImportar"></tr> -->
                                            <tr id="trImportar" v-for="(item,index) in listaClientes">
                                                <!--  -->
                                                <td>{{item.documento}}</td>
                                                <td> {{item.datos}}</td>
                                                <td>{{item.direccion}}</td>
                                                <td>{{item.direccion2}}</td>
                                                <td>{{item.telefono}}</td>
                                                <td>{{item.telefono2}}</td>
                                                <td>{{item.email}}</td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <!--  <button id="agregarClientesImport" type="button" class="btn btn-primary">Guardar</button> -->
                                    <button @click="agregarListaImport" type="button" class="btn btn-primary">Guardar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL DE IMPORTAR XLS -->
                    <div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Importar Cliente con EXCEL</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form enctype='multipart/form-data'>
                                        <div class="mb-3">
                                            <p>Descargue el modelo en <span class="fw-bold">EXCEL</span> para importar, no
                                                modifique los campos en el archivo, <span class="fw-bold">click para
                                                    descargar</span> <a href="<?= URL::to("public/templateExcelClientes.xlsx") ?>">template.xlsx</a></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="col-form-label">Importar Excel:</label>

                                        </div>
                                        <input type="file" id="nuevoExcel" name="nuevoExcel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Rol</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Nombres</th>
                                        
                                        
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="usuario-add-bs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="myForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Rol</label>
                            <select name="rol" id="rol" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Número de documento</label>
                            <input type="text" name="ndoc" id="ndoc" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Clave</label>
                            <input type="text" name="clave" id="clave" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" id="nombres" class="form-control" required>
                        </div>
                        <!--
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tienda</label>
                            <select name="tienda" id="tiendau" class="form-control">
                                <option value="1">Tienda 435</option>
                                <option value="2">Tienda 426</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Rotativo</label>
                            <select name="rotativo" id="rotativou" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        -->

                        <div class="col-md-12 mb-3 text-center">
                        <button type="button" id="submitButton" class="btn btn-primary" onclick="saveUser()">Crear</button>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- EDITAR MODAL -->
<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
            </div>
            <div class="modal-body">
                <form id="clientesEditar">
                    <div class="row">
                        <input type="text" name="idCliente" id="idCliente" value="" hidden>
                        <div class="col-md-4 form-group">
                            <label>Rol</label>
                            <select name="rol" id="rol2" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="datosAgregar">Nombre</label>
                            <input type="text" class="form-control" id="datosEditar" name="datosEditar">
                        </div>
                        <div class="col-md-6">
                            <label for="doc" class="col-form-label">Número de documento</label>
                            <input type="text" class="form-control" id="doc" name="doc">
                        </div>
                        <div class="col-md-6">
                            <label for="usuariou" class="col-form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuariou" name="usuariou">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="claveu" class="col-form-label">Clave</label>
                            <input type="text" class="form-control" id="claveu" name="claveu">
                        </div>
                        <!---
                        <div class="col-md-6 ">
                            <label>Tienda</label>
                            <select name="tiendau" id="tiendau" class="form-control">
                                <option value="1">Tienda 435</option>
                                <option value="2">Tienda 426</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label>Rotativo</label>
                            <select name="rotativou" id="rotativou" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        --->
                        
                        <div class="col-md-6 ">
                            <label for="emailEditar" class="col-form-label">Email</label>
                            <input required type="text" class="form-control" id="emailEditar" name="emailEditar">
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button id="updateCliente" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>

    function saveUser() {
        // Obtener valores del formulario
        let rol = document.getElementById("rol").value;
        let ndoc = document.getElementById("ndoc").value.trim();
        let usuario = document.getElementById("usuario").value.trim();
        let clave = document.getElementById("clave").value.trim();
        let email = document.getElementById("email").value.trim();
        let nombres = document.getElementById("nombres").value.trim();
        let rotativo = 0;

        // Validaciones
        if (!/^\d+$/.test(ndoc)) {
            Swal.fire("Error", "El número de documento debe contener solo números.", "error");
            return;
        }
        if (usuario === "" || clave === "" || nombres === "") {
            Swal.fire("Error", "Usuario, clave y nombres son obligatorios.", "error");
            return;
        }

        // Crear objeto FormData para enviar los datos
        let formData = new FormData();
        formData.append("rol", rol);
        formData.append("ndoc", ndoc);
        formData.append("usuario", usuario);
        formData.append("clave", clave);
        formData.append("email", email);
        formData.append("nombres", nombres);
        formData.append("rotativo", rotativo);

        // Enviar datos al controlador con fetch()
        fetch("/arequipago/addUser", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", "Usuario creado correctamente.", "success").then(() => {
                    tabla_clientes.ajax.reload(null, true);

                    // Limpiar los campos del modal 🌍
                    document.getElementById("rol").value = ""; // 🌍 Limpiar campo
                    document.getElementById("ndoc").value = ""; // 🌍 Limpiar campo
                    document.getElementById("usuario").value = ""; // 🌍 Limpiar campo
                    document.getElementById("clave").value = ""; // 🌍 Limpiar campo
                    document.getElementById("email").value = ""; // 🌍 Limpiar campo
                    document.getElementById("nombres").value = ""; // 🌍 Limpiar campo

                    // Cerrar el modal 🌍
                    $('#usuario-add-bs').modal('hide'); // 🌍 Cerrar modal
                    
                });
            } else {
                Swal.fire("Error", data.message || "Hubo un problema al crear el usuario.", "error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        });
    }

    $(document).ready(function() {

        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/usuarios/render",
                method: "POST", //usamos el metodo POST
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [{
                    data: "usuario_id",
                    class: "text-center",
                },
                {
                    data: "nombre",
                    class: "text-center",
                },
                {
                    data: "usuario",
                    class: "text-center",
                },
                {
                    data: "email",
                    class: "text-center",
                },
                {
                    data: "nombres",
                    class: "text-center",
                },
                
                {

                    /* href="' + _URL + '/files/facturacion/xml/ */
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        return `<div class="text-center">
            <div class="btn-group btn-sm"><button  data-id="${Number(row.usuario_id)}" class="btn btn-sm btn-warning btnEditar"
            ><i class="fa fa-edit"></i> </button>
            <button btn-sm  data-id="${Number(row.usuario_id)}" class="btn btn-sm  btn-danger btnBorrar"><i class="fa fa-trash"></i> </button>
            </div></div>`;
                    },
                },
            ],
        });

        $("#tabla_clientes").on("click", ".btnEditar ", function(event) {
            $("#claveu").val("");
            $("#loader-menor").show();
            var table = $("#tabla_clientes").DataTable();
            var trid = $(this).closest("tr").attr("id");
            var id = $(this).data("id");
            $("#editarModal").modal("show");
            $("#editarModal")
                .find(".modal-title")
                .text("Editar Usuario N°" + id);
            $.ajax({
                url: _URL + "/ajs/usuarios/getOne",
                data: {
                    id: id,
                },
                type: "post",
                success: function(datos) {
                    $.ajax({
                        type: "POST",
                        url: _URL + "/ajs/getroles",
                        success: function(response) {
                            let data = JSON.parse(response);
                            let options = '';
                            $.each(data, function(i, d) {
                                options += `<option value="${d.rol_id}">${d.nombre}</option>`;
                            });
                            $('#rol2').html(options);
                            $("#loader-menor").hide();
                            let json = JSON.parse(datos)[0];
                            $("#rol2").val(json.id_rol);
                            $("#doc").val(json.num_doc);
                            $("#datosEditar").val(json.nombres);
                            $("#usuariou").val(json.usuario);
                            $("#emailEditar").val(json.email);
                            $("#nombresu").val(json.usuario);
                            
                            $("#rotativou").val(1);
                            $("#idCliente").val(id);
                            $("#trid").val(trid);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });

                },
            });
        });
        $("#updateCliente").click(function() {
            $("#loader-menor").show();
            let data = $("#clientesEditar").serializeArray();
            // Agregar manualmente la clave si el campo no está vacío
            let clave = $("#claveu").val();
            if (clave.trim() !== "") {
                data.push({ name: "clave", value: clave });
            }

            // Modificación: Asegurar que siempre se envíe el campo rotativou con el valor 0
            data.push({ name: "rotativou", value: 1 });

            let id = $("#idCliente").val();
           
            $.ajax({
                url: _URL + "/ajs/usuarios/editar",
                type: "POST",
                data: data,
                success: function(resp) {
                    $("#loader-menor").hide();
                    console.log(resp);
                    if (Array.isArray(data)) {
                        tabla_clientes.ajax.reload(null, false);
                        Swal.fire("¡Buen trabajo!", "Actualización exitosa", "success");
                        $("#editarModal").modal("hide");
                        $("body").removeClass("modal-open");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: JSON.parse(resp),
                        });
                    }
                },
            });
        });
        $("#tabla_clientes").on("click", ".btnBorrar", function() {
            var id = $(this).data("id");
            let idData = {
                value: id,
            };
            Swal.fire({
                title: "¿Deseas borrar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/usuarios/borrar",
                        type: "post",
                        data: idData,
                        success: function(resp) {
                            /* console.log(resp); */
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire(
                                "¡Buen trabajo!",
                                "Registro Borrado Exitosamente",
                                "success"
                            );
                        },
                    });
                } else {}
            });
        });

        $('#add-user').on('click', function() {
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/getroles",
                success: function(response) {
                    let data = JSON.parse(response);
                    let options = '';
                    $.each(data, function(i, d) {
                        options += `<option value="${d.rol_id}">${d.nombre}</option>`;
                    });
                    $('#rol').html(options);
                    $('#rol2').html(options);
                    $('#usuario-add-bs').modal('show');
                },
                error: function(response) {
                    console.log(response);
                }
            });
        });
        
    });
</script>