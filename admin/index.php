<?php

/*******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  index.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 23 de março de 2011 às 16:06 hrs.
 ******************************************************************/
error_reporting(0);
function callback($buffer)
{
  //remover crappy output code.
  return (str_replace("sca", "", $buffer));
}

include("../inc/basic/functions.basic.php");
include("../inc/sessaoUsuario.class.php");
define("_INCLUDED", true);
ob_start("callback");
session_start();
$sessaoUsuario = new sessaoUsuario();

if ((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo']))) {
    if ((isset($_SESSION['sca_expired'])) && ($_SESSION['sca_expired'])) {
        Header("Location: ../expired/");
    } else {
        if ($_SESSION['sca_grupo'] == 1) {
            if (!isset($_SESSION['sca_url'])) {
                $_SESSION['sca_url'] = curPageURL();
            }        
            $sessaoUsuario->registraEvento('[admin] Página da administração acessada.');
            include("admin.php");
        } else {
            $sessaoUsuario->registraEvento('[admin] Acesso a página de administração negado.');
            Header("Location: denied/");
        }
    }
} else {
    $_SESSION['sca_url'] = curPageURL();
    $sessaoUsuario->registraEvento('[admin] Redirecionado a:'.$_SESSION['sca_url']);
    Header("Location: ..");
}
ob_end_flush();
?>