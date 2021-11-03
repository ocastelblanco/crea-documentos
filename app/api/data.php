<?php
require_once 'config.php';
if (isset($_GET['accion'])) {
  $json = file_get_contents("contenidos.json");
  $data = json_decode($json, true);
  $accion = $_GET['accion'];
  switch ($accion) {
    case 'lista':
      salida($data);
      break;
    case 'carga':
      salida($data[$_POST['id']]['valores']);
      break;
    case 'guarda':
      $documento = json_decode($_POST['documento'], true);
      array_push($data, $documento);
      modifica($data);
      break;
    case 'borra':
      $id = $_POST['id'];
      array_splice($data, $id, 1);
      modifica($data);
      break;
  }
}
function modifica($data)
{
  if (file_put_contents("contenidos.json", json_encode($data, JSON_PRETTY_PRINT))) {
    salida();
  } else {
    salida(null);
  }
}
function salida($data = null, $error = null)
{
  $salida = array('error' => $error);
  if ($data != null) $salida['valores'] = $data;
  $json = json_encode($salida);
  print_r($json);
}
