<?php
require_once('config.php');
$lista = json_decode(file_get_contents("plantillas/lista.json"), true);
$rem = array_splice($lista, $_POST['num'], 1);
unlink("plantillas/" . $rem[0]['archivo']);
file_put_contents("plantillas/lista.json", json_encode($lista, JSON_PRETTY_PRINT));
print_r(json_encode(array('exito' => true)));
