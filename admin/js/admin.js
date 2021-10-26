/********************************************************************
 Sistemas Controlador de Acesso
 Controle Administrativo
 admin.js - criado e mantido por: Giuliano Cardoso
 última alteração: 22 de julho de 2011 às 14:30 hrs.
********************************************************************/
/* Este arquivo ainda está em desenvolvimento.
/* estado padrão dos div */
var current_window=$('div[app=true]');
var iterator=0;
var first = true;

jQuery.fn.getLista = function(type,update,dominio) {    
    var $this = $(this);
    var iq=0;
    if (($.jsper.get(type+'s_json')==null) || (update==true)) {
        $.get("server-side/?get="+type+"s",  function(data) {
            if ((data!='') || (data!=null)) {
                $.jsper.set(type+'s_json', data);        
                $.each($.parseJSON(data), function(i,registry) {                
                    if ((registry.dominio==dominio) || (dominio==null)) {           
                        $this.addOption(registry.id,type=='usuario'?registry.login:registry.nome);
                        iq++;
                    } 
                });
                if (iq==0) {
                    $this.addOption(0,"-- Nenhum --");
                }
            }
        });     
    } else {
        $.each($.parseJSON($.jsper.get(type+'s_json')), function(i,registry) {                
            if ((registry.dominio==dominio) || (dominio==null)) {           
                $this.addOption(registry.id,type=='usuario'?registry.login:registry.nome);
                iq++;
            } 
        });
        if (iq==0) {
            $this.addOption(0,"-- Nenhum --");
        }
    }        
};

jQuery.fn.waitScreen = function(status) {
    if (status) {
        $.blockUI(
        { 
            css: {   
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            }
        })
    } else {
        $.unblockUI()
    }
}

jQuery.fn.makeUnselectable = function() {
    var $this = $(this);

    if ($this.nodeType == 1) {
        $this.unselectable = true;
    }
    var child = $this.firstChild;
    while (child) {
        makeUnselectable(child);
        child = child.nextSibling;
    }
}

function checkForEnter (event) {
    currentBoxNumber = textboxes.index(this);
    currBox = textboxes[currentBoxNumber];
    
    if (event.keyCode == 13) {   
        if (textboxes[currentBoxNumber + 1] != null) {           
            nextBox = textboxes[currentBoxNumber + 1]
            if (currBox.value!='') {
                nextBox.focus();
                event.preventDefault();
                return false;
            }
        }
    }
    
    if (event.keyCode == 8) {
        if (textboxes[currentBoxNumber - 1] != null) {
            prevBox = textboxes[currentBoxNumber - 1]
            if (currBox.value=='') {
                prevBox.focus();
                event.preventDefault();
                return false;
            }
        }
    }
    return true; //precisar retornar algo.
}

function estado_padrao() {
    //aplicativo de usuário
    $('input[type=text]').val('');
    $('div[app=true]').hide();
    //->  subordinado a #usuario_app
    /**/ $("#seleciona_usr").show(); //seleção de usuário
    /**/ $("#usr_selecionado").hide(); //tela de edição de usuário
    //->  subordinado a #grupo_app
    /**/ $('#loaded_grupo').hide();
    /**/ $('#select_group').show();
    //->  subordinado a #dominio_app
    /**/ $('#select_dominio').show();
    /**/ $('#loaded_dominio').hide();
}

function checa_dados() {
    forceUpdate = false; 
    if (first) {
        forceUpdate = true;
        first = false;
    }
    if (iterator==5) {
        forceUpdate = true;
        iterator=0;
    } else {
        iterator++;
    }
    if (($.jsper.get('dominios_json')==null) || (forceUpdate))  {
        $.get("server-side/?get=dominios",  function(data) {
            $.jsper.set('dominios_json', data);
        });
    }

    if (($.jsper.get('grupos_json')==null) || (forceUpdate))  {
        $.get("server-side/?get=grupos",  function(data) {
            $.jsper.set('grupos_json', data);
        });
    }

    if (($.jsper.get('usuarios_json')==null)  || (forceUpdate))  {
        $.get("server-side/?get=usuarios",  function(data) {
            $.jsper.set('usuarios_json', data);
        });
    }
    
    $.ajax({
        type: "POST",
        url: "server-side/",
        data: "check=session",
        success: function(q){
            if ((q!='') && (typeof(q)!="undefined")) {
                window.location.href=q;
            }
        }
    });
    setTimeout('checa_dados()', 15000); // a cada 15 segundos verifica a sessão.
}

