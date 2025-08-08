<?php

require_once "app/models/Usuario.php";

class UsuarioController extends Controller
{
    private $usuario;

    public function __construct(){
        $this->usuario = new Usuario();
    }

    public function login(){
        $this->usuario->setUsuario($_POST['user']);
        $this->usuario->setClave($_POST['clave']);
        $this->usuario->setUserRol($_POST['sucursal']);
        return json_encode($this->usuario->login());
    }
    public function logout(){
        session_destroy();   // Destruye la sesión
        header('Location: ' . URL::to('/login'));
        exit();
    }

}