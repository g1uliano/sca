<?php

/* * ******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  admin.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 07 de Maio de 2011 às 10:04 hrs.
 * ****************************************************************** */
error_reporting(0);

function callback($buffer) {
    return (str_replace("sca", "", $buffer));
    ;
}

ob_start("callback");
session_start();

if ($_POST['enc']) {
    require_once('../../inc/FirePHPCore/FirePHP.class.php');
    $firephp = FirePHP::getInstance(true);
    include("../../inc/mysql.class.php");
    include("../../inc/basic/functions.basic.php");
    include("../../inc/biRSA.php");
    include("../../inc/sessaoUsuario.class.php");
    $sessaoUsuario = new sessaoUsuario();
    ob_end_flush();
    $keyDecrypt = new biRSAKeyPair(
                    '0', '10f8f41d7aea5526d56ce19fd88cd2dd',
                    '1d65d1033aa8c90edb2e44ff0b6e33f9'
    );


    $dec = base64_decode($_POST['enc']);
    $dec = $keyDecrypt->biDecryptedString($dec);
    $dec = explode(',', $dec);
    $pwd = hash('sha512', trim($dec[1]));
    $db = new MySQL();

    $sql = "SELECT id,encoded_password FROM usuarios WHERE encoded_password='" . $pwd . "' AND id = " . $_SESSION['sca_id'];
    $row = $db->QuerySingleRowArray($sql, MYSQL_ASSOC);

    if ($row['encoded_password'] == $pwd) {
        $pwd = hash('sha512', $dec[0]);
        $sql = "UPDATE `usuarios` SET `encoded_password` = '" . $pwd . "', `expires`= '" . date("Y-m-d H:i:s") . "'  WHERE `id`= " . $_SESSION['sca_id'] . ";";
        $db->Query($sql);
        $errorNumber = $db->ErrorNumber();
        switch ($errorNumber) {
            case 0:
                $sessaoUsuario->registraEvento('[troca-senha] Senha alterada com sucesso.');
                echo json_encode(array(1, $_SESSION['sca_url']));
                $_SESSION['sca_expired'] = false;
                break;
            default:
                echo json_encode(array(0, "mysql error (" . $errorNumber . "): " . $db->getMessage()));
        }
    } else {
        echo json_encode(array(0, "A senha informada como atual não é válida."));
    }
    $db = NULL;
}
?>
