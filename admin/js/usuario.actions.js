/********************************************************************
 Sistemas Controlador de Acesso
 Controle de Login
 usuario.actions.js - criado e mantido por: Giuliano Cardoso
 última alteração: 23 de julho de 2011 às 07:06 hrs.
********************************************************************/
//todas as vezes que enviar informações sensíveis, favor criptografar primeiro.
var $keyEncrypt = new biRSAKeyPair("1c8b622ddf189407642fd37bf5e041f5", "0", "1d65d1033aa8c90edb2e44ff0b6e33f9");

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
                data: "delete_usr="+$('select[name=usuario_id] :selected').val(),
                success: function(q){
                    parsed = $.parseJSON(q);
                    $(this).waitScreen(false);
                    if (parsed[0]) {
                        $('#seleciona_usr').show();
                        $('#usr_selecionado').hide();
                        $('form[name=feditar_usuario]')[ 0 ].reset();
                        $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                        $('select[name=usuario_id]').removeOption(/./).getLista('usuario',true,$('select[name=dominio_id] :selected').val());
                    } else {
                        $('#generic_purpose_dialog').dialog('open').text("Erro ao tentar excluir usuário");
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

$("#cr_error_msg").html("").hide();
$('#usuario_tabs').tabs();

jQuery.validator.addMethod("noSpace", function(value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "Espaços não são permitidos");

$('#cr_usuario').keypress(function() {
    $("#cr_usuario").val($("#cr_usuario").val().toLowerCase());
}).focus(function() {
    $("#cr_usuario").val($("#cr_usuario").val().toLowerCase());
});

$("select[name=dominio_id]").change(function() {
    $('select[name=usuario_id]').removeOption(/./).getLista('usuario',false,$(this).val()); 
});

$("select[name=ed_usr_dominio]").change(function() {
    $('select[name=ed_usr_grupo]').removeOption(/./).getLista('grupo',false,$(this).val()); 
});

$("#fcriar_usuario").validate({
    rules: {
        cr_usuario:  {
            required: true,
            minlength: 3,
            noSpace: true
        },
        cr_fullname: {
            required: true,
            minlength: 3
        },
        cr_email:  {
            required: true,
            email: true
        },
        cr_senha:  {
            required: true,
            minlength: 5
        },
        cr_confirmar_senha:  {
            required: true,
            minlength: 5,
            equalTo: "#cr_senha"
        }
    },
    messages: {
        cr_usuario:  {
            required: "Por favor entre com um nome de usuário.",
            minlength: "Entre com o usuário, com no minímo 3 caracteres.",
            noSpace: "Espaço não é permitido no campo de usuário"
        },
        cr_fullname:  {
            required: "Por favor entre com o nome do usuário.",
            minlength: "Entre com o nome completo, com no minímo 3 caracteres."
        },
        cr_senha: {
            required: "Por favor, entre com uma senha.",
            minlength: "Entre com a senha com no minímo 5 caracteres."
        },
        cr_confirmar_senha: {
            required: "Por favor, confirme a senha.",
            minlength: "Entre com a senha com no minímo 5 caracteres.",
            equalTo: "As senhas informadas devem ser iguais."
        },
        cr_email:  "Por favor entre com um e-mail válido."
    },
    errorPlacement: function() {}, //anular exibição lateral de erros.
    showErrors: function(errorMap, errorList){
        errorP = new Array();
        $.each(errorList, function(i, error) {
            if ((typeof(error)!="undefined") && (error!='')) {
                errorP[i]=error;
            }
        });
        this.defaultShowErrors();
        if (typeof(errorP[0])!="undefined") {
            $("#cr_error_msg").html(errorP[0].message).show();
        } else {
            $("#cr_error_msg").html('').hide();
        }
    },
    submitHandler:  function() {
        if ($('select[name=usr_grupo]').val()!=0)  {
            //vamos enviar a senha do usuário pro servidor, uma cada de criptografia, não mata ninguém.
            $("#cr_usuario").val($("#cr_usuario").val().toLowerCase());
            $("#cr_error_msg").html("").hide();

            enc  = $('input[name=cr_usuario]').val()+","; 
            enc += $('input[name=cr_fullname]').val()+",";
            enc += $('input[name=cr_email]').val()+","; 
            enc += $('input[name=cr_senha]').val()+","; 
            enc += $('select[name=cr_lst_dominio]').val()+","; 
            enc += $('select[name=usr_grupo]').val()+",";      
            enc += $('select[name=cr_expired]').val(); 
            
            encryptedString = $keyEncrypt.biEncryptedString(enc);
            enc = base64_encode(encryptedString);
            $(this).waitScreen(true);
            $.ajax({
                type: "POST",
                url: "server-side/",
                data: "create_usr="+enc,
                success: function(q){
                    parsed = $.parseJSON(q);
                    $(this).waitScreen(false);
                    $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                    $('select[name=usuario_id]').removeOption(/./).getLista('usuario',true,$('select[name=dominio_id] :selected').val());
                    if (parsed[0]==0) {
                        $('form[name=fcriar_usuario]')[ 0 ].reset();
                    } else if (parsed[0]==1062) {
                        $('input[name=cr_usuario]').val('');
                    } else if (parsed[0]==1063) {
                        $('input[name=cr_email]').val('');
                    } else {
                        console.log(parsed);
                    }
                }
            });
        } else {
            $('#generic_purpose_dialog').dialog('open').text("Por favor selecione um grupo válido. Para isto, troque de domínio ou crie um grupo para o domínio atual.");
        }
            
    }
});

$("#feditar_usuario").validate({
    rules: {
        ed_usuario:  {
            required: true,
            minlength: 3,
            noSpace: true
        },
        ed_fullname: {
            required: true,
            minlength: 3
        },
        ed_email:  {
            required: true,
            email: true
        },
        ed_senha:  {
            minlength: 5
        },
        ed_confirmar_senha:  {
            minlength: 5,
            equalTo: "#ed_senha"
        }
    },
    messages: {
        ed_usuario:  {
            required: "Por favor entre com um nome de usuário.",
            minlength: "Entre com o usuário, com no minímo 3 caracteres.",
            noSpace: "Espaço não é permitido no campo de usuário"
        },
        ed_fullname:  {
            required: "Por favor entre com o nome do usuário.",
            minlength: "Entre com o nome completo, com no minímo 3 caracteres."
        },
        ed_senha: {
            minlength: "Entre com a senha com no minímo 5 caracteres."
        },
        ed_confirmar_senha: {
            minlength: "Entre com a senha com no minímo 5 caracteres.",
            equalTo: "As senhas informadas devem ser iguais."
        },
        ed_email:  "Por favor entre com um e-mail válido."
    },
    errorPlacement: function() {}, //anular exibição lateral de erros.
    showErrors: function(errorMap, errorList){
        errorP = new Array();
        $.each(errorList, function(i, error) {
            if ((typeof(error)!="undefined") && (error!='')) {
                errorP[i]=error;
            }
        });
        this.defaultShowErrors();
        if (typeof(errorP[0])!="undefined") {
            $("#ed_error_msg").html(errorP[0].message).show();
        } else {
            $("#ed_error_msg").html('').hide();
        }
    },
    submitHandler:  function() {
        $("#ed_usuario").val($("#ed_usuario").val().toLowerCase());
        $("#ed_error_msg").html("").hide();
        enc  = $('input[name=ed_usuario]').val()+",";  //0
        enc += $('input[name=ed_fullname]').val()+","; //1
        enc += $('input[name=ed_email]').val()+","; //2
        if ($('input[name=ed_senha]').val()!='') {
            enc += $('input[name=ed_senha]').val()+","; //3
        } else {
            enc += "0,";
        }
        enc += $('select[name=ed_usr_dominio]').val()+"," //4
        enc += $('select[name=ed_usr_grupo]').val()+","; //5
        enc += $('select[name=usuario_id]').val()+',';  //6
        enc += $('select[name=ed_expired]').val()+',';
        enc += $('select[name=ed_banuser]').val();
        encryptedString = $keyEncrypt.biEncryptedString(enc);
        enc = base64_encode(encryptedString);
        $(this).waitScreen(true);
        $.ajax({
            type: "POST",
            url: "server-side/",
            data: "alter_usr="+enc,
            success: function(q){
                parsed = $.parseJSON(q);
                $(this).waitScreen(false);
                $('#generic_purpose_dialog').dialog('open').text(parsed[1]);
                if (parsed[0]==0) {
                    $('select[name=usuario_id]').removeOption(/./).getLista('usuario',true,$('select[name=dominio_id] :selected').val());
                    $('#seleciona_usr').show();
                    $('#usr_selecionado').hide();
                    $('form[name=feditar_usuario]')[ 0 ].reset();
                    $('#seleciona_usr').show();
                    $('#usr_selecionado').hide();
                    $('select[name=dominio_id]').change();
                } else {
                    console.log(parsed);
                }
            }
        });

    }
});

$('select[name=cr_lst_dominio]').change(function() {
    $('select[name=usr_grupo]').removeOption(/./).getLista('grupo',false,$(this).val()); 
});

$('input[name=limpar_usuario]').click(function() {
    $('form[name=fcriar_usuario]')[ 0 ].reset();
    $('select[name=cr_lst_dominio]').change();
    $("#cr_error_msg").html("");
});

//edição de usuário
$('input[name=usr_ok]').click(function() {    
    $('select[name=ed_usr_grupo]').removeOption(/./).getLista('grupo',false,$('select[name=dominio_id]').val());
    if ($('select[name=usuario_id] :selected').val()!=0) {
        var notLoaded = true;
        if ($.jsper.get('dominios_json')!=null) { //evitar o get desnecessário de informação.
            $.each($.parseJSON($.jsper.get('usuarios_json')), function(i,registry) {
                if (registry.dominio==$('select[name=dominio_id]').val()) {   
                    if (registry.id==$('select[name=usuario_id]').val()) {
                        $('input[name=ed_usuario]').val(registry.login);
                        $('input[name=ed_fullname]').val(registry.fullname);
                        $('input[name=ed_email]').val(registry.email);
                        $('select[name=ed_usr_grupo]').val(registry.grupo);
                        $('select[name=ed_usr_dominio]').val(registry.dominio);
                        $('select[name=ed_banuser]').val(registry.banido);
                        $('#seleciona_usr').hide();
                        $('#usr_selecionado').show();                    
                        notLoaded = false;
                    }                                  
                }
            });
        } 
        if (notLoaded) {
            $(this).waitScreen(true);
            $.ajax({
                type: "POST",
                url: "server-side/",
                data: "get_id="+$('select[name=usuario_id]').val(),
                success: function(q){
                    $(this).waitScreen(false);
                    // id,login,fullname,email,grupo
                    parsed = $.parseJSON(q); 
                    $('input[name=ed_usuario]').val(parsed[1]);
                    $('input[name=ed_fullname]').val(parsed[2]);
                    $('input[name=ed_email]').val(parsed[3]);
                    $('select[name=ed_usr_grupo]').val(parsed[4]);
                    $('select[name=ed_usr_dominio]').val(parsed[5]);
                    $('#seleciona_usr').hide();
                    $('#usr_selecionado').show();
                }
            });

        }
    } else {
        $('#generic_purpose_dialog').dialog('open').text("Impossível realizar edição, pois, nenhum usuário foi selecionado. Por favor, selecione outro domínio.");
    }
});

$('input[name=ed_excluir_usuario]').click(function() {
    usr = $('select[name=usuario_id] :selected').val();
    if (usr==1) {
        $('#generic_purpose_dialog').dialog('open').text("Este usuário não pode ser excluido do sistema.");
    } else {
        msg = "Deseja realmente excluir este usuário?";
        $('#generic_confirm').text(msg).dialog('open');
    }
});

//cancelar edição de usuário.
$('input[name=cancelar_usuario]').click(function() {
    $('#seleciona_usr').show();
    $('#usr_selecionado').hide();
    $('form[name=feditar_usuario]')[ 0 ].reset();
    $('#seleciona_usr').show();
    $('#usr_selecionado').hide();
});
