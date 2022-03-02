<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    
    public static function index(Router $router) {
        session_start();

        isAdmin();

        //La primera vez que se carga la pagina "$fecha" obtiene la fecha actual del servidor y la formateamos con el formato que tiene la DB asi la pasamos a la query y por defecto se muestren los servicios de la fecha actual. Tambien la pasamos a la vista para mostrarla en el campo de fecha.
        //Cuando el usuario cambia la fecha se recarga la pagina y nos la envia por GET, de esta forma la asignamos a una variable para poder usarla y filtrar las citas por la fecha seleccionada.
        //debuguear($_GET['fecha']);
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        //Validamos que la fecha del GET de la URL sea correcta para evitar que se modifique e ingresen una fecha que no exista, para esto usamos la funcion "checkdate($mes,$dia,$año)". Para poder usar esta funcion separamos el dia, año y mes del string de fecha para poder pasarselos a la funcion de forma separada.
        $fechas = explode('-', $fecha);
        //Consultamos si la fecha es incorrecta entonces direccionamos a la pagina 404, de lo contrario mostramos la cita de la fecha
        if (!checkdate( $fechas[1], $fechas[2], $fechas[0] )) {
            header('location: /404');
        }


        
        //Consultar la base de datos
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasServicios ";
        $consulta .= " ON citasServicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasServicios.servicioId ";
        $consulta .= " WHERE fecha =  '${fecha}' ";
    
        $citas = AdminCita::SQL($consulta);

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}