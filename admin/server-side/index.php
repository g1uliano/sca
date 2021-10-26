<?php

/* * ******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  admin.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 07 de Maio de 2011 às 10:04 hrs.
 * ****************************************************************** */
error_reporting(0);

function callback($buffer) {
    //remover crappy output code.
    return (str_replace("sca", "", $buffer));
}

ob_start("callback");
session_start();
require_once('../../inc/FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
require_once("../../inc/mysql.class.php");
require_once("../../inc/sessaoUsuario.class.php");
$sessaoUsuario = new sessaoUsuario();
require_once("../../inc/basic/functions.basic.php");
require_once("../../inc/biRSA.php");
require_once("../../inc/banlist.class.php");
$ban = new banlist();
ob_end_flush();

if ($_POST['check'] == 'session') {
    if ($ban->verificarBan()) {
        destruir_sessao();
        print '..';
        exit;
    }
    if (!((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo'])))) {
        print '..';
        exit;
    }
}
if (!((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo'])))) {
    exit;
}

if ($_POST['destroy'] == 'session') {
    destruir_sessao();
    echo '..';
    exit;
} else {
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
}

$keyDecrypt = new biRSAKeyPair(
                '0', '10f8f41d7aea5526d56ce19fd88cd2dd',
                '1d65d1033aa8c90edb2e44ff0b6e33f9'
);

//pegar grupos.
if ($_GET['get'] == 'grupos') {
    $sql = "SELECT id,nome,dominio,banido FROM grupos";
    sql2json($sql);
}

//pegar dominios.
if ($_GET['get'] == 'dominios') {
    $sql = "SELECT id,nome,banido FROM dominio";
    sql2json($sql);
}

//pega os usuários
if ($_GET['get'] == 'usuarios') {
    $sql = "SELECT id,login,fullname,email,grupo,dominio,banido FROM usuarios";
    sql2json($sql);
}

//pega um usuário
if ($_POST['get_id'] != '') {
    $db = new MySQL();
    $sql = "SELECT id,login,fullname,email,grupo,dominio FROM usuarios WHERE id = " . $_POST['get_id'] . ";";
    $row = $db->QuerySingleRowArray($sql, MYSQL_NUM);
    $db = NULL;
    print json_encode($row); //retorna tudo prontinho, uma beleza. Parece atá magia, mas não, é tecnologia.
}

//cria um usuário
if ($_POST['create_usr'] != "") {
    $dec = base64_decode($_POST['create_usr']);
    $dec = $keyDecrypt->biDecryptedString($dec);
    $csv = explode(",", $dec);
    $csv[3] = hash('sha512', $csv[3]);
    $db = new MySQL();
    if ($csv[6] == '0') {
        $sql = "INSERT INTO `usuarios` (`login`, `fullname`, `email`, `encoded_password`, `dominio`, `grupo`) VALUES
            ('" . $csv[0] . "', '" . $csv[1] . "', '" . $csv[2] . "', '" . $csv[3] . "', " . $csv[4] . ", " . $csv[5] . ");";
    } else {
        //expires
        $sql = "INSERT INTO `usuarios` (`login`, `fullname`, `email`, `encoded_password`, `dominio`, `grupo`, `expires`) VALUES
            ('" . $csv[0] . "', '" . $csv[1] . "', '" . $csv[2] . "', '" . $csv[3] . "', " . $csv[4] . ", " . $csv[5] . ", 1);";
    }
    $db->Query($sql);
    $errorNumber = $db->ErrorNumber();
    switch ($errorNumber) {
        case 0:
            $sessaoUsuario->registraEvento("[usuários] O usuário " . $csv[0] . " foi criado com sucesso.");
            echo json_encode(array(0, "O usuário " . $csv[0] . " foi criado com sucesso."));
            break;
        case 1062:
            if (!check_email_exists($csv[2])) {
                echo json_encode(array(1062, "O usuário " . $csv[0] . " já existe."));
            } else {
                echo json_encode(array(1063, "O e-mail " . $csv[2] . " já está associado a outra conta de usuário."));
            }
            break;
        default:
            echo json_encode(array($errorNumber, "mysql error (" . $errorNumber . "): " . $db->getMessage()));
    }
    $db = NULL;
}

//altera um usuário
if ($_POST['alter_usr'] != "") {
    $dec = base64_decode($_POST['alter_usr']);
    $dec = $keyDecrypt->biDecryptedString($dec);
    $csv = explode(",", $dec);
    $db = new MySQL();
    if ($csv[5] == 0) {
        $csv[4] = 1;
    }
    if ($csv[3]) {
        $csv[3] = hash('sha512', $csv[3]);
        if ($csv[7] == '0') {
            $sql = "UPDATE `usuarios` SET `login`= '" . $csv[0] . "', `fullname` = '" . $csv[1] . "', `email` = '" . $csv[2] . "', `encoded_password` = '" . $csv[3] . "', `dominio` = '" . $csv[4] . "', `grupo` = '" . $csv[5] . "', `expires`= '" . date("Y-m-d H:i:s") . "', `banido` = '" . $csv[8] . "'  WHERE `id`= " . $csv[6] . ";";
        } else {
            $sql = "UPDATE `usuarios` SET `login`= '" . $csv[0] . "', `fullname` = '" . $csv[1] . "', `email` = '" . $csv[2] . "', `encoded_password` = '" . $csv[3] . "', `dominio` = '" . $csv[4] . "',  `grupo` = '" . $csv[5] . "', `expires`= '1', `banido` = '" . $csv[8] . "'  WHERE `id`= " . $csv[6] . ";";
        }
    } else {
        if ($csv[7] == '0') {
            $sql = "UPDATE `usuarios` SET `login`= '" . $csv[0] . "', `fullname` = '" . $csv[1] . "', `email` = '" . $csv[2] . "', `dominio` = '" . $csv[4] . "', `grupo` = '" . $csv[5] . "', `expires`= '" . date("Y-m-d H:i:s") . "' , `banido` = '" . $csv[8] . "' WHERE `id`= " . $csv[6] . ";";
        } else {
            $sql = "UPDATE `usuarios` SET `login`= '" . $csv[0] . "', `fullname` = '" . $csv[1] . "', `email` = '" . $csv[2] . "', `dominio` = '" . $csv[4] . "',  `grupo` = '" . $csv[5] . "', `expires`= '1', `banido` = '" . $csv[8] . "'  WHERE `id`= " . $csv[6] . ";";
        }
    }
    $db->Query($sql);
    $errorNumber = $db->ErrorNumber();

    switch ($errorNumber) {
        case 0:
            $sessaoUsuario->registraEvento("[usuários] O usuário " . $csv[0] . " foi alterado com sucesso.");
            echo json_encode(array(0, "O usuário " . $csv[0] . " foi alterado com sucesso."));
            break;
        default:
            echo json_encode(array($errorNumber, "mysql error (" . $errorNumber . "): " . $db->getMessage()));
    }

    if ($csv[8] != '') {
        if ($csv[8] == 1) {
            $sql = 'INSERT INTO `banlist` (`usuario`) VALUES (' . $csv[6] . ')';
        } else if ($csv[8] == 0) {
            $sql = "DELETE FROM `banlist` WHERE `usuario`= " . $csv[6] . ";";
        }
    }
    $db->Query($sql);
    $db = NULL;
}

//deleta um usuário
if ($_POST['delete_usr'] != "") {
    if ($_POST['delete_usr'] != 1) {
        $db = new MySQL();
        $sql = "DELETE FROM `usuarios` WHERE `id` = " . @mysql_real_escape_string($_POST['delete_usr']);
        if ($db->Query($sql)) {
            $sessaoUsuario->registraEvento("[usuários] O registro de usuário #" . $_POST['delete_usr'] . " foi deletado com sucesso.");
            echo json_encode(array(1, "Usuário deletado com sucesso."));
        } else {
            echo json_encode(array(0, "Erro ao deletar o usuário."));
        }
        $db = NULL;
    } else {
        $sessaoUsuario->registraEvento("[usuários] Houve uma tentativa de se apagar o usuário root.");
        echo json_encode(array(0, "Este usuário não pode ser deletado."));
    }
}

//criar um grupo
if ($_POST['create_group'] != "") {
    $db = new MySQL();
    $group_name = @mysql_real_escape_string($_POST['create_group']);
    $dominio = @mysql_real_escape_string($_POST['dominio']);
    $sql = "INSERT INTO `grupos` (`nome`,`dominio`) VALUES ('" . $group_name . "','" . $dominio . "') ;";
    $db->Query($sql);
    $errorNumber = $db->ErrorNumber();
    switch ($errorNumber) {
        case 0:
            $sessaoUsuario->registraEvento("[grupo] O grupo #" . $group_name . " foi criado com sucesso.");
            echo json_encode(array(0, "O grupo " . $group_name . " foi criado com sucesso."));
            break;
        case 1062:
            echo json_encode(array(1062, "O grupo " . $group_name . " já existe."));
            break;
        default:
            echo json_encode(array($errorNumber, "mysql error (" . $errorNumber . "): " . $db->getMessage()));
    }
    $db = NULL;
}

if ($_POST['alter_group'] != "") {
    $db = new MySQL();
    $csv = explode(",", base64_decode($_POST['alter_group']));
    $sql = "UPDATE `grupos` SET `nome`= '" . $csv[1] . "', `banido` = '" . $csv[2] . "' WHERE `id`= " . $csv[0];
    if ($db->Query($sql)) {
        $sessaoUsuario->registraEvento("[grupo] O grupo " . $csv[1] . " foi alterado com sucesso.");
        echo json_encode(array(1, "O grupo " . $csv[1] . " foi atualizado com sucesso."));
    } else {
        echo json_encode(array(0, "Erro ao atualizar o grupo."));
    }
    if ($csv[2] != '') {
        if ($csv[2] == 1) {
            $sql = 'INSERT INTO `banlist` (`grupo`) VALUES (' . $csv[0] . ')';
            $sessaoUsuario->registraEvento("[grupo] O grupo " . $csv[1] . " foi teve seu acesso bloqueado.");
        } else if ($csv[2] == 0) {
            $sql = "DELETE FROM `banlist` WHERE `grupo` = " . $csv[0] . ";";
        }
    }
    $db->Query($sql);
    $db = NULL;
}

if ($_POST['delete_group'] != "") {
    if ($_POST['delete_group'] != 1) {
        $db = new MySQL();
        $sql = "DELETE FROM `grupos` WHERE `id` = " . @mysql_real_escape_string($_POST['delete_group']);
        if ($db->Query($sql)) {
            $sessaoUsuario->registraEvento("[grupo] O registro de grupo #" . $_POST['delete_group'] . " foi deletado com sucesso.");
            echo json_encode(array(1, "Grupo deletado com sucesso."));
        } else {
            echo json_encode(array(0, "Ocorreu um erro ao tentar excluir o grupo."));
        }
        $db = NULL;
    } else {
        echo json_encode(array(0, "Este grupo não pode ser deletado."));
    }
}

//criar um dominio
if ($_POST['create_dominio'] != "") {
    $db = new MySQL();
    $dominio_name = @mysql_real_escape_string($_POST['create_dominio']);
    $sql = "INSERT INTO `dominio` (`nome`) VALUES ('" . $dominio_name . "') ;";
    $db->Query($sql);
    $errorNumber = $db->ErrorNumber();
    switch ($errorNumber) {
        case 0:
            $sessaoUsuario->registraEvento("[dominio] O dominio " . $dominio_name . " foi criado com sucesso.");
            echo json_encode(array(0, "O dominio " . $dominio_name . " foi criado com sucesso."));
            break;
        case 1062:
            echo json_encode(array(1062, "O dominio " . $dominio_name . " já existe."));
            break;
        default:
            echo json_encode(array($errorNumber, "mysql error (" . $errorNumber . "): " . $db->getMessage()));
    }
    $db = NULL;
}

if ($_POST['alter_dominio'] != "") {
    $db = new MySQL();
    $csv = explode(",", base64_decode($_POST['alter_dominio']));
    $sql = "UPDATE `dominio` SET `nome`= '" . $csv[1] . "' WHERE `id`= " . $csv[0];
    if ($db->Query($sql)) {
        $sessaoUsuario->registraEvento("[dominio] O dominio " . $csv[1] . " foi atualizado com sucesso.");
        echo json_encode(array(1, "O dominio " . $csv[1] . " foi atualizado com sucesso."));
    } else {
        echo json_encode(array(0, "Erro ao atualizar o dominio."));
    }
    if ($csv[2] != '') {
        if ($csv[2] == 1) {
            $sql = 'INSERT INTO `banlist` (`dominio`) VALUES (' . $csv[0] . ')';
            $sessaoUsuario->registraEvento("[dominio] O dominio " . $csv[1] . " foi teve seu acesso bloqueado.");
        } else if ($csv[2] == 0) {
            $sql = "DELETE FROM `banlist` WHERE `dominio`= " . $csv[0] . ";";
        }
    }
    $db->Query($sql);
    $db = NULL;
}

if ($_POST['delete_dominio'] != "") {
    if ($_POST['delete_dominio'] != 1) {
        $db = new MySQL();
        $sql = "DELETE FROM `dominio` WHERE `id` = " . @mysql_real_escape_string($_POST['delete_dominio']);
        if ($db->Query($sql)) {
            $sessaoUsuario->registraEvento("[dominio] O dominio #" . $_POST['delete_dominio'] . " foi deletado com sucesso.");
            echo json_encode(array(1, "dominio deletado com sucesso."));
        } else {
            echo json_encode(array(0, "Ocorreu um erro ao tentar excluir domínio."));
        }
        $db = NULL;
    } else {
        echo json_encode(array(0, "Este domínio não pode ser deletado."));
    }
}

if ($_GET['get_eventos'] != "") {
    $sql = "SELECT `id`, `usuario`, `datahora`, `ipv4` FROM `sessoes` ORDER BY `id` DESC LIMIT 0 , " . @mysql_real_escape_string($_GET['get_eventos']);
    $db = new sessaoUsuario();
    $row = $db->QueryArray($sql, MYSQL_ASSOC);
    echo 'Últimas ' . sizeof($row) . ' autenticações efetuadas<br />';
    echo '<table class="tablesorter" cellspacing="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>usuário</th>';
    echo '<th>data e hora</th>';
    echo '<th>ipv4</th>';
    echo '<th>ação</th>';
    echo '</tr>';
    echo '</thead>';
    echo "<tbody>";
    for ($i = 0; $i < sizeof($row); $i++) {
        echo "<tr>";
        echo "<td>" . $db->getUserById($row[$i]['usuario']) . "</td>";
        echo "<td>" . date("d/m/Y H:i:s", strtotime($row[$i]['datahora'])) . "</td>";
        echo "<td>" . $row[$i]['ipv4'] . "</td>";
        echo "<td><a name=detalhar uid=" . $row[$i]['id'] . " href=#>detalhar sessão</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    if ($_GET['get_eventos'] != '1000') {
        echo '<a id="eventos_todos" href="#">exibir todos os registros</a>';
    }
}

if ($_GET['detalhar_evento'] != '') {
    $sql = "SELECT * FROM `sessoes` WHERE `id` = " . $_GET['detalhar_evento'] . " ORDER BY `id` LIMIT 0 , 1";
    $db = new sessaoUsuario();
    $row = $db->QuerySingleRowArray($sql, MYSQL_ASSOC);
    echo "<u>Detalhes da sessão</u> <br /><br />";
    echo "<b>usuário:</b> " . $db->getUserById($row['usuario']) . " ";
    echo "<b>grupo: </b>" . $db->getGrupoById($row['grupo']) . " ";
    echo "<br />";
    echo "<b>domínio: </b>" . $db->getDominioById($row['grupo']) . " ";
    echo "<br />";
    echo "<b>navegador:</b> " . $row['browser'] . " ";
    echo "<b>versão:</b> " . $row['version'] . " ";
    echo "<br />";
    echo "<b>sistema operacional:</b> " . $row['so'] . " ";
    echo "<br />";
    echo "<b>ipv4:</b> " . $row['ipv4'] . " ";
    echo "<b>hostname:</b> " . $row['hostname'] . " ";
    echo "<br /><br />";
    $sql = "SELECT `id`, `sessao`, `acao`, `datahora` FROM `eventos` WHERE `sessao` = " . $_GET['detalhar_evento'] . " ORDER by `id` ASC LIMIT 0, 1000;";
    $row = $db->QueryArray($sql, MYSQL_ASSOC);
    if (!empty($row[1]['sessao'])) {
        echo "<u>Rastreio das operações ocorridas na sessão</u>";
        echo "<br /><br />";
        echo '<table class="tablesorter" cellspacing="1">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>data e hora</th>';
        echo '<th>ação</th>';
        echo '</tr>';
        echo '</thead>';
        echo "<tbody>";
        for ($i = 0; $i < sizeof($row); $i++) {
            echo "<tr>";
            echo "<td>" . date("d/m/Y H:i:s", strtotime($row[$i]['datahora'])) . "</td>";
            echo "<td>" . $row[$i]['acao'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<br />";
        echo "Nenhum evento está relacionado a esta sessão.<br /><br />";
    }
    echo '<a id="eventos" href="#">retornar para a tela principal</a>';
}
?>
