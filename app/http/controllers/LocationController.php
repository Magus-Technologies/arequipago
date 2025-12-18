<?php



require_once "app/models/Departamento.php";
require_once "app/models/Provincia.php";
require_once "app/models/Distrito.php";



class LocationController extends Controller{
    

    public function getDepartments(){
        $departamentoModel = new Departamento();

        // Verificar si se solicita todos los departamentos (para uso futuro)
        // Ejemplo: /cargardireccion?todos=1
        $todos = isset($_GET['todos']) && $_GET['todos'] == '1';

        if ($todos) {
            $departments = $departamentoModel->obtenerTodosDepartamentos();
        } else {
            // Por defecto, solo Arequipa, La Libertad y Lima
            $departments = $departamentoModel->obtenerDepartamentos();
        }
        
        header('Content-Type: application/json');
        echo json_encode($departments);
        exit;
    }

 
    public function getProvincesByDepartment(){

        $iddepartamento = $_GET['iddepartamento'];
    
               
        if(empty($iddepartamento)|| !is_numeric($iddepartamento)){
           header('Content-Type: application/json');
           echo json_encode(['error' => 'ID de departamento inválido']);
           exit;
        }



        $provinciaModel = new Provincia();

        $provincias = $provinciaModel->obtenerProvincias($iddepartamento);

        header('Content-Type: application/json');
        echo json_encode($provincias);
        exit;
    }




    public function getDistritosByProvincias(){
        
       

        $idprovincia = $_GET['idprovincia'];
    
               
        if(empty($idprovincia)|| !is_numeric($idprovincia)){
           header('Content-Type: application/json');
           echo json_encode(['error' => 'ID de distrito inválido']);
           exit;
        }



        $distritoModel = new Distrito();

        $distritos = $distritoModel->obtenerDistritos($idprovincia);

        header('Content-Type: application/json');
        echo json_encode($distritos);
        exit;
    }
}


