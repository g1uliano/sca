/*********************************************************************
 Sistemas Controlador de Acesso
 Controle de Login
 dominio.actions.js - criado e mantido por: Giuliano Cardoso
 última alteração: 23 de julho de 2011 às 14:47 hrs.
/********************************************************************/
//falta atualizar todos os recursos.

function sortoptions(sort)
{
    var $this = $(this);
    $this.sortOptions(sort.dir == "asc" ? true : false);
}

$('#dominio_tabs').tabs();
$('#cd_error_msg').hide();
$('#loaded_dominio').hide();
$('input[name=cr_s_dominio]').click(function() {
    $('#generic_confirm').dialog({
        autoOpen: false,
        width: 300,
        position: [30,60],
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
                $(this).waitScreen(true);
                $.ajax({
                    type: "POST",
                    url: "server-side/",
                    data: "create_dominio="+$('input[name=cr_dominio]').val(),
                    success: function(q){
                        parsed = $.parseJSON(q);
                        $(this).waitScreen(false);
                        $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                        if ((parsed[0]==0) || (parsed[0]==1062)) {
                            $('input[name=cr_dominio]').val('');
                            $('select[name=ed_lst_dominio]').removeOption(/./).getLista('dominio',true,null);       
                        } else {
                            console.log(parsed);
                        }
                    }
                });
            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        },
        modal: true
    });
    if ($('input[name=cr_dominio]').val()!="") {
        $('#cd_error_msg').text("").hide();
        msg = "Deseja realmente criar este dominio?";
        $('#generic_confirm').text(msg).dialog('open');
    } else {
        $('#cd_error_msg').text("Por favor, entre com um nome para o dominio.").show();
    }
});

$('input[name=cr_c_dominio]').click(function() {
    $('input[name=cr_dominio]').val('');
});

$('a[href=#editar_dominio]').click(function() {
    $('#select_dominio').show();
    $('#loaded_dominio').hide();
})

$('input[name=dominio_ok]').click(function() {
    $('#ed_n_dominio').val($('select[name=ed_lst_dominio] :selected').text());
    $('#select_dominio').hide();
    $('#loaded_dominio').show();
});

$('input[name=del_dominio]').click(function() {
    $('#generic_confirm').dialog({
        autoOpen: false,
        width: 300,
        position: [30,60],
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
                $(this).waitScreen(true);
                $.ajax({
                    type: "POST",
                    url: "server-side/",
                    data: "delete_dominio="+$('select[name=ed_lst_dominio]').val(),
                    success: function(q){
                        parsed = $.parseJSON(q);
                        $(this).waitScreen(false);
                        if (parsed[0]!=0) {
                            $('select[name=ed_lst_dominio]').removeOption(/./).getLista('dominio',true,null);
                        }
                        $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                        $('#select_dominio').show();
                        $('#loaded_dominio').hide();
                    }
                });
            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        },
        modal: true
    });
    msg  = 'Deseja realmente excluir o domínio <b><u>'
    msg += $('select[name=ed_lst_dominio] :selected').text() + '</u></b>?'
    msg += "<br/><br />"
    msg += "<i><b>AVISO:</b> "
    msg += "Todos os registros diretamente relacionados "
    msg += "(grupos, usuários, etc) também serão excluídos."
    msg += "<b><u>Esta operação não pode ser revertida.<b></u></i>" 
    $('#generic_confirm').html(msg).dialog('open');
});

$('input[name=alt_c_dominio]').click(function(){
    $('#select_dominio').show();
    $('#loaded_dominio').hide();
})

$('input[name=alt_dominio]').click(function(){
    if ($('input[name=ed_n_dominio]').val()!='') {
        code = base64_encode($('select[name=ed_lst_dominio]').val()+","+$('input[name=ed_n_dominio]').val()+","+$('select[name=desativar_dominio]').val());
        $('#generic_confirm').dialog({
            autoOpen: false,
            width: 300,
            position: [30,60],
            buttons: {
                "Ok": function() {
                    $(this).dialog("close");
                    $(this).waitScreen(true);
                    $.ajax({
                        type: "POST",
                        url: "server-side/",
                        data: "alter_dominio="+code,
                        success: function(q){
                            parsed = $.parseJSON(q);
                            $(this).waitScreen(false);
                            $('select[name=ed_lst_dominio]').removeOption(/./).getLista('dominio',true,null);
                            $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                            $('#select_dominio').show();
                            $('#loaded_dominio').hide();
                        }
                    });
                },
                "Cancelar": function() {
                    $(this).dialog("close");
                }
            },
            modal: true
        });
        msg = "Deseja realmente atualizar o nome deste dominio?";
        $('#generic_confirm').text(msg).dialog('open');
    } else {
        $('#generic_purpose_dialog').dialog('open').text("O nome do dominio não pode ser vazio.");
    }
})
