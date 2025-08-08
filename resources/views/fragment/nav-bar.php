<style>
    /* Add your custom styles here */
    .navbar-nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
    }

    .nav-item a i {
        color: #ff00ff;
    }

    .nav-item a {
        color: white;
        /* font-weight: bold; */

    }

    .nav-item ul li a {
        color: black;
        /* font-weight: bold; */

    }

    .panel-palpitante {
        background-color: white;
        animation-name: colorpalpitante;
        animation-duration: 1.5s;
        animation-iteration-count: infinite;
    }


    @keyframes colorpalpitante {
        from {
            background-color: #eed8fc;
            color: white
        }

        to {
            background-color: #626ed4;
            color: white
        }
    }

    .texto-palpitante {
        color: #000000;
        animation-name: colorpalpitante2;
        animation-duration: 1.5s;
        animation-iteration-count: infinite;
    }

    @keyframes colorpalpitante2 {
        from {
            color: white;
        }

        to {
            color: #ffffff;
        }
    }
</style>
<?php
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();

// }
//   $id_role = $_SESSION['rol'];
// var_dump($_SESSION); 

// echo "el rol es". $id_role;
// 

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Aseguramos que la sesión está iniciada
}

// Verificamos si el usuario tiene sesión activa
$id_rol = $_SESSION['id_rol'] ?? null;

