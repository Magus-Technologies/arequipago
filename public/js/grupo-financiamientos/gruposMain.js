/**
 * gruposMain.js
 * Módulo principal de inicialización para grupos de financiamiento
 */

/**
 * Inicializar todo el módulo de grupos de financiamiento
 */
function inicializarGruposFinanciamiento() {
    // 1. Inicializar DataTable
    inicializarTablaGrupos();
    
    // 2. Configurar event handlers
    setupFormularioPrincipal();
    setupBotonesAccion();
    setupModalVariantes();
    setupTabs();
    
    // 3. Configurar controles UI
    manejarCheckboxVehicular();
    manejarCheckboxYango();
    manejarCamposComision();
    manejarCampoPenalizacionMora();
    configurarCheckboxEstado();
    
    // 4. Configurar dropdown global y contratos
    configurarDropdownGlobal();
    
    // 5. Estado inicial
    toggleEstadoGrupo();
}

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    inicializarGruposFinanciamiento();
});
