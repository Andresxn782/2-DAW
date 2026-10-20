<!-- Página dinámica: la hora la escribe el servidor cada vez que se pide la página -->
<p>Horario actualizado el
<time><?php echo date('Y'); ?>-<?php echo date('m'); ?>-<?php echo date('d'); ?> <?php echo date('H'); ?>:<?php echo date('i'); ?>:<?php echo date('s'); ?></time>
</p>
<p>Hoy es <?php echo date('l'); ?> y el gimnasio Velnora <?php echo (date('H') >= 7 && date('H') < 23) ? "está abierto" : "está cerrado"; ?>.</p>
