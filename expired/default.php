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
?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
        <title> Trocar Senha de Usuário </title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/default.css" type="text/css" />       
        <script type="text/javascript" src="../js/yepnope.1.0.1-min.js"></script>
        <script language="javascript">
            yepnope([
                { load: '../min/?g=exp_js' }
            ]);
        </script> 
    </head>
    <body>
        <div id="default">
            <div class="center">
                <span>Trocar Senha de Usuário</span> <br /><br />
                Antes de continuar, você deve primeiro alterar a sua senha de usuário.
                <br /><br />
                <div id="troca_senha">
                    <form name="valida_troca" id="valida_troca" method="post" action="">
                    <p><label class="texto" for="atual">senha atual</label><input name="senha_atual" id="senha_atual" type="password" value=""></p>
                    <p><label class="texto" for="nova">nova senha</label><input name="nova_senha" id="nova_senha" type="password" value=""></p>
                    <p><label class="texto" for="confirma">confirma senha</label><input name="confirma_senha" type="password" value=""></p>
                    <p class="submit"><input type="submit" name=trocar value="trocar" /></p>
                    </form>
                </div>
            </div>
        </div>
           
       </body>
</html>
