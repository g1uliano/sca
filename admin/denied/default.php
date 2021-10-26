<?php
/* * ******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  admin.php - criado e mantido por: Giuliano Cardoso
  última alteração: 16 de abril de 2011 às 09:20 hrs.
 * ***************************************************************** */

if (!defined('_INCLUDED')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
//se o usuário não estiver autenticado, manda de volta para o lugar de onde ele veio, afinal, ele não foi convidado pra festa, foi?
if (!((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo'])))) {
    header("Location: ..");
}

if ($_GET['destroy']) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: ..");
    exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
        <title> Acesso Negado </title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/denied.css" type="text/css" />
    </head>
    <body>
        <div id="default">
            <div class="center">
                <span>Acesso Negado</span> <br /><br />
                Seu usuário não possui os privilégios necessários para acessar este recurso.
                <br /><br />
                Clique <a href="?destroy=true"> aqui</a> para retornar a tela de login.
            </div>
        </div>
           
       </body>
</html>
