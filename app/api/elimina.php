<?php
if (unlink($_GET['nombreArchivo'])) {
  print_r(json_encode(array('exito' => true)));
} else {
  print_r(json_encode(array('exito' => false)));
}
