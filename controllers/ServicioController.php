<?php 

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        session_start();
        isAdmin();

        $servicios = Servicio::all();
        
        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router) {
        session_start();
        isAdmin();
        $servicio = new Servicio;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Lamamos la funcion que sincroniza el objeto que tenemos en memoria con los datos que enviemos, en este caso le indicamos que lo sincronice con los datos del POST
            $servicio->sincronizar($_POST);
            
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header('location: /servicios');
            }

        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        session_start();
        isAdmin();
        //Validamos que desde el GET recibamos un valor numerico
        if (!is_numeric($_GET['id'])) return;

        $servicio = Servicio::find($_GET['id']);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Sincronizamos el objeto de servicio que tenemos en memoria con los que envia el usuario por POST
            $servicio->sincronizar($_POST);

            //Validamos
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header('location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar() {
        session_start();
        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Guardamos el ID del servicio que se envia por POST en una variable
            $id = $_POST['id'];
            //Buscamos ese ID en la tabla de servicios y nos retorna el objeto sincronizado con el registro de la DB
            $servicio = Servicio::find($id);
            //Eliminamos el registro
            $servicio->eliminar();
            header('location: /servicios');
        }
    }
}