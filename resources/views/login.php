<!DOCTYPE html>
<html lang="es">
<head>
    <title>AREQUIPAGO - ERP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= URL::to('public/login/images/icons/are.png') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= URL::to('public/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= URL::to('public/login/fonts/iconic/css/material-design-iconic-font.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/animate/animate.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= URL::to('public/login/vendor/css-hamburgers/hamburgers.min.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= URL::to('public/login/vendor/animsition/css/animsition.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/select2/select2.min.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= URL::to('public/login/vendor/daterangepicker/daterangepicker.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/css/util.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/css/main.css') ?>">
    <link rel="stylesheet" href="<?=URL::to('public/plugin/sweetalert2/sweetalert2.min.css')?>">
    <meta name="robots" content="noindex, follow">
    <script>
    const _URL='<?=URL::base()?>'; // Mover la definición de _URL al principio

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[\]]/g, '\\$&'); // Corregir esta línea para escapar corchetes
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
        var results = regex.exec(location.href);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    };

    // Verifica si se debe hacer logout local
    if (getUrlParameter('logout_local') === 'true') {
        localStorage.removeItem("_token");
        // Limpia el parámetro de la URL para evitar re-ejecución en recargas
        var newUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, document.title, newUrl);
    }
    </script>
    <style>
    @keyframes ldio-407auvblvok {
    0% { transform: rotate(0) }
                100% { transform: rotate(360deg) }
            }
            .ldio-407auvblvok div { box-sizing: border-box!important }
            .ldio-407auvblvok > div {
    position: absolute;
    width: 79.92px;
                height: 79.92px;
                top: 15.540000000000001px;
                left: 15.540000000000001px;
                border-radius: 50%;
                border: 8.88px solid #000;
                border-color: #626ed4 transparent #626ed4 transparent;
                animation: ldio-407auvblvok 1s linear infinite;
            }
            .ldio-407auvblvok > div:nth-child(2), .ldio-407auvblvok > div:nth-child(4) {width: 59.940000000000005px;
                height: 59.940000000000005px;
                top: 25.53px;
                left: 25.53px;
                animation: ldio-407auvblvok 1s linear infinite reverse;
            }
            .ldio-407auvblvok > div:nth-child(2) {border-color: transparent #02a499 transparent #02a499
            }
            .ldio-407auvblvok > div:nth-child(3) { border-color: transparent }
            .ldio-407auvblvok > div:nth-child(3) div {
    position: absolute;
    width: 100%;
    height: 100%;
    transform: rotate(45deg);
            }
            .ldio-407auvblvok > div:nth-child(3) div:before, .ldio-407auvblvok > div:nth-child(3) div:after {
    content: "";
    display: block;
    position: absolute;
    width: 8.88px;
                height: 8.88px;
                top: -8.88px;
                left: 26.64px;
                background: #626ed4;
                border-radius: 50%;
                box-shadow: 0 71.04px 0 0 #626ed4;
            }
            .ldio-407auvblvok > div:nth-child(3) div:after {
    left: -8.88px;
                top: 26.64px;
                box-shadow: 71.04px 0 0 0 #626ed4;
            }
            .ldio-407auvblvok > div:nth-child(4) { border-color: transparent; }
            .ldio-407auvblvok > div:nth-child(4) div {
    position: absolute;
    width: 100%;
    height: 100%;
    transform: rotate(45deg);
            }
            .ldio-407auvblvok > div:nth-child(4) div:before, .ldio-407auvblvok > div:nth-child(4) div:after {
    content: "";
    display: block;
    position: absolute;
    width: 8.88px;
                height: 8.88px;
                top: -8.88px;
                left: 16.650000000000002px;
                background: #02a499;
                border-radius: 50%;
                box-shadow: 0 51.06px 0 0 #02a499;
            }
            .ldio-407auvblvok > div:nth-child(4) div:after {
    left: -8.88px;
                top: 16.650000000000002px;
                box-shadow: 51.06px 0 0 0 #02a499;
            }
            .loadingio-spinner-double-ring-8kmkrab6ncg {
    width: 111px;
                height: 111px;
                display: inline-block;
                overflow: hidden;
                background: rgba(255, 255, 255, 0);
            }
            .ldio-407auvblvok {
    width: 100%;
    height: 100%;
    position: relative;
    transform: translateZ(0) scale(1);
                backface-visibility: hidden;
                transform-origin: 0 0; /* see note above */
            }
            .ldio-407auvblvok div { box-sizing: content-box; }
            /* generated by https://loading.io/ */
    </style>
    <style>
    #loader {
    background-color: #fcfcfc; /* Fondo negro semi-transparente */
    animation: 20s forwards;
    }#loader-menor{position: fixed;
                top: 0;
                left: 0;
                z-index: 9999;
                width: 100%;
                height: 100%;
                display: none;
                background-color: #ffffff96;
                line-height: 100vh;
                text-align: center;
            }
            #loader-init{
                position: fixed;
                top: 0;
                left: 0;
                z-index: 9999;
                width: 100%;
                height: 100%;
                background-color: #ffffff;
                line-height: 100vh;
                text-align: center;
            }
    </style>
