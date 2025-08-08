<?php 

///$(document).on('click', '#Crear', function() {
   // $('#ModalCrear').modal('show');
/*});

$(document).on('click', '#agregar_nombres', function() {
    $('#ModalAgregarNombre').modal('show');
});*/
?>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Inventario></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .container-custom {
            max-width: 1200px;
            /* Ajustado para un tamaño más realista en lugar de 900rem */
            margin-top: 20px;
            padding: 20px;
            background-color: #F2F2F2;
            /* Fondo claro para hacer que los elementos resalten */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .form-section {
            border: 2px solid #D7D7D7;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #FAFAFA;
            /* Fondo oscuro para contrastar con el contenedor principal */
            color: #000000;
            font-weight: normal;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            }

            .form-section input,
            .form-section select,
            .form-section textarea {
                border-radius: 8px;
                background-color: #EEEFEF;
                border: 1px solid #CED4DA; /* Borde gris suave */
                padding: 5px 1px; /* Espaciado interno */
                font-size: 1rem; /* Tamaño de fuente */
                font-family: 'Roboto', sans-serif;
                padding-left: 9px;
              
            }    

            button{
                background-color: #000000;
                border-radius:8px;
                border: none;
                color: white;
                font-family: 'Corbel' sans-serif;
                font-size: 14px;
                padding: 6px;
                padding-left: 11px;  /* Espacio en el lado izquierdo */
                padding-right: 11px; /* Espacio en el lado derecho */
                border-radius: 20px;
            }
            .button-primary{
                padding: 7px 9px;
                font-size: 15px;
                font-weight: bold;
                background-color: #000000; 
                color: #F2E74B;
                border-radius: 5px;
            }

            .btn-primary {
                background-color: #000000;
                color: #F2E74B;
                border-color: #6c757d;
            }

            .btn-primary:hover {
                background-color: white;
                color: #000000;
                border-color: #000000;
            }

            select.form-select {
                width: 58%;
                padding: 0.375rem 0.75rem;
                box-sizing: border-box;               
            }   
            input[type="date"]{
                width: 58%;
                padding: 0.375rem 0.75rem;
                box-sizing: border-box;
            }
            
        </style>
    </head>

    <body>
       <!---- 
        <div id="DatosInventario" class="container container-customm"> 

        <br>    

        <h2>Registrar Producto en Inventario</h2> <br>

        <form action="inventario_registro.php" method="POST">
           <div class="form-section"> 

           <div class="row mb-3">
                <div class="col-sm-4">
                    <label for="nombre_producto">Nombre del Producto</label>
                </div>
                    <div class="col-sm-8">
                        <input type="text" id="nombre_producto" name="nombre_producto" required>
                    </div>
            </div>

            <div class="row mb-3">
            
                <div class="col-sm-4">
            
                    <label for="Ltipo_producto">Tipo de producto:</label>

                </div>

                <div class="col-sm-4">
                    <select name="tipo_producto" id="tipo_producto" required>
                        <option value="fisico">Físico</option>
                        <option value="intangible">Intangible</option>
                    </select>
                    <button data-bs-toggle="modal" data-bs-target="#exampleModal">Nuevo tipo de producto</button>
                </div>    
           
            </div>

            <div class="row mb-3">
                <div class="col-sm-4">
                    <label for="codigo_producto">Código del Producto (Generado o Escaneado)</label>
                </div>            

                <div class="col-sm-4">
                    <input type="text" id="codigo_producto" required placeholder="Escanear o ingresar código"/>    
                </div>    
            </div> 
        
            <div class="row mb-3">        
                <div class="col-sm-4">    
                    <label for="cantidad">Cantidad</label>
                </div> 
                <div class="col-sm-4">   
                    <input type="number" id="intcantidad" name="intcantidad" required>
                </div>    
            </div>    
        
            <div class="row mb-3">
                <div class="col-sm-4">
                    <label for="categoria_producto_label" id="categoria_producto_label">Categoría</label>
                </div>
                <div class="col-sm-4">
                    <select name="categoria_producto" id="categoria_producto" class="form-select" onchange="toggleFechaVencimiento()">
                        <option value="seleccionar_categoría">Seleccionar Categoría</option>
                        <option value="soat">SOAT</option>
                        <option value="seguro">Seguro</option>
                        <option value="llantas">Llantas</option>
                        <option value="aceites">Aceites</option>
                    </select>
                </div>    
            </div>    

            <div class="row mb-3" id="fecha_vencimiento_wrapper">
                <div class="col-sm-4">
                        <label id="lfecha_vencimiento" style="display: none">Fecha de Vencimiento</label>
            </div> 
            <div class="col-sm-4">
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" style="display: none;">
                </div>
            </div>  <!---end FORM Section----->

            <!----- 
                           
            <button type="submit" class="button-primary">Registrar</button>
           </div> 
        </form>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Nuevo Tipo de Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <label class="mb-2">Agregar nuevo tipo de Producto:</label><br>
                            <input type="text" class="fomr-control" placeholder="Ingrese tipo de producto">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>


        ------>



    <button type="button" id="Crear" class="btn btn-primary">
        <span class="fa fa-plus"></span> Agregar
    </button>

    <div id="ModalCrear" class="modal fade" role="dialog"> 
    	<div class="modal-dialog">
    		<div class="modal-content">
    			<div class="modal-header"> 
    				<h4 class="modal-tittle">Crear</h4> 
    			</div> 
    			<form class="form-horizontal" role="form" id="form-crear">
    				<div class="modal-body"> 
    					<div class="row form-group col-md-12">
    						<label for="crear_codigo" class="control-label col-sm-2 col-xs-12">Código: </label>
    						<div class="col-sm-4 col-xs-6">
    							<input type="number" class="form-control" id="crear_codigo" name="crear_codigo">
    						</div>
    					</div>  
    					<div class="row form-group col-md-12">
    						<label for="crear_nombre" class="control-label col-sm-2 col-xs-12">Nombre: </label>
    						<div class="col-sm-6 col-xs-10 selectContainer">
    							<select id="crear_nombre" name="crear_nombre" class="form-control" style="width: 100%;">
                                        <option value="0">Seleccione...</option>
                                        <option value="1">Clorace</option>
                                        <option value="2">Miovit</option>
    							</select>
    						</div>
    						<div class="col-sm-2 col-xs-2">
    							<button type="button" class="btn btn-primary" id="agregar_nombres">
    								<span class="fa fa-plus"></span>
                        			<span class="hidden-xs"> Agregar Items</span> 
    							</button>
    						</div>
    					</div> 
    					<div class="row form-group col-md-12">
    						<label for="crear_formas_farmaceuticas" class="control-label col-sm-2 col-xs-12">Forma Farmacéutica: </label>
    						<div class="col-sm-6 col-xs-10 selectContainer">
    							<select id="crear_formas_farmaceuticas" class="form-control" name="crear_formas_farmaceuticas" style="width: 100%;">
                                        <option value="0">Seleccione...</option>
                                        <option value="1">Inyección</option>
                                        <option value="2">Jarabe</option>
    							</select>
    						</div>
    						<div class="col-sm-1 col-xs-2">
    							<button type="button" class="btn btn-primary" id="agregar_formas">
    								<span class="fa fa-plus"></span>
                        			<span class="hidden-xs"> Agregar Items</span> 
    							</button>
    						</div>
    					</div>
    					<div class="row form-group col-md-12">
    						<label for="crear_presentacion" class="control-label col-sm-2 col-xs-12">Presentación: </label>
    						<div class="col-sm-3 col-xs-5">
    							<input type="number" class="form-control" id="crear_presentacion" name="crear_presentacion">
    						</div> 
    						<div class="col-sm-4 col-xs-7 selectContainer">
    							<select id="crear_unidad_de_medicion_p" class="form-control" name="crear_unidad_de_medicion_p" style="width: 100%;">
                                        <option value="0">Seleccione...</option>
                                        <option value="1">(und) Unidad</option>
                                        <option value="2">(ml) Mililitro</option>
                                        <option value="2">(mg) Miligramo</option>
    							</select>
    						</div>
    					</div>
    					<div class="row form-group col-md-12">
    						<label for="crear_unidad_teorica" class="control-label col-sm-2 col-xs-12">Unidad Teórica: </label>
    						<div class="col-sm-5 col-xs-5">
    							<input type="number" class="form-control" id="crear_unidad_teorica" name="crear_unidad_teorica">
    						</div> 
    						<div class="col-sm-4 col-xs-7 selectContainer">
    							<select id="crear_unidad_de_medicion_u" class="form-control" name="crear_unidad_de_medicion_u" style="width: 100%;">
                                        <option value="0">Seleccione...</option>
                                        <option value="1">(und) Unidad</option>
                                        <option value="2">(ml) Mililitro</option>
                                        <option value="2">(mg) Miligramo</option>
    							</select>
    						</div>
    					</div>
    					<div class="row form-group col-md-12">
    						<label for="crear_velocidad" class="control-label col-sm-2 col-xs-12">Velocidad del Producto: </label>
    						<div class="col-sm-3 col-xs-5">
    							<input type="number" class="form-control" id="crear_velocidad" name="crear_velocidad">
    						</div> 
    						<label class="col-sm-5 col-xs-7">
    							<h4>
    								<sup id="crear_unidad_de_medicion_v_u" name="crear_unidad_de_medicion_v_u">
    									
    								</sup>
    								/
    								<sub id="crear_unidad_de_medicion_v_t" name="crear_unidad_de_medicion_v_t">
    									min
    								</sub>
    							</h4>
    						</label>
    					</div>
    					<div class="row form-group col-md-12">
    						<label class="control-label col-sm-2 col-xs-12">Tiempo Teórico: </label>
    						<label class="col-sm-7 col-xs-7">
    							<h4>
    								<sub id="crear_tiempo_teorico" name="crear_tiempo_teorico">
    									
    								</sub>
    							</h4>
    						</label>
    					</div>
    					<div class="row form-group col-md-12">
    						<label for="crear_linea_de_produccion" class="control-label col-sm-2">Linea de Producción:</label>
    						<div class="col-sm-8 selectContainer">
    							<select id="crear_linea_de_produccion" class="form-control" name="crear_linea_de_produccion" style="width: 100%;">
                                        <option value="0">Seleccione...</option>
                                        <option value="1">Liquidos Esteriles</option>
                                        <option value="2">Liquidos No Esteriles</option>
                                        <option value="2">Solidos</option>
    							</select>
    						</div>
    					</div>
    				</div>
    				<div class="modal-footer">
    					<button type="button" class="btn btn-default" data-dismiss="modal">
    						<span class="glyphicon glyphicon-remove"></span>
                        	<span class="hidden-xs"> Cerrar</span> 
    					</button>
    					<button type="button" id="Guardar" name="Guardar" class="btn btn-primary">
    						<span class="fa fa-save"></span>
                        	<span class="hidden-xs"> Guardar</span> 
    					</button>
    				</div>
    			</form>
    		</div>
    	</div>
    </div>

    <div id="ModalAgregarNombre" class="modal fade" role="dialog"> 
    	<div class="modal-dialog">
    		<div class="modal-content">
    			<div class="modal-header"> 
    				<h4 class="modal-tittle">Agregar</h4>
    			</div> 
    			<form class="form-horizontal" role="form" id="form-agregar">
    				<div class="modal-body"> 
    					<div class="form-group col-md-12">
    						<label for="agregar_nombre" class="control-label col-sm-4">Nombre: </label>
    						<div class="col-sm-8">
    							<input type="text" class="form-control" id="agregar_nombre" name="agregar_nombre">
    						</div>
    					</div> 
    				</div>
    				<div class="modal-footer">
    					<button type="button" class="btn btn-default" data-dismiss="modal">
    						<span class="glyphicon glyphicon-remove"></span><span class="hidden-xs"> Cerrar</span>
    					</button>
    					<button type="button" id="GuardarNombre" name="GuardarNombre" class="btn btn-primary">
    						<span class="fa fa-save"></span><span class="hidden-xs"> Guardar</span>
                          
    					</button>
    				</div>
    			</form>
    		</div>
    	</div>
    </div>   


        <button data-bs-toggle="modal" data-bs-target="#modal-add-prod" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Producto</button>


        <div class="modal fade" id="modal-add-prod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="agregarProd">
                    <div class="modal-body">
                        <!-----<div class="row">
                            <div class="form-group col-md-8 mt-2">
                                <label>Descripción de producto</label>
                                <input v-model="reg.descripcicon" required type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Codigo</label>
                                <input v-model="reg.codigo" required type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-3 mt-2">
                                <label>Precio Venta</label>
                                <input v-model="reg.precio" @keypress="onlyNumber" required value="0" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Costo</label>
                                <input v-model="reg.costo" @keypress="onlyNumber" required value="0" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Cantidad</label>
                                <input v-model="reg.cantidad" @keypress="onlyNumber" required type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Cod. Sunat</label>
                                <input v-model="reg.codSunat" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Afecto ICBP</label>
                                <select v-model="reg.afecto" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Precio por Mayor</label>
                                <input v-model="reg.precioMayor" @keypress="onlyNumber" required value="0" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label>Precio por Menor</label>
                                <input v-model="reg.precioMenor" @keypress="onlyNumber" required value="0" type="text" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label><span class="rojo"></span>RUC: </label>
                                        <div class="input-group">
                                            <input @change="ChangeconsultarDocRUC" v-model="reg.ruc" required @keypress="onlyNumber" type="text" class="form-control" maxlength="11">
                                            <div class="input-group-prepend">
                                                <button type="button" @click="consultarDocRUC" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Razon Social: </label>
                                        <input v-model="reg.razon" required type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>---->


                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="nombre_producto">Nombre del Producto</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="nombre_producto" name="nombre_producto" class="InputNProduc" required class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                        
                            <div class="col-sm-4">
                        
                                <label for="Ltipo_producto">Tipo de producto:</label>

                            </div>

                            <div class="col-sm-8 d-flex" >
                                <select name="tipo_producto" id="tipo_producto" required class="form-select me-2">
                                    <option value="fisico">Físico</option>
                                    <option value="intangible">Intangible</option>
                                </select>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary">Nuevo tipo de producto</button>
                            </div>    

                           
           
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="codigo_producto">Código del Producto (Generado o Escaneado)</label>
                            </div>            

                            <div class="col-sm-4">
                                <input type="text" id="codigo_producto" required placeholder="Escanear o ingresar código" class="form-contro"/>    
                            </div>    
                        </div> 
        
                        <div class="row mb-3">        
                            <div class="col-sm-4">    
                                <label for="cantidad">Cantidad</label>
                            </div> 
                            <div class="col-sm-4">   
                                <input type="number" id="intcantidad" name="intcantidad" required>
                            </div>    
                        </div>    
        
                        <div class="row mb-1">
                            <div class="col-sm-4">
                                <label for="categoria_producto_label" id="categoria_producto_label">Categoría</label>
                            </div>
                            <div class="col-sm-4">
                                <select name="categoria_producto" id="categoria_producto" class="form-select">
                                    <option value="seleccionar_categoría">Seleccionar Categoría</option>
                                    <option value="soat">SOAT</option>
                                    <option value="seguro">Seguro</option>
                                    <option value="llantas">Llantas</option>
                                    <option value="aceites">Aceites</option>
                                </select>
                            </div>    
                        </div>    

                        <div class="row mb-3" id="fecha_vencimiento_wrapper">
                            <div class="col-sm-4">
                                    <label id="lfecha_vencimiento" style="display: none">Fecha de Vencimiento</label>
                            </div> 
                            <div class="col-sm-4">
                                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" style="display: none;">
                            </div>   
                        </div>

                        <div class="row mb-3" id="ruc">
                            <div class="form-group col-sm-4">
                                <label><span class="rojo"></span>RUC: </label>
                            </div>
                            <div class="form-group col-sm-8 d-flex">
                                
                                    <input @change="ChangeconsultarDocRUC" v-model="reg.ruc" required @keypress="onlyNumber" type="text" class="form-control me-2" maxlength="11" style="width: 193px;" >
                                    
                                    <button type="button" @click="consultarDocRUC" class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                    
                                
                            </div>
                        </div>

                        <div class="row mb-3" id="razonsocial">
                            <div class="form-group col-sm-4">
                                <label>Razon Social: </label>
                            </div>
                            <div class="form-group col-sm-4">
                                <input v-model="reg.razon" required type="text" class="form-control">
                            </div>
                        </div>


                        
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>                 
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Nuevo Tipo de Producto</h5>
                            <button type="button" class="btn-close" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <label class="mb-2">Agregar nuevo tipo de Producto:</label><br>
                            <input type="text" class="fomr-control" placeholder="Ingrese tipo de producto">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="CancelarAgregarP">Cancelar</button>
                            <button type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>                     
                </div>              
     </div>
            
        <script>
            document.getElementById('tipo_producto').addEventListener('change', function(){
                var tipo =this.value;
                if (tipo == 'intangible') {
                    document.getElementById('codigo_producto').style.display = 'none';
                } else {
                    document.getElementById('codigo_producto').style.display = 'block';
                }
            });

            document.getElementById('categoria_producto').addEventListener('change',function(){
                var categoria =this.value;
                if(categoria == 'soat' || categoria == 'seguro'){
                    document.getElementById('fecha_vencimiento').style.display= 'block';
                    document.getElementById('lfecha_vencimiento').style.display= 'block';
                } else{
                    document.getElementById('fecha_vencimiento').style.display= 'none';
                    document.getElementById('lfecha_vencimiento').style.display= 'none';
                }
            });
        </script>
    </body>



    
