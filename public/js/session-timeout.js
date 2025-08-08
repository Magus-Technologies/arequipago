// public/js/session-timeout.js

document.addEventListener("DOMContentLoaded", function() {
    // Definir el tiempo de inactividad en segundos para el cliente.
    // Debe ser ligeramente menor que el timeout del servidor (5 minutos = 300 segundos).
    // Usaremos 4 minutos y 50 segundos (290 segundos) para que el cliente actúe primero.
    const INACTIVITY_TIMEOUT_SECONDS = 290;

    // Intervalo para enviar un ping al servidor y mantener la sesión viva.
    // Debe ser menor que el timeout del servidor para que el ping se envíe antes de que la sesión expire.
    // Usaremos 3 minutos (180 segundos).
    const PING_INTERVAL_SECONDS = 180;

    let lastActivityTime = new Date().getTime();
    let inactivityTimer;
    let pingTimer;

    // Función para reiniciar el temporizador de inactividad del cliente
    function resetInactivityTimer() {
        lastActivityTime = new Date().getTime();
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT_SECONDS * 1000);
        // console.log('Inactivity timer reset.'); // Para depuración
    }

    // Función para enviar un ping al servidor para mantener la sesión viva
    function sendKeepAlivePing() {
        // console.log('Sending keep-alive ping...'); // Para depuración
        // BASE_URL debe ser definido en header.php
        fetch(BASE_URL + '/keep-alive', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Para identificar la solicitud como AJAX
            },
            // No se necesita cuerpo para este ping
        })
        .then(response => response.json())
        .then(data => {
            // console.log('Keep-alive response:', data); // Para depuración
            if (data.status === 'error' && data.message === 'No active session.') {
                // Si el servidor indica que no hay sesión activa, redirigir inmediatamente
                logoutUser();
            }
        })
        .catch(error => {
            console.error('Error sending keep-alive ping:', error);
            // En caso de error de red, también podríamos considerar desloguear
            // o al menos no confiar en que la sesión se mantenga.
        });
    }

    // Función para desloguear al usuario (redirección del cliente)
    function logoutUser() {
        // console.log('Inactivity detected. Logging out...'); // Para depuración
        window.location.href = BASE_URL + '/logout?status=inactive'; // Redirigir al logout
    }

    // Eventos de actividad del usuario para reiniciar el temporizador
    const activityEvents = ['mousemove', 'keydown', 'click', 'scroll'];
    activityEvents.forEach(event => {
        document.addEventListener(event, resetInactivityTimer);
    });

    // Iniciar el temporizador de inactividad al cargar la página
    resetInactivityTimer();

    // Iniciar el temporizador de ping periódico al servidor
    pingTimer = setInterval(sendKeepAlivePing, PING_INTERVAL_SECONDS * 1000);

    // Limpiar los temporizadores al salir de la página (buena práctica)
    window.addEventListener('beforeunload', function() {
        clearTimeout(inactivityTimer);
        clearInterval(pingTimer);
    });
});