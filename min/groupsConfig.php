<?php

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 * */
return array(
    'login_css' => array(dirname(__FILE__) . '/../css/login.css'),
    'login_js' => array(dirname(__FILE__) . '/../js/jquery-1.6.2.min.js',
        dirname(__FILE__) . '/../js/jquery.blockUI.js',
        dirname(__FILE__) . '/../js/generic.functions.js',
        dirname(__FILE__) . '/../js/biBigInt.js',
        dirname(__FILE__) . '/../js/biRSA.js',
        dirname(__FILE__) . '/../js/biMontgomery.js',
        dirname(__FILE__) . '/../js/gradient.js',
        dirname(__FILE__) . '/../js/jsper.js',
        dirname(__FILE__) . '/../js/login.screen.js'),
    'admin_css' => array(dirname(__FILE__) . '/../admin/css/admin.css',
        dirname(__FILE__) . '/../css/Aristo/jquery-ui-1.8.7.custom.css',
        dirname(__FILE__) . '/../css/tablesorter/blue/style.css'
    ),
    'admin_js' => array(dirname(__FILE__) . '/../js/jquery-1.6.2.min.js',
        dirname(__FILE__) . '/../js/jquery-ui-1.8.7.custom.min.js',
        dirname(__FILE__) . '/../js/jquery.blockUI.js',
        dirname(__FILE__) . '/../js/jquery.selectboxes.js',
        dirname(__FILE__) . '/../js/jquery.validate.min.js',
        dirname(__FILE__) . '/../js/jquery.tablesorter.js',
        dirname(__FILE__) . '/../js/jsper.js',
        dirname(__FILE__) . '/../js/generic.functions.js',
        dirname(__FILE__) . '/../js/biBigInt.js',
        dirname(__FILE__) . '/../js/biRSA.js',
        dirname(__FILE__) . '/../js/biMontgomery.js',
        dirname(__FILE__) . '/../admin/js/admin.js',
        dirname(__FILE__) . '/../admin/js/usuario.actions.js',
        dirname(__FILE__) . '/../admin/js/grupo.actions.js',
        dirname(__FILE__) . '/../admin/js/dominio.actions.js',
        dirname(__FILE__) . '/../admin/js/eventos.actions.js'
    ),
    'exp_js' => array(dirname(__FILE__) . '/../js/jquery-1.6.2.min.js',
        dirname(__FILE__) . '/../js/jquery-ui-1.8.7.custom.min.js',
        dirname(__FILE__) . '/../js/jquery.blockUI.js',
        dirname(__FILE__) . '/../js/jquery.selectboxes.js',
        dirname(__FILE__) . '/../js/jquery.validate.min.js',       
        dirname(__FILE__) . '/../js/generic.functions.js',
        dirname(__FILE__) . '/../js/biBigInt.js',
        dirname(__FILE__) . '/../js/biRSA.js',
        dirname(__FILE__) . '/../js/biMontgomery.js',
        dirname(__FILE__) . '/../js/gradient.js',
        dirname(__FILE__) . '/../expired/js/troca.senha.js',
    )
);