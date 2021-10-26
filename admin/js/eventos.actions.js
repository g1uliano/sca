/*********************************************************************
 Sistemas Controlador de Acesso
 Controle de Login
 dominio.actions.js - criado e mantido por: Giuliano Cardoso
 última alteração: 23 de julho de 2011 às 14:47 hrs.
/********************************************************************/
//falta atualizar todos os recursos.
function detalhar_eventos() {
    $('a[name=detalhar]').click( function() {
        $.get( "server-side/?detalhar_evento="+$(this).attr('uid'), function(msg) {
            $('#visualizar_eventos').html('Por favor aguarde, carregando eventos...');
            $('#visualizar_eventos').html(msg);
            $('a[id=eventos]').click(function() {
                $('#visualizar_eventos').html('Por favor aguarde, carregando eventos...');
                setTimeout('exibe_eventos()', 1);
            });        
        });
    });
}

function exibe_eventos() {
    $.get("server-side/?get_eventos=15",  function(data) {
        $('#visualizar_eventos').html(data);
        $("table").tablesorter();
        detalhar_eventos();
        $('a[id=eventos_todos]').click(function() {
            $('#visualizar_eventos').html('Por favor aguarde, carregando eventos...');
            $.get("server-side/?get_eventos=1000",  function(data) {                
                $('#visualizar_eventos').html('Por favor aguarde, carregando eventos...');
                $('#visualizar_eventos').html(data);
                detalhar_eventos();
            });
        });        

    });
}




$('a[id=eventos]').click(function() {
    $('#visualizar_eventos').html('Por favor aguarde, carregando eventos...');
    setTimeout('exibe_eventos()', 1);
});

$('#eventos_tabs').tabs();
$('#cd_error_msg').hide();
