/*********************************************************************
 Sistemas Controlador de Acesso
 Controle de Login
 grupo.actions.js - criado e mantido por: Giuliano Cardoso
 última alteração: 16 de junho de 2011 às 14:31 hrs.
/********************************************************************/
function sortoptions(sort)
{
    var $this = $(this);
    $this.sortOptions(sort.dir == "asc" ? true : false);
}

$('#grupo_tabs').tabs();
$('#cg_error_msg').hide();
$('#loaded_grupo').hide();
$('#select_group').show();

$('a[href=#editar_grupo]').click(function(){
    $('#select_group').show();
    $('#loaded_grupo').hide();
})

$('input[name=cr_s_grupo]').click(function() {
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
                    data: "create_group="+$('input[name=cr_grupo]').val()+"&dominio="+$('select[name=g_ed_lst_dominio] :selected').val(),
                    success: function(q){
                        parsed = $.parseJSON(q);
                        $(this).waitScreen(false);
                        $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                        if ((parsed[0]==0) || (parsed[0]==1062)) {
                            $('input[name=cr_grupo]').val('');
                            $('select[name=ed_lst_grupo]').removeOption(/./).getLista('grupo',true,$('select[name=g2_ed_lst_dominio]').val());
                        } else {
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
    if ($('input[name=cr_grupo]').val()!="") {
        $('#cg_error_msg').text("").hide();
        msg = "Deseja realmente criar este grupo?";
        $('#generic_confirm').text(msg).dialog('open');
    } else {
        $('#cg_error_msg').text("Por favor, entre com um nome para o grupo.").show();
    }
});

$("select[name=g2_ed_lst_dominio]").change(function() {
    $('select[name=ed_lst_grupo]').removeOption(/./).getLista('grupo',false,$(this).val()); 
});

$('input[name=cr_c_grupo]').click(function() {
    $('input[name=cr_grupo]').val('');
});

$('input[name=group_ok]').click(function() {
    if ($('select[name=ed_lst_grupo] :selected').val()!=0) {
        $('#ed_n_grupo').val($('select[name=ed_lst_grupo] :selected').text());
        $('#select_group').hide();
        $('#loaded_grupo').show();
    } else {
        $('#generic_purpose_dialog').dialog('open').text("Não existe grupo a ser selecionado, crie um ou selecione outro domínio.");
    }
});

$('input[name=del_grupo]').click(function() {
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
                    data: "delete_group="+$('select[name=ed_lst_grupo]').val(),
                    success: function(q){
                        parsed = $.parseJSON(q);
                        $(this).waitScreen(false);
                        if (parsed[0]!=0) {                                       
                            $('select[name=ed_lst_grupo]').removeOption(/./).getLista('grupo',true,$('select[name=g2_ed_lst_dominio]').val());
                        }                         
                        $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                        $('#select_group').show();
                        $('#loaded_grupo').hide();
                    }
                });
            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        },
        modal: true
    });
    msg  = 'Deseja realmente excluir o grupo <b><u>'
    msg += $('select[name=ed_lst_grupo] :selected').text() + '</u></b>?'
    msg += "<br/><br />"
    msg += "<i><b>AVISO:</b> "
    msg += "Todos os registros diretamente relacionados "
    msg += "(usuários pertecentes a este grupo) também serão excluídos."
    msg += "<b><u>Esta operação não pode ser revertida.<b></u></i>" 
    
    $('#generic_confirm').html(msg).dialog('open');
});

$('input[name=alt_c_grupo]').click(function(){
    $('#select_group').show();
    $('#loaded_grupo').hide();
})

$('input[name=alt_grupo]').click(function(){
    if ($('input[name=ed_n_grupo]').val()!='') {
        code = base64_encode($('select[name=ed_lst_grupo]').val()+","+$('input[name=ed_n_grupo]').val()+","+$('select[name=desativar_grupo]').val());
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
                        data: "alter_group="+code,
                        success: function(q){
                            parsed = $.parseJSON(q);                            
                            $(this).waitScreen(false);
                            $('select[name=ed_lst_grupo]').removeOption(/./).getLista('grupo',true,$('select[name=g2_ed_lst_dominio]').val());
                            $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                            $('#select_group').show();
                            $('#loaded_grupo').hide();
                        }
                    });
                },
                "Cancelar": function() {
                    $(this).dialog("close");
                }
            },
        modal: true
        });
        msg = "Deseja realmente atualizar o nome deste grupo?";
        $('#generic_confirm').text(msg).dialog('open');
    } else {
        $('#generic_purpose_dialog').dialog('open').text("O nome do grupo não pode ser vazio.");
    }
})
