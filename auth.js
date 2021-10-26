//inclua este arquivo junto com o jquery e você adicionará o suporte a autenticação do sca em sua aplicação.
var sca_path = "http://www.logomedia.com.br/sca/";
var html_show=false;	
function checa_acesso() {
    $.ajax({
        type: "POST",
        url: sca_path+"server-side/",
        data: "check=session&u="+window.location.href,
        success: function(q){
            if ((q!='') && (typeof(q)!="undefined")) {
                if (q=='..') {
                    window.location.href=sca_path;
                } else {
                    if (!html_show) {
                        parsed = $.parseJSON(q);
                        html  =  "<div style=background:#fff;color:#000;position:absolute;top:0px;right:5px>";
                        html +=  "Olá, "+parsed+". <a href="+sca_path+"logout>Sair</a></div>";
                        $('body').append(html);
                        $('body').show();
                        html_show=true;
                    }
                }
            } 
        }
    });
    setTimeout('checa_acesso()', 60000); // a cada 60 segundos verifica a sessão.
}
$().ready(function() {
	
    $('body').hide();
	
    $(document).bind("keydown.cbox_close", function (e) {
        if (e.keyCode === 27) {
            e.preventDefault();
            cboxPublic.close();
        }
    });

    checa_acesso();
});
