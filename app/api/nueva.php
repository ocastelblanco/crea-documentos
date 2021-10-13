<?php
require_once('config.php');
if (move_uploaded_file($_FILES["file"]["tmp_name"], "plantillas/" . $_FILES['file']['name'])) {
  $lista = json_decode(file_get_contents("plantillas/lista.json"), true);
  array_push($lista, json_decode($_POST["datos"], true));
  file_put_contents("plantillas/lista.json", json_encode($lista, JSON_PRETTY_PRINT));
  print_r(json_encode(array('carga' => true)));
} else {
  print_r(json_encode(array('carga' => false)));
}
