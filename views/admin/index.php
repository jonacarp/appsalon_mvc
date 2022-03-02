<h1 class="nombre-pagina">Panel de Administracion</h1>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>

<h2>Buscar Citas</h2>

<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>"/>
        </div>
    </form>
</div>

<?php
    //Revisamos si el arreglo de citas está vacio, si es asi es porque no hay citas en la fecha indicada, entonces mostramos un mensaje
    if (count($citas) === 0) {
        echo "<h2 class='no-citas'>No hay citas en esta fecha</h2>";
    }
?>

<div id="citas-admin">
    <ul class="citas">
        <?php
            $idCita = null;
            foreach($citas as $key => $cita) {
                if ($idCita !== $cita->id) {
                    //Iniciamos en 0 la variable que va a mostrar el precio total. Lo hacemos dentro de este if ya que al pasar a la siguiente cita volvemos a inicializar la variable.
                    $total = 0;
            ?>
                <li>
                    <p>ID: <span><?php echo $cita->id; ?></span></p>
                    <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                    <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                    <p>Email: <span><?php echo $cita->email; ?></span></p>
                    <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>
        
                    <h3>Servicios</h3>
            <?php  $idCita = $cita->id;
                } //Fin de if 
                //Hacemos la suma por fuera del if para que vaya sumando en la variable "$total" en cada iteracion del foreach
                $total += $cita->precio;
            ?>
                <p class="servicio"><?php echo $cita->servicio . " " . $cita->precio; ?></p>
            <?php
                //Valor actual del ID de la cita 
                $actual = $cita->id;
                //Valor siguiente del ID de la cita (Tomamos el objeto donde están todas las citas)
                $proximo = $citas[$key +1]->id ?? 0;

                //Lamamos a la funcion para que sonsulte si coincide el ultimo con el actual, en caso de ser el ultimo mostramos el precio total del servicio
                if (esUltimo($actual, $proximo)) { 
            ?>
                    <p class="total">Total: <span>$ <?php echo $total ?></span></p>
                    <form action="/api/eliminar" method="POST">
                        <input type="hidden" name="id" value="<?php echo $cita->id; ?>"><!--Enviamos por POST el ID de la cita a la API "/api/eliminar" y lo procesa APIController.php-->
                        <input type="submit" class="boton-eliminar" value="Eliminar">
                    </form>
            <?php } //Cierra el if de esUltimo()
            } //Fin de Foreach?>

    </ul>
</div>

<?php
    $script = "<script src='build/js/buscador.js'></script>";
?>