<?php
/********************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  admin.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 07 de Maio de 2011 às 10:04 hrs.
 ********************************************************************/
function callback($buffer)
{
  //remover crappy output code.
  return (str_replace("sca", "", $buffer));
}

ob_start("callback");

error_reporting(0); // para evitar problemas quando em produção.

session_start();
ob_end_flush();
$url = $_SESSION['sca_url'];
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
       $params["path"], $params["domain"],
       $params["secure"], $params["httponly"]
    );
}
session_destroy();
if (empty($url)) 
    $url = '..';
Header("Location: $url");
exit;

?>
