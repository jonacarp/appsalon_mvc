<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}
//Sanitiza el HTML que es ingresado por el usuario escapando a los caracteres especiales
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

//Busca si dos valores son distintos devuelve true si son distintos o false si son iguales
function esUltimo(string $actual, string $proximo): bool {
    if ($actual !== $proximo) {
        return true;
    }
    return false;
}

//Esta funcion revisa que el usuario esté autenticado y si no lo está lo envia al login
function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        header('location: /');
    } 
}

//Esta funcion revisa si el usuario que inicio sesion es un admin
function isAdmin() : void {
    if (!isset($_SESSION['admin'])) {
        header('location: /');
    }
}

//Esta funcion revisa que el usuario esté autenticado y si está entonces lo redirige a /cita
function isAuth1() : void {
    if(isset($_SESSION['login'])) {
        header('location: /cita');
    } 
}