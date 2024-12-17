<?php
session_start();
session_destroy();
header("Location: /2DAW/m7blog/app/index.php"); // Ruta absoluta
exit();
?>