?>
<nav class="navbar navbar-expand-lg" style="background-color: #8B8C64;">
    <div class="container" style="max-width: 1440px;">
        <a class="navbar-brand" href="#"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="color: #4e58aa;">
            <i class="fa fa-align-justify" style="color: #4e58aa;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

            
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
                  
                        <li class="nav-item">
                            <a class="nav-link" href="/arequipago/">
                                <i class="ti-home"></i>
                                DASHBOARD
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti-package"></i>
                                    FACTURACIÓN
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="/arequipago/ventas">Ventas</a></li>
                                    <li><a class="dropdown-item" href="/arequipago/guias/remision">Guías Remisión</a></li>
                                    <li><a class="dropdown-item" href="/arequipago/nota/electronica/lista">Notas Electrónicas</a></li>
                                </ul>
                            </li>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdownPagos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti-wallet"></i> <!-- Icono relacionado con pagos -->
                                  PAGOS   
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownPagos">
                                <li><a class="dropdown-item" href="/arequipago/nuevo-pago">Pagos Inscripción</a></li>
                                <li><a class="dropdown-item" href="/arequipago/pago-financiamiento">Pagos Financiamiento</a></li>
                            </ul>
                        </li>
                    
                        <!----<li class="nav-item">
                            <a class="nav-link" href="/arequipago/registrar-inventario">
                                <i class="ti-package"></i>
                                INVENTARIO    
                            </a>
                        </li>---->

                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/lencika/cotizaciones">
                                <i class="fa fa-align-justify"></i>
                                COTIZACIONES
                            </a>
                        </li> -->
                   
                        <li class="nav-item">
                            <a class="nav-link" href="/arequipago/regisconductor">
                                <i class="ti-home"></i>
                                REGISTRO DE CONDUCTOR
                            </a>
                        </li>
            
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/cobranzas">
                                <i class="fa fa-money-bill"></i>
                                CUENTAS POR COBRAR
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/pagos">
                                <i class="fa fa-money-bill"></i>
                                CUENTAS POR PAGAR</a>
                        </li> -->
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti-package"></i>
                                CAJAS
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="/lencika/cajaRegistros">Registro</a></li>
                                <li><a class="dropdown-item" href="/lencika/caja/flujo">Caja Chica</a></li>
                            </ul>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/lencika/compras">
                                <i class="ti-calendar"></i>
                                COMPRAS
                            </a>
                        </li> -->
                        <!-- </li> -->
                        <?php if ($id_rol == 1 || $id_rol == 3): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="almacenDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti-view-grid"></i>
                                    ALMACÉN
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="almacenDropdownMenu">
                                    <li><a class="dropdown-item" href="/arequipago/almacen/productos">KARDEX</a></li> <!-- 🔹 Nueva opción KARDEX -->
                                    <li><a class="dropdown-item" href="/arequipago/reporte-almacen">Reportes</a></li> <!-- 🔹 Nueva opción REPORTES -->
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/arequipago/conductores">
                                <i class="ti-user"></i>
                                CONDUCTORES
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/arequipago/ver-clientes">
                                <i class="ti-user"></i>
                                CLIENTES
                            </a>
                        </li>

                        <?php if ($id_rol == 3): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="usuarios" style="cursor: pointer;">
                                    <i class="ti-user"></i>
                                    USUARIOS
                                </a>
                            </li>
                        <?php endif; ?>   

                        <li class="nav-item">
                            <a class="nav-link" href="/arequipago/module-financiamiento">
                                <i class="ti-money"></i>
                                CREDI GO
                            </a>
                        </li>

                        <?php if ($id_rol == 1 || $id_rol == 3): ?>
                            <li class="nav-item">
                                <a class="nav-link" onclick=""href="/arequipago/grupo-financiamiento">
                                    <i class="fas fa-piggy-bank"></i>
                                    GRUPOS FINANCIAMIENTO
                                </a>
                            </li>

                            <?php if ($id_rol == 3): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/arequipago/comisiones">
                                        <i class="ti-stats-up"></i>
                                        COMISIONES
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="/arequipago/mostrarReportes">
                                    <i class="ti-bar-chart"></i>
                                    REPORTES
                                </a>
                            </li>
                        <?php endif; ?> 
                </ul>
                        <!--- <li class="nav-item panel-palpitante">
                            <a class="nav-link texto-palpitante" href="/almacen/intercambio/productos">
                                <i class="ti-calendar"></i>
                                Intercambio Productos
                            </a>
                        </li> -->
                      

                    
                   
                    
                    <!-- <li class="nav-item panel-palpitante" >
                    <a class="nav-link texto-palpitante" href="#">
                    <i class="ti-calendar"></i>
                        Intercambio Productos
                    </a>
                    </li> -->
                
            
            <!-- fin del nav de administrador -->

            <!-- inicio de nav del vendedor    -->
                                    
            <!-- fin del nav del vendedor -->

            <!--
            
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
                    <li class="nav-item">
                        <a class="nav-link" href="/lencika/cotizaciones">
                            <i class="fa fa-align-justify"></i>
                            COTIZACIONES
                        </a>
                    </li> 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti-package"></i>
                            FACTURACIÓN
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="/ventas">Ventas</a></li>
                            <li><a class="dropdown-item" href="/guias/remision">Guías Remisión</a></li>
                            <li><a class="dropdown-item" href="/nota/electronica/lista">Notas Electrónicas</a></li>
                        </ul>
                    </li>
                   
                        <li class="nav-item">
                            <a class="nav-link" href="/clientes">
                                <i class="ti-calendar"></i>
                                CLIENTES
                            </a>
                        </li>
                    

                </ul>
    
           fin de nav de cajera 

         inicio de nav de contador 
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100 text-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/ventas">
                            <i class="ti-calendar"></i>
                            Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/guias/remision">
                            <i class="ti-calendar"></i>
                            Guías Remisión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/clientes">
                            <i class="ti-calendar"></i>
                            Notas Electrónicas
                        </a>
                    </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti-package"></i>
                            FACTURACIÓN
                        </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="/ventas">Ventas</a></li>
                        <li><a class="dropdown-item" href="/guias/remision">Guías Remisión</a></li>
                        <li><a class="dropdown-item" href="/nota/electronica/lista">Notas Electrónicas</a></li>
                    </ul>
                </li> 
                </ul>
            
                 fin de nav de contador 



               inicio de nav de almacen 
            
            




                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
                        <li class="nav-item">
                            <a class="nav-link" href="/lencika/almacen/productos">
                                <i class="ti-calendar"></i>
                                Kardex
                            </a>
                        </li>
                        <li class="nav-item panel-palpitante">
                            <a class="nav-link texto-palpitante" href="/almacen/intercambio/productos">
                                <i class="ti-calendar"></i>
                                Intercambio Productos
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti-view-grid"></i>
                            ALMACÉN
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="/almacen/productos">Kardex</a></li>
                            <li><a class="dropdown-item" href="/almacen/intercambio/productos">Intercambio Productos</a></li>
                            
                        </ul>
                    </li>     
                        </ul>
                    

                    -->





                <!-- fin de nav de almacen -->
            </div>


        </div>
    <div>
</nav>

    <script>
    function mostrarAlerta() {
        Swal.fire({
            icon: 'info',
            title: 'Función en desarrollo',
            text: 'Esta función aún está en desarrollo. ¡Pronto estará disponible!',
            confirmButtonText: 'Entendido'
        });
    }
    </script>