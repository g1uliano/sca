<?php
/*******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  index.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 16 de maio de 2011 às 20:16 hrs.
  ******************************************************************/
function callback($buffer)
{
  //remover crappy output code.
  return (str_replace("sca", "", $buffer));
}

ob_start("callback");

define("_INCLUDED", true);
session_start();

if ((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo']))) {
    if ((isset($_SESSION['sca_expired'])) && ($_SESSION['sca_expired'])) {
        include("../inc/sessaoUsuario.class.php");
        $sessaoUsuario = new sessaoUsuario();
        $sessaoUsuario->registraEvento('[troca-senha] Página de troca de senha acessada.');
        include("default.php");
    } else {
        Header("Location: ..");
    }
} else {
    Header("Location: ..");
}
ob_end_flush();
?>
