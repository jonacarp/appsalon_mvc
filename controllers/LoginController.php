<?php
namespace Controllers;
use MVC\Router;
use Classes\Email;
use Model\Usuario;


class LoginController {
    public static function login(Router $router) {
        session_start();
        //Si el usuario está autenticado lo redirigimos a /cita para evitar que vuelva a iniciar sesion
        isAuth1();

        $alertas = [];
        $auth = new Usuario();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Guardamos lo que el usuario envia en el formulario de autenticacion
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //Comprobamos que exista el usuario
                //Guardamos la respuesta de la consulta a la base de datos en $usuario
                $usuario = Usuario::where('email', $auth->email);
                
                if ($usuario) {
                    //Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //Autenticamos al usuario - Iniciamos su sesion
                        //Iniciamos la sesion para poder tener acceso a la super global $_SESSION
                        
                        session_start();
                        
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //Redireccionamiento si es admin o no
                        if ($usuario->admin === "1") {
                            
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            
                            header('location: /admin');
                        } else {
                            header('location: /cita');
                        }
                    }
                } else {
                    //Seteamos un alerta en el objeto del modelo Usuario
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }

        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                
                //Consultamos si el usuario existe (Si la fucion "where()" nos devolvio un registro) y si el mismo existe si es que está confirmado en la DB
                if ($usuario && $usuario->confirmado === "1") {
                    //Generamos y enviamos un token para que el usuario resetee la password

                    //Nuevo token de un solo uso
                    $usuario->crearToken();
                    //Guardamos el usuario en la DB con el nuevo token
                    $usuario->guardar();
                    //Enviar el email al usuario
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de exito de envio
                    Usuario::setAlerta('exito', 'Revisa tu email');
                   
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }

        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $error = false;
        
        //Buscar usuario por su token y guardamos el registro en $usuario
        $usuario = Usuario::where('token', $token);
        
        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Válido');
            //Seteamos el error para pasarlo a la vista
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo password y guardarlo en la DB
            //Generamos la instancia $password donde vamos a almacenar la password que ingresa el usuario
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                //En la instancia del usuario, donde tenemos los datos del registro asignamos "null" al password
                $usuario->password = null;
                //A la instancia del usuario, donde tenemos los datos del registro le asignamos la clave que ingresa el usaurio y envia por POST, la cual ya teniamos en otra instancia llamada $password
                $usuario->password = $password->password;
                //Hasheamos la nueva password que acabamos de ingresar al objeto $usuario
                $usuario->hashPassword();
                //Seteamos el token en "null" para que se actualice en la DB
                $usuario->token = null;
                
                //Lamamos al metodo guarda los cambios en la base de datos, sincronizando el objeto $usuario que tenemos en memoria con el registro de la base de datos.
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('location: /');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            //Este error lo enviamos a la vista y ahi validamos si se recibe con valor "true" entonces indicamos un "return" para que corte la ejecucion del resto del codigo, de lo contrario no se hace nada.
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario($_POST); 
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Sincronizamos el objeto "$usuario" que ya está instanciado con los datos que envia el usuario en el metodo $_POST
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisamos que el objetos de alertas esté vacio
            if (empty($alertas)) {
                //Verificamos que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();
                
                //Consultamos si hay resultado en la query, si hay es porque el usuario existe y si no hay lo creamos
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //No está registrado, lo creamos en la base de datos

                    //Hasheamos el password
                    $usuario->hashPassword();

                    //Generamos un token unico
                    $usuario->crearToken();

                    //Eviamos un mail para confirmar el token
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    //Crear el usuario el la base de datos
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('location: /mensaje');
                    }
                }
            }

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        
        if (empty($usuario)) {
            //Mostramos mensaje de error
            //Elviamos las alertas al modelo
            Usuario::setAlerta('error', 'Toekn no valido');
        } else {
            //Modificamos a usuario confirmado en la DB
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        //Trae las alertas desde el modelo
        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
    
}