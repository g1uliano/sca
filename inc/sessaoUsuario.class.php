<?php

/**
 * Description of sessaoUsuario
 * 
 * Classe de controle das sessões dos usuários.
 *
 * @author Giuliano Cardoso
 */
require_once("mysql.class.php");

class sessaoUsuario extends MySQL {

    public function getIP() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    public function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {            
        }

        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    public function get_hostname() {
        return gethostbyaddr($this->getIP());
    }

    public function getUserId() {
        return isset($_SESSION['sca_id']) ? $_SESSION['sca_id'] : null;
    }

    public function getGrupoId() {
        return isset($_SESSION['sca_grupo']) ? $_SESSION['sca_grupo'] : null;
    }

    public function getUserById($Id) {
        $sql = "select id,login from `usuarios` where `id` = $Id;";
        $row = $this->QuerySingleRowArray($sql);
        return $row['login'];
    }
    
    public function getGrupoById($Id) {
        $sql = "select id,nome from `grupos` where `id` = $Id;";
        $row = $this->QuerySingleRowArray($sql);
        return $row['nome'];
    }
    
    public function getDominioById($Id) {
        $sql = "select id,nome from `dominio` where `id` = $Id;";
        $row = $this->QuerySingleRowArray($sql);
        return $row['nome'];
    }
    
    public function getDominioByUserId($Id) {
        $sql = "select id,dominio from `usuarios` where `id` = $Id;";
        $row = $this->QuerySingleRowArray($sql);        
        return $row['dominio'];
    }
    
    public function registrarSessao($usuario, $grupo, $dominio, $fingerprint) {
        $browser = $this->getBrowser();
        $sql = "INSERT INTO `sessoes` (`usuario`,`grupo`,`dominio`,`ipv4`,`hostname`,`browser`,`version`,`so`,`fingerprint`) ";
        $sql .= "VALUES ('$usuario','$grupo','$dominio','" . $this->getIP() . "','" . $this->get_hostname() . "','" . $browser['name'] . "', '" . $browser['version'] . "','" . $browser['platform'] . "', '$fingerprint')";
        return $this->Query($sql);
    }

    public function ultimaSessao($usuarioId) {
        $sql = 'SELECT id,usuario FROM `sessoes` WHERE `usuario` = ' . $usuarioId . ' ORDER BY id DESC';
        $row = $this->QuerySingleRowArray($sql);
        return $row['id'];
    }

    public function registraEvento($evento) {
        $return = false;
        if ($this->getUserId() != null) {
            $usuarioId = $this->getUserId();
            $sessao = $this->ultimaSessao($usuarioId);
            $sql = "INSERT INTO `eventos` (`sessao`, `acao`) VALUES ($sessao, '$evento');";
            $this->Query($sql);
            $return = true;
        }
        return $return;
    }

}

?>