$().ready(function() {
    //desabilita a tecla 'esc'.
    $(document).bind("keydown.cbox_close", function (e) {
        if (e.keyCode === 27) {
            e.preventDefault();
            cboxPublic.close();
        }
    });
    
    $.ajaxSetup({
        ifModified: true
    });
    
    $('#loading').hide();
    
    $('#loading').ajaxStart(function() {
        $(this).show();
    });
    $('#loading').ajaxStop(function() {
        $(this).hide();
    });
    
    estado_padrao();
    
    $(this).bind("contextmenu", function(e) {
        e.preventDefault();
    }); //desabilita click com o botão direito do mouse.

    //converter todos os enter em tab
    textboxes = $("input, select, textarea");

    if ($.browser.mozilla) {
        $(textboxes).keypress (checkForEnter);
    } else {
        $(textboxes).keydown (checkForEnter);
    }

    /* desabilita o submit de todos os formulários.
    $('input[type=submit]').click( function(event) {
        event.preventDefault();
        return false;
    });
    */
    checa_dados();

    /********** menu principal *************/
    
    $('a[id=usuarios]').click(function() {
        estado_padrao();
        //pega os grupos dos usuários
        $("#cr_error_msg,#ed_error_msg").html('').hide();
        $('a[href=#editar_usuario]').click(function () {
            $('select[name=usuario_id]').removeOption(/./).getLista('usuario',false,$('select[name=dominio_id] :selected').val());
            $('#seleciona_usr').show();
            $('#usr_selecionado').hide();
            $('form[name=feditar_usuario]')[ 0 ].reset();
        })        
        $('select[name=cr_lst_dominio],select[name=dominio_id],select[name=ed_usr_dominio]').removeOption(/./).getLista('dominio',false,null);
        //,select[name=ed_usr_grupo]
        $('select[name=usr_grupo],select[name=ed_usr_grupo]').removeOption(/./).getLista('grupo',false,$('select[name=cr_lst_dominio] :selected').val());
        //pega os usuários
        $('select[name=usuario_id]').removeOption(/./).getLista('usuario',false,$('select[name=dominio_id] :selected').val());
        $("input[type=text],input[type=password]").val(''); //limpa todos os campos.
        //validação de campos em #criar_usuario.
        //carrega todos os dados de validação e então apresenta os campos.
        if (current_window.is(":visible")) {
            current_window.hide();
        }
        current_window = $("#usuario_app");
        current_window.show(); //então exibe.
        
    });

    $('a[id=grupos]').click(function() {
        $('#cg_error_msg').hide();
        //g_ed_lst_dominio
        $('select[name=g_ed_lst_dominio],select[name=g2_ed_lst_dominio]').removeOption(/./).getLista('dominio',false,null);
        $('select[name=ed_lst_grupo]').removeOption(/./).getLista('grupo',false,$("select[name=g2_ed_lst_dominio]").val());
        estado_padrao();
        if (current_window.is(":visible")) {
            current_window.hide();
        }
        current_window = $("#grupo_app");
        current_window.show();

    });
    
    $('a[id=dominios]').click(function() {
        $('#cd_error_msg').hide();
        $('select[name=ed_lst_dominio]').removeOption(/./).getLista('dominio',false,null);
        estado_padrao();
        if (current_window.is(":visible")) {
            current_window.hide();
        }
        current_window = $("#dominio_app");
        current_window.show();

    });
    $('a[id=eventos]').click(function() {
        estado_padrao();
        if (current_window.is(":visible")) {
            current_window.hide();
        }
        current_window = $("#evento_app");
        current_window.show();        
    });
    /********** encerramento de sessão *************/
    //criar diálogo de encerramento de sessão.
    $('#dialog').dialog({
        autoOpen: false,
        width: 300,
        position: [30,60],
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
                $.ajax({
                    type: "POST",
                    url: "server-side/",
                    data: "destroy=session",
                    success: function(q){
                        if (q!='') {
                            window.location.href=q;
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
    $('#generic_purpose_dialog').dialog({
        autoOpen: false,
        width: 300,
        position: [30,60],
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
            }
        },
        modal: true
    });

    //chamar diálogo de encerramento de sessão
    $('a[id=sair]').click(function() {
        $('#dialog').dialog('open');
        return false;
    });
});
