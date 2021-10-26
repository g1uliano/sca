<?php

/**
 * banlist.class.php
 * Classe para verificação do banimento de usuário.
 * 
 * @author Cardoso
 */
require_once("mysql.class.php"); //apenas para garantir.
require_once("sessaoUsuario.class.php");

class banlist extends sessaoUsuario {

    public function cBLbrowser($browser, $version=null) {
        $result = false;
        if (!empty($browser)) {
            $sql = "SELECT `browser`, `version` FROM `banlist` WHERE `browser` = '$browser';";
            $row = $this->QueryArray($sql, MYSQL_ASSOC);
            for ($i = 0; $i < sizeof($row); $i++) {
                if (!empty($row[$i]['version'])) {
                    if (($row[$i]['browser'] == $browser) && ($row[$i]['version'] == $version)) {
                        $result = true;
                        break;
                    }
                } else {
                    if ($row[$i]['browser'] == $browser) {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function cBLip($ip) {
        $result = false;
        if (!empty($ip)) {
            $sql = "SELECT `ipv4` FROM `banlist` WHERE `ipv4` = '$ip';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['ipv4'] == $ip) {
                $result = true;
            }
        }
        return $result;
    }

    public function cBLos($os) {
        $result = false;
        if (!empty($os)) {
            $sql = "SELECT `so` FROM `banlist` WHERE `so` = '$os';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['so'] == $os) {
                $result = true;
            }
        }
        return $result;
    }

    public function cBLusuario($usuario) {
        $result = false;
        if (!empty($usuario)) {
            $sql = "SELECT `usuario` FROM `banlist` WHERE `usuario` = '$usuario';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['usuario'] == $usuario) {
                $result = true;
            }
        }
        return $result;
    }

    public function cBLgrupo($grupo) {
        $result = false;
        if (!empty($grupo)) {
            $sql = "SELECT `grupo` FROM `banlist` WHERE `grupo` = '$grupo';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['grupo'] == $grupo) {
                $result = true;
            }
        }
        return $result;
    }

    public function cBLdominio($dominio) {
        $result = false;
        if (!empty($dominio)) {
            $sql = "SELECT `dominio` FROM `banlist` WHERE `dominio` = '$dominio';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['dominio'] == $dominio) {
                $result = true;
            }
        }
        return $result;
    }

    public function cBLfp($fp) { //checar assinatura do navegador.
        $result = false;
        if (!empty($fp)) {
            $sql = "SELECT `fingerprint` FROM `banlist` WHERE `fingerprint` = '$fp';";
            $row = $this->QuerySingleRowArray($sql, MYSQL_ASSOC);
            if ($row['fingerprint'] == $fp) {
                $result = true;
            }
        }
        return $result;
    }

    public function verificarBan($fingerprint=null) {
        $result = false;
        $browser = $this->getBrowser();
        if ($this->cBLbrowser($browser['name'], $browser['version'])) {
            $result = true;
        }
        if ($this->cBLos($browser['platform'])) {
            $result = true;
        }
        if ($this->cBLip($this->getIP())) {
            $result = true;
        }
        if ($this->cBLusuario($this->getUserId())) {
            $result = true;
        }
        if ($this->cBLdominio($this->getDominioByUserId($this->getUserId()))) {            
            $result = true;
        }
        if ($this->cBLgrupo($this->getGrupoId())) {
            $result = true;
        }
        if ($this->cBLfp($fingerprint)) {
            $result = true;
        }
        return $result;
    }

}

?>
