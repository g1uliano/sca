<?php

/* * **************************************************************** */
/* Sistemas Controlador de Acesso
  /* Controle Administrativo
  /* functions.basic.php - criado e mantido por: Giuliano Cardoso
  /* Ùltima alteração: 31 de março de 2011 às 08:03 hrs.
  /****************************************************************** */

function mysql_query2json($sql) {
    $db = new MySQL();
    $db->Query($sql);
    $array_n = array(); //cria um array vazio
    $array_i = array(); //cria outro array vazio.
    while ($row = $db->RowArray(NULL, MYSQL_NUM)) { // Faz a consulta e retorna um array.
        array_push($array_n, $row[1]); //Empilha a consulta no array.
        array_push($array_i, $row[0]); //Empilha a consulta no array. (de novo!)
    }
    $array_t = array_combine($array_i, $array_n);
    $db = NULL;
    print json_encode($array_t); //retorna tudo prontinho, uma beleza. Parece atá magia, mas não, é tecnologia.
}

function sql2json($sql) {
    $db = new MySQL();
    $array = $db->QueryArray($sql, MYSQL_ASSOC);
    $db = NULL;
    print json_encode($array); //retorna tudo prontinho, uma beleza. Parece atá magia, mas não, é tecnologia.
}

function generatePassword($length = 12) {

    //source: http://www.laughing-buddha.net/php/lib/password
    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
        $length = $maxlength;
    }

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

        // have we already used this character in $password?
        if (!strstr($password, $char)) {
            // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
        }
    }

    // done!
    return $password;
}

function curPageURL() {
    $pageURL = 'http';
    if (@$_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function check_email_exists($email) {
    $db = new MySQL();
    $sql = "SELECT * FROM `usuarios` where `email` = '" . $email . "';";
    $array = $db->QuerySingleRowArray($sql);
    if (empty($array['email'])) {
        return false;
    } else {
        return true;
    }
}

function convert_datetime($str) {

    list($date, $time) = explode(' ', $str);
    list($year, $month, $day) = explode('-', $date);
    list($hour, $minute, $second) = explode(':', $time);

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    return $timestamp;
}

function destruir_sessao() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

?>