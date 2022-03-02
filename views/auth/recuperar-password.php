<h1 class="nombre-pagina">Recuperar Password</h1>

<p class="descripcion-pagina">Coloca tu nuevo password a continuacion</p>

<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<!--En caso de que se recibe la variable $error como el valor "true" entonces ejecutamos "return" para cancelar el resto del codigo-->
<?php if ($error) return; ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu nueva password">
    </div>
    <input type="submit" class="boton" value="Guardar Nueva Password">

</form>

<div class="acciones">
    <a href="/">Ya tienes cuenta una cuenta? Iniciar Sesión.</a>
    <a href="/crear-cuenta">Aún no tienes cuenta? Obtener una</a>
</div>