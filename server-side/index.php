<?php

/* * ***************************************************************** */
/* Sistemas Controlador de Acesso
  /* Controle de Login
  /* login.php - criado e mantido por: Giuliano Cardoso
  /* última alteração: 01 de maio de 2011 às 12:12 hrs.
  /******************************************************************* */
error_reporting(0); // para evitar problemas quando em produção.

function callback($buffer) {
    //remover crappy output code.
    return (str_replace("sca", "", $buffer));
}

ob_start("callback");
session_start();
require_once('../inc/FirePHPCore/FirePHP.class.php');
require_once('../inc/basic/ajax.header.php');
require_once('../inc/basic/functions.basic.php');
require_once('../inc/mysql.class.php');
require_once('../inc/sessaoUsuario.class.php');
require_once('../inc/biRSA.php');
require_once("../inc/banlist.class.php");
$ban = new banlist();

ob_end_flush();
$firephp = FirePHP::getInstance(true);

$db = new MySQL();

$keyDecrypt = new biRSAKeyPair(
                '0', '10f8f41d7aea5526d56ce19fd88cd2dd',
                '1d65d1033aa8c90edb2e44ff0b6e33f9'
);

if ($_POST['check'] == 'session') {
    if ($ban->verificarBan()) {
        destruir_sessao();
        print '..';
        exit;
    }
    if (!((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo'])))) {
        if ($_POST['u'] != '') {
            $_SESSION['sca_url'] = $_POST['u'];
        }
        echo "..";
    } else {
        $sql = "SELECT login FROM `usuarios` where id = " . $_SESSION['sca_id'];
        $array = $db->QuerySingleRowArray($sql, MYSQL_NUM);
        echo json_encode($array);
    }
}

if ($_POST['check'] == "true") {
    if ($db->IsConnected()) {
        echo 1;
    } else {
        echo 0;
    }
}

if ($_POST['enc'] != '') {
    $dec = base64_decode($_POST['enc']);
    $dec = $keyDecrypt->biDecryptedString($dec);
    $div = explode(":", $dec);
    $l = trim(@mysql_real_escape_string($div[1]));
    $s = trim(@mysql_real_escape_string($div[3]));
    $s = hash('sha512', $s);

    $sql = "SELECT * FROM `usuarios` where `login` = '" . $l . "' and `encoded_password` = '" . $s . "' ;";

    if ($ban->verificarBan()) {
        destruir_sessao();
        print 0;
        exit;
    }

    if ($db->IsConnected()) {
        $array = $db->QuerySingleRowArray($sql);
        if ($array) {
            if (($array['login'] == $l) && ($array['encoded_password'] == $s)) {
                $_SESSION['sca_id'] = $array['id'];
                $_SESSION['sca_grupo'] = $array['grupo'];
                $array['expires'] = convert_datetime($array['expires']);
                $validade = (($array['expires']) + 7776000);
                $sessaoUsuario = new sessaoUsuario();
                $sessaoUsuario->registrarSessao($array['id'], $array['grupo'], $array['dominio'], $div[4]);
                $sessaoUsuario->registraEvento('[login] Login efetuado com sucesso.');

                if ($validade < time()) {
                    $_SESSION['sca_expired'] = true;
                    if (!isset($_SESSION['sca_url'])) {
                        $link = str_replace('/server-side/', '', curPageURL());
                        $_SESSION['sca_url'] = $link . '/admin/';
                        $sessaoUsuario->registraEvento('[login] Redirecionado para a administração.');
                    }
                    echo 'expired/';
                } else {
                    if (isset($_SESSION['sca_url'])) {
                        $sessaoUsuario->registraEvento('[login] Redirecionado para ' . $_SESSION['sca_url']);
                        echo $_SESSION['sca_url'];
                    } else {
                        $sessaoUsuario->registraEvento('[login] Redirecionado para a administração.');
                        echo 'admin/';
                    }
                }
            } else {
                echo 0;
            }
        } else {
            if ($db->Error()) {
                $firephp->error('MySQL Error: ' . $db->Error());
            }
            echo 0;
        }
    } else {
        echo "NULL";
    }
}

if ($_POST['email'] != '') {
    $email = base64_decode($_POST['email']);
    $email = @mysql_real_escape_string($email);
    $sql = "SELECT * FROM `usuarios` where `email` = '" . $email . "';";
    $array = $db->QuerySingleRowArray($sql);
    if ($array['email'] != '') {
        $senha = generatePassword();

        $subject = 'Nova Senha de Acesso';
        $host_name = $_SERVER['REMOTE_HOST'];
        if (empty($host_name)) {
            $host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }
        $link = str_replace('server-side/', '', curPageURL());
        $srv = $_SERVER['SERVER_NAME'];
        $srv = str_replace("www.", "", $srv);
        $no_reply = 'no-reply@' . $srv;

        $message = 'Um usuário requisitou uma reinicialização de senha a partir de ' . $host_name . ' (' . $_SERVER['REMOTE_ADDR'] . '). <br /><br />';
        $message .= 'Sendo assim, estes são os seus novos da dados de login: <br />';
        $message .= 'login: ' . $array['login'] . '<br />';
        $message .= 'senha: ' . $senha . ' <br />';
        $message .= '<br /><br />';
        $message .= 'Para efetuar o login acess este link: <a href="' . $link . '">' . $link . '</a>';

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: SCA <' . $no_reply . '>' . "\r\n";
        $encoded_password = hash('sha512', $senha);
        if (mail($array['email'], $subject, $message, $headers)) {
            $sql = "UPDATE `usuarios` SET `expires`= '1', `encoded_password` = '" . $encoded_password . "'  WHERE `id`= " . $array['id'] . ";";
            $db->Query($sql);
            echo json_encode(array(1, "Uma nova senha foi enviada para o e-mail informado."));
        }
    } else {
        echo json_encode(array(0, "O e-mail informado é inválido."));
    }
}
?>
