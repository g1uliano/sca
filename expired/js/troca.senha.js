/*******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  index.php - criado e mantido por: Giuliano Cardoso
  Ùltima alteração: 23 de março de 2011 às 16:06 hrs.
  ******************************************************************/
var $keyEncrypt = new biRSAKeyPair("1c8b622ddf189407642fd37bf5e041f5", "0", "1d65d1033aa8c90edb2e44ff0b6e33f9");

jQuery.validator.addMethod("notEqual", function(value, element, param) {
    return value == $(param).val() ? false : true;
}, "Este valor não pode ser igual"); // Mensagem padrão 
 
 
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

$().ready(function() {
    $(document).ajaxStart( $.blockUI(
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
    }) );
    radialgradient(['body','#9EE9F5','#268FAC','550','MC']);
        
    $(this).bind("contextmenu", function(e) {
        e.preventDefault();
    });
    
    textboxes = $("input, select, textarea");

    if ($.browser.mozilla) {
        $(textboxes).keypress (checkForEnter);
    } else {
        $(textboxes).keydown (checkForEnter);
    }

    $.ajax({
        type: "POST",
        url: "../server-side/",
        data: "check=true",
        success: function(q){
            $.unblockUI();
            if (q=='0') {
                text  = "O serviço de autenticação encontrasse indisponível no momento.";
                text += "<br /><br />Entre em contato com o administrador ";
                text += "do sistema ou tente novamente mais tarde."
                msg = '<div id="default"><div class="center"><span>'+text+'</span></div></div>';
                $('#default').html(msg);
                $('#another').html('').hide();
            }
        }
    });
    $('input[type=password]').val('');
    $('input[name=senha_atual]').focus();
    
    $("#valida_troca").validate({
        rules: {
            senha_atual:  {
                required: true,
                minlength: 5
            },
            nova_senha:  {
                required: true,
                minlength: 5,
                notEqual: "#senha_atual"
            },          
            confirma_senha:  {
                required: true,
                minlength: 5,
                equalTo: "#nova_senha"
            }
            
        },
        messages: {
            senha_atual: {
                required: "Por favor, entre com a sua senha atual.",
                minlength: "Entre com a senha com no minímo 5 caracteres."
            },
            nova_senha: {
                required: "Por favor, entre com a sua nova senha.",
                minlength: "Entre com a sua nova senha com no minímo 5 caracteres.",
                notEqual: "A nova senha não pode ser igual a senha atual"
            },
            confirma_senha: {
                required: "Por favor, confirme a sua nova senha.",
                minlength: "Confirme a sua nova senha com no minímo 5 caracteres.",
                equalTo: "As senhas informadas devem ser iguais."              
            }
        },
        submitHandler:  function() {
            enc = $('input[name=nova_senha]').val()+','+$('input[name=senha_atual]').val();
            encryptedString = $keyEncrypt.biEncryptedString(enc);
            enc = base64_encode(encryptedString);
            $.ajax({
                type: "POST",
                url: "server-side/",
                data: "enc="+enc,
                success: function(q){
                    $.unblockUI();
                    parsed = $.parseJSON(q);
                    if (parsed[0]) {
                        if (parsed[1]!=null) {
                            window.location = parsed[1];
                        } else {
                            window.alert(parsed);
                        }
                    } else {
                        window.alert(parsed[1]);
                        $('input[type=password]').val('');
                    }
                }
            });
        }
    });
});