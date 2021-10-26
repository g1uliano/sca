<?php
/*******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  index.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 6 de maio de 2011 às 20:16 hrs.
  ******************************************************************/
error_reporting(0);
function callback($buffer)
{
  //remover crappy output code.
  return (str_replace("sca", "", $buffer));
}
ob_start("callback");

define("_INCLUDED", true);
session_start();

if ((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo']))) {
    if ($_SESSION['sca_grupo']<>1) {
        include("default.php");
    } else {
        Header("Location: ..");
    }
} else {
    Header("Location: ..");
}
ob_end_flush();
?>
