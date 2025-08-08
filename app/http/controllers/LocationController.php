<?php



require_once "app/models/Departamento.php";
require_once "app/models/Provincia.php";
require_once "app/models/Distrito.php";



class LocationController extends Controller{
    

    public function getDepartments(){
        

        $departamentoModel = new Departamento();

        $departments = $departamentoModel->obtenerDepartamentos();

     
        
        echo json_encode($departments);
        exit;
    }

 
    public function getProvincesByDepartment(){

        $iddepartamento = $_GET['iddepartamento'];
    
               
        if(empty($iddepartamento)|| !is_numeric($iddepartamento)){
          
           echo json_encode(['error' => 'ID de departamento inválido']);
           exit;
        }



        $provinciaModel = new Provincia();

        $provincias = $provinciaModel->obtenerProvincias($iddepartamento);

        
        echo json_encode($provincias);
        exit;
    }




    public function getDistritosByProvincias(){
        
       

        $idprovincia = $_GET['idprovincia'];
    
               
        if(empty($idprovincia)|| !is_numeric($idprovincia)){
          
           echo json_encode(['error' => 'ID de distrito inválido']);
           exit;
        }



        $distritoModel = new Distrito();

        $distritos = $distritoModel->obtenerDistritos($idprovincia);

        
        echo json_encode($distritos);
        exit;
    }
}


