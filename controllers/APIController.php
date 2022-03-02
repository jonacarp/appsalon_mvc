<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        //Convertimos el objeto $servicios en un json
        echo json_encode($servicios);
    }

    public static function guardar() {
        //***Resivimos por POST desde la pagina de reserva de cita, lo cual se procesa en el archivo app.js en la funcion "reservarCita" y nos envia por POST los datos de la cita y sus servicios.

        //***Generamos un objeto instanciado desde la clase "Cita" y le pasamos como valores lo que recibimos por POST
        $cita = new Cita($_POST);

        //***Guardamos el resultado de la respuesta de la base de datos (true si guarda y false si hay error, y si guarda nos retorna el ID) al gurdar la cita para mostrarlo en la API
        $resultado = $cita->guardar();

        //Almacenamos el ID de la nueva cita generada dentro de la constante "id"
        $id = $resultado['id'];

        //***Separamos el string de los servicios que nos retorna el POST para poder detectar cada ID por separado y los separamos luego de cada coma convirtiendolo en un arreglo con cada uno de los valores.
        $idServicios = explode(",", $_POST['servicios']);

        //***Generamos el arreglo que le vamos a enviar al modelo de CitaServicio para que lo guarde en la DB. Le vamos a pasar el ID de la cita que lo obtenemos de la respuesta al guardar la informacion en la tabla "cita" y le pasamos los servicios que selecciona el usaurio.

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        //Retornamos una respuesta
        echo json_encode(['resultado' => $resultado]);

    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Asignamos a una variable el ID de la cita que recibimos por POST
            $id = $_POST['id'];
            
            //Usamos el modelo de "Cita" para usar el metodo "find" el cual va a buscar si el ID está en la DB y nos va a devolver el resultado de la consulta
            $cita = Cita::find($id);
            //Borramos la cita
            $cita->eliminar();
            //Redireccionamos a la pagina donde está actualmente incluido el GET
            header('location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}