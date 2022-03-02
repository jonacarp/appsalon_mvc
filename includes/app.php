<?php 

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//safeLoad() lo usamos para que no de error en el caso de que el archivo no exista, y lo usamos ya que en un ambiente productivo no usamos este archivo sino que las variables de entorno las inyectamos con un panel especial.
$dotenv->safeLoad();



require 'funciones.php';
require 'database.php';


// Conectarnos a la base de datos
use Model\ActiveRecord;
ActiveRecord::setDB($db);