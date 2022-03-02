<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        //Creamos una instancia de PHPMailer
        $mail = new PHPMailer();

        //Configuramos SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true; //Para que se requiera autenticacion
        $mail->Username = 'a4ee4782ecfe31';
        $mail->Password = '50de3b72c489ee';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;
        //Configurar contenido del email
        $mail->setFrom('admin@appsalon.com'); //Sender
        $mail->addAddress('admin@appsalon.com', 'AppSalon.com'); //Receptor
        $mail->Subject = 'Confirma tu cuenta'; //Asunto

        //Habilitar el contenido del mail en formato HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        //Definimos el contenido
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "!</strong> Has creato tu cuenta en AppSalon, solo debes confirmarla precionando el siguiente enlace:</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar-cuenta?token=". $this->token ."'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        
        $mail->Body = $contenido;
        $mail->AltBody = 'Esto es texto alternativo sin HTML';

        //Enviar el email
        $mail->send();
        // if ($mail->send()) {//Retorna true si se envió y false si no se envió
        //     echo "Mensaje enviado correctamente";
        // } else {
        //     echo "El mensaje no se pudo enviar";
        // }

    }

    public function enviarInstrucciones() {
        //Creamos una instancia de PHPMailer
        $mail = new PHPMailer();

        //Configuramos SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true; //Para que se requiera autenticacion
        $mail->Username = 'a4ee4782ecfe31';
        $mail->Password = '50de3b72c489ee';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;
        //Configurar contenido del email
        $mail->setFrom('admin@appsalon.com'); //Sender
        $mail->addAddress('admin@appsalon.com', 'AppSalon.com'); //Receptor
        $mail->Subject = 'Reestablece tu password'; //Asunto

        //Habilitar el contenido del mail en formato HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        //Definimos el contenido
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "!</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/recuperar?token=". $this->token ."'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no solicitaste el cambio de password, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        
        $mail->Body = $contenido;
        $mail->AltBody = 'Esto es texto alternativo sin HTML';

        //Enviar el email
        $mail->send();
        // if ($mail->send()) {//Retorna true si se envió y false si no se envió
        //     echo "Mensaje enviado correctamente";
        // } else {
        //     echo "El mensaje no se pudo enviar";
        // }
    }
}