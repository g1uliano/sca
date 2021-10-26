/*********************************************************************/
/* Sistemas Controlador de Acesso
/* Controle de Login
/* login.screen.js - criado e mantido por: Giuliano Cardoso
/* última alteração: 01 de maio de 2011 às 12:11 hrs.
/********************************************************************/
var $keyEncrypt = new biRSAKeyPair("1c8b622ddf189407642fd37bf5e041f5", "0", "1d65d1033aa8c90edb2e44ff0b6e33f9");

function uniqueId() {    
    if ($.jsper.get('unique_id')==null) {        
        unId = md5(time());
        $.jsper.set('unique_id', unId);                        
    } else {
        unId = $.jsper.get('unique_id');
    }
    return unId;
}

function isValidEmail(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function DoLogin() {
    enc = time()+":"+$('input[name=usuario]').val()+":"+time()+":"+$('input[name=senha]').val()+":"+uniqueId();
    encryptedString = $keyEncrypt.biEncryptedString(enc);
    enc = base64_encode(encryptedString);
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
    });

    $.ajax({
        type: "POST",
        url: "server-side/",
        data: "enc="+enc,
        success: function(wsc){
            $.unblockUI();
            if (wsc==0) {
                $.growlUI('Acesso Negado', 'Tente outra vez.');
                $('input[name=usuario]').val('');
                $('input[name=senha]').val('');
                $('input[name=usuario]').focus();
            } else {
                if (wsc!='NULL') {
                    $('input[name=usuario]').val('');
                    $('input[name=senha]').val('');
                    window.location.href=wsc;
                } else {
                    window.alert('Erro de comunicação com o banco de dados.');
                }
            }
        }
    });
}
$().ready(function() {
    radialgradient(['body','#9EE9F5','#268FAC','550','MC']);
    //desabilita a tecla 'esc'.
    $(document).bind("keydown.cbox_close", function (e) {
        if (e.keyCode === 27) {
            e.preventDefault();
            cboxPublic.close();
        }
    });
    
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


    if (window.location.hash.substring(1)!='recover_password') {
        $('#another').hide();
        $('#default').show();
    } else {
        $('#default').hide();
        $('#another').show();
        $('input[name=email]').val('').focus();
    } 
    
    $(this).bind("contextmenu", function(e) {
        e.preventDefault();
    });

    $.ajax({
        type: "POST",
        url: "server-side/",
        data: "check=true",
        success: function(q){
            $.unblockUI();
            if (q=='0') {
                text  = "O serviço de autenticação encontra-se indisponível no momento.";
                text += "<br /><br />Entre em contato com o administrador ";
                text += "do sistema ou tente novamente mais tarde."
                msg = '<div id="default"><div class="center"><span>'+text+'</span></div></div>';
                $('#default').html(msg);
                $('#another').html('').hide();
            }
        }
    });
    
    $('input[name=usuario]').val('').focus();
    $('input[name=senha]').val('');
    $('input[name=usuario]').keypress(function(e) {
        if (e.keyCode==13) {
            if ($(this).val()!='') {
                $('input[name=senha]').val('').focus();
            }
        }
    });
    $('input[name=senha]').keypress(function(e) {
        if (e.keyCode==13) {
            if ($(this).val()!='') {
                DoLogin();
            }
        }
			 
        if (e.keyCode==8) {
            if ($(this).val()=='') {
                $('input[name=usuario]').focus();
            }
        }
    });
    $('input[name=logar]').click(function () {
        if (($('input[name=senha]').val()!='')
            && ($('input[name=usuario]').val()!=''))  {
            DoLogin();
        } else {
            if ($('input[name=usuario]').val()=='') {
                $('input[name=usuario]').focus();
            } else {
                $('input[name=senha]').focus();
            }
        }
    });
    
    $('a[href=#recover_password]').click(function() {
        $('#default').hide();
        $('#another').show();
        $('input[name=email]').val('').focus();
    });    
    $('a[href=#back]').click(function() {
        $('#default').show();
        $('#another').hide();
        $('input[name=usuario]').val('').focus();
        $('input[name=senha]').val('');
    });
    
    $('input[name=reset_password]').click(function(){
        if ($('input[name=email]').val()!='') {
            if (isValidEmail($('input[name=email]').val())) {
                $.ajax({
                    type: "POST",
                    url: "server-side/",
                    data: "email="+base64_encode($('input[name=email]').val()),
                    success: function(q){
                        $.unblockUI();
                        parsed = $.parseJSON(q);
                        window.alert(parsed[1]);
                        if (parsed[0]) {
                            $('#default').show();
                            $('#another').hide();
                            $('input[name=usuario]').focus();                       
                        } else {
                            $('input[name=email]').val('').focus();
                        }
                    }
                });
            } else {
                window.alert('O e-mail informado é inválido.');
                $('input[name=email]').val('').focus();
            }
        } else {
            window.alert('É necessário informar um e-mail para utilizar a recuperação de senha.');
            $('input[name=email]').focus();
        }
    });
});