</head>
<body>
<div id="loader-init">
    <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
        <div class="ldio-407auvblvok">
            <div></div>
            <div></div>
            <div><div></div></div>
            <div><div></div></div>
        </div>
    </div>
</div>
<div style="display: none" id="loader-menor">
    <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
        <div class="ldio-407auvblvok">
            <div></div>
            <div></div>
            <div><div></div></div>
            <div><div></div></div>
        </div>
    </div>
</div>
<div id="loader"><img class="loader-img" src="<?=URL::to('public/login/images/ani2arequipago.gif?v='.Tools::getToken(8))?>" alt="Cargando..." /></div>
<div class="limiter">
    <div class="container-login100" style="background-image: url('<?= URL::to('public/login/images/Recurso 1AQP GO.png') ?>');">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <form class="login100-form validate-form">
                <span class="login100-form-title p-b-49">
                <img src="<?= URL::to('public/login/images/Recurso 3AQP GO.png') ?>" style="max-width: 235px;">
                </span>
                <div class="wrap-input100 validate-input m-b-23"
                     data-validate="Se requiere usuario o correo electrónico">
                    <span class="label-input100">Usuario / Email</span>
                    <input class="input100" type="text" required name="user"
                           placeholder="Escribe tu usuario o correo electrónico">
                    <span class="focus-input100" data-symbol="&#xf206;"></span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Se requere contraseña">
                                        <span class="label-input100">Contraseña</span>
                                        <span class="focus-input100" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 16px;"></span> <!-- 🔹 Candado alineado correctamente -->
                    <input class="input100" type="password" required name="clave" placeholder="Escribe tu contraseña" id="passwordInput"
                           style="padding-left: 45px; padding-right: 35px; width: 100%;"><span class="focus-input100" data-symbol=""></span> <!-- 🔹 Ajustamos padding para que no se superpongan los iconos -->
                    <span id="togglePassword" style="position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="fas fa-eye"></i> <!-- 🔹 Ojito alineado con el input -->
                                        </span>
                                                                                </div>
                <div class="wrap-input100" data-validate="">
                    <span class="label-input100">Seleccionar Usuario</span>
                    <select class="input100" name="sucursal" id="sucursal" required>
                        <option value="">Seleccione un usuario</option>
                        <option value="3">Director</option>
                        <option value="1">Administrador</option>
                        <option value="2">Asesor</option>
                    </select>
                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                </div>
                <div class="text-right p-t-8 p-b-31">
                    <a href="#">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button type="submit" class="login100-form-btn">Ingresar
                        </button>
                    </div>
                </div>
                <div class="txt1 text-center p-t-54 p-b-20"><span>Desarrollado por:<br></span>
                    <a href="https://magustechnologies.com/" target="_blank"><img class="magus"
                                                                                    src="<?= URL::to('public/login/images/magus.png') ?>"
                                                                                    style="max-width: 150px"></a>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="dropDownSelect1"></div>
<script src="<?= URL::to('public/login/vendor/jquery/jquery-3.2.1.min.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/animsition/js/animsition.min.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/bootstrap/js/popper.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/select2/select2.min.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/daterangepicker/moment.min.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= URL::to('public/login/vendor/countdowntime/countdowntime.js') ?>"></script>
<script src="<?= URL::to('public/login/js/main.js?v=2') ?>"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
(function verificador() {
    if (localStorage.getItem("_token")){
        $.ajax({
                url: _URL+"/ajs/verificador/token",
                type: "POST",
                data: {
                    token:localStorage.getItem("_token"),
                    s:false
                },
                success(resp){
                    console.log(resp);
                    resp=JSON.parse(resp);
                    if (resp.res){
                        $("#loader-init").hide();
                        location.href=_URL
                    }else{
                        localStorage.removeItem("_token")
                        $("#loader-init").hide();
                    }
                }
            })
        }else{
        $("#loader-init").hide();
    }
})()
    $(document).ready(function(){
        $("form").submit(function (evt){
            evt.preventDefault();
            $("#loader-menor").show();
            $.ajax({
                type: "POST",
                url: _URL+"/login",
                data: $("form").serialize(),
                success: function (resp) {
                    $("#loader-menor").hide();
                    //console.log(resp);
                    resp=JSON.parse(resp);
                    if (resp.res){
                        localStorage.setItem("_token",resp.token)
                        location.href=_URL
                    }else{
                        Swal.fire({icon: 'warning',
                                    title: resp.msg})
                    }
                },
                error(){
                    $("#loader-menor").hide();
                }
            });
        });
        document.getElementById("togglePassword").addEventListener("click", function() {
            let passwordInput = document.getElementById("passwordInput");
            let icon = this.querySelector("i");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // 🔹 Cambia el icono a "ojito cerrado"
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye"); // 🔹 Cambia el icono a "ojito abierto"
            }
        });
    })
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</html>
