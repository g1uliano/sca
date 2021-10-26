<?php
/********************************************************************
  Sistemas Controlador de Acesso
  Controle de Login
  index.php - criado e mantido por: Giuliano Cardoso
  Última alteração: 31 de janeiro de 2011 às 16:06 hrs.
 ********************************************************************/
function callback($buffer)
{
  //remover crappy output code.
  return (str_replace("sca", "", $buffer));
}

ob_start("callback");
session_start();
if ((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo']))) {
    if (isset($_SESSION['sca_url'])) {
        header("Location: " . $_SESSION['sca_url']);
    } else {
        header("Location: admin/");
    }
} else {
    header("Location: login.html");
}
ob_end_flush();
?>
