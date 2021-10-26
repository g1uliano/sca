<?php
/* * ******************************************************************
  Sistemas Controlador de Acesso
  Controle Administrativo
  admin.php - criado e mantido por: Giuliano Cardoso
  última alteração: 16 de junho de 2011 às 09:20 hrs.
 * ***************************************************************** */

if (!defined('_INCLUDED')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
//se o usuário não estiver autenticado, manda de volta para o lugar de onde ele veio, afinal, ele não foi convidado pra festa, foi?
if (!((isset($_SESSION['sca_id'])) && (isset($_SESSION['sca_grupo'])))) {
    header("Location: ..");
}
?>
<!DOCTYPE html>
<html lang="en" manifest="admin.appcache">
    <head>
        <meta charset=utf-8 />
        <title> Administração </title>
        <link rel="stylesheet" href="../min/?g=admin_css" type="text/css" />
        <script src="../js/yepnope.1.0.1-min.js"></script>
        <script language="javascript">
            yepnope([
                { load: '../min/?g=admin_js' }
            ]);
        </script> 
    </head>
    <body>
        <div id="header">Administração <span id="loading">- <span id="simple_loaded">&nbsp;Trocando informações com o servidor...&nbsp;</span></span></div>

        <div id="menu">
            <ul>
                <li><a href="#" id="usuarios" >Usuários</a></li>
                <li><a href="#" id="grupos" >Grupos</a></li>
                <li><a href="#" id="dominios" >Domínios</a></li>
                <li><a href="#" id="eventos" >Eventos</a></li>
                <li><a href="#" id="sair" >Sair</a></li>
            </ul>
        </div>
        <div id="generic_confirm" title="Confirmação"></div>
        <div id="generic_purpose_dialog" title="Aviso"></div>
        <div id="dialog" title="Confirmação de Saída">
            Clicando em "OK" você estará confirmando a sua saída do sistema, 
            deseja realmente fazer isto? <br />
        </div>

        <div id="usuario_app" app="true">
            <h2>Controle de Usuários</h2>

            <div id="usuario_tabs">
                <ul>
                    <li><a href="#criar_usuario">Criar Usuário</a></li>
                    <li><a href="#editar_usuario">Editar Usuário</a></li>
                </ul>

                <div id="criar_usuario">
                    <div id="cr_error_msg" style="color:#FF0000;text-align: left; "></div>
                    <form name="fcriar_usuario" id="fcriar_usuario" method="post" action="" autocomplete="off" >
                        <p><label for="usuario">usuário</label><input name="cr_usuario" id="cr_usuario" type="text" value=""></p>
                        <p><label for="fullname">nome completo</label><input name="cr_fullname" id="cr_fullname" type="text" value=""></p>
                        <p><label for="email">e-mail</label><input name="cr_email" id="cr_email" type="text" value=""></p>
                        <p><label for="senha">senha</label><input name="cr_senha" id="cr_senha" type="password" value=""></p>
                        <p><label for="confirmar_senha">confirmar senha</label><input id="cr_vsenha" name="cr_confirmar_senha" type="password" value=""></p>
                        <p><label for="dominio">domínio</label>
                            <select name="cr_lst_dominio" id="cr_lst_dominio">
                            </select>
                        </p>

                        <p><label for="grupo">grupo</label>
                            <select name="usr_grupo" id="cr_grupo">
                            </select>
                        </p>
                        <p><label for="expired">senha expirada</label>
                            <select name="cr_expired">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </p>   
                        <p>
                            <input class="ui-button ui-widget ui-state-default ui-corner-all" name="criar_usuario" id="cr_crusr" type="submit" value="criar usuário" />
                            <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name="limpar_usuario" id="cr_lmpusr" value="limpar campos" /></p>

                    </form>

                </div>

                <div id="editar_usuario">
                    <div id="seleciona_usr">
                        <p><label for="seleciona_dominio">domínio</label>                    
                            <select name="dominio_id"></select>
                        </p>
                        <p>
                            <span>
                                <label for="seleciona_usuario">selecionar usuário</label>                    
                                <select name="usuario_id"></select>
                            </span>
                        </p>                            
                        <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name="usr_ok" value="confirmar">
                    </div>
                    <div id="usr_selecionado">
                        <div id="ed_error_msg" style="color:#FF0000;text-align: left; "></div>
                        <form name="feditar_usuario" id="feditar_usuario" method="post" action="" autocomplete="off">
                            <p><label for="usuario">usuário</label><input name="ed_usuario" id="ed_usuario" type="text" value=""></p>
                            <p><label for="fullname">nome completo</label><input name="ed_fullname" id="ed_fullname" type="text" value=""></p>
                            <p><label for="email">e-mail</label><input name="ed_email" id="ed_email" type="text" value=""></p>
                            <p><label for="senha">nova senha</label><input name="ed_senha" id="ed_senha" type="password" value=""></p>
                            <p><label for="confirmar_senha">conf. nova senha</label><input name="ed_confirmar_senha" id="ed_confirmar_senha" type="password" value=""></p>
                            <p><label for="dominio">domínio</label>
                                <select name="ed_usr_dominio" id="ed_usr_dominio">
                                </select>
                            </p>
                            <p><label for="grupo">grupo</label>
                                <select name="ed_usr_grupo" id="ed_usr_grupo">
                                </select>
                            </p>
                            <p><label for="expired">expirar senha</label>
                                <select name="ed_expired">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </p>
                            <p><label for="desabilitar">desabilitar usuário</label>
                                <select name="ed_banuser">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </p>

                            <p class="editar">
                                <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" name=ed_atualizar_usuario value="atualizar" />
                                <input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=ed_excluir_usuario value="excluir" />
                                <input type="button" name=cancelar_usuario class="ui-button ui-widget ui-state-default ui-corner-all" value="cancelar" />
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="grupo_app" app="true">
            <h2>Controle de Grupos</h2>
            <div id="grupo_tabs">
                <ul>
                    <li><a href="#criar_grupo">Criar Grupo</a></li>
                    <li><a href="#editar_grupo">Editar Grupo</a></li>
                </ul>
                <div id="criar_grupo">
                    <p><label for="dominio">domínio</label>
                        <select name="g_ed_lst_dominio" id="g_ed_lst_dominio">
                        </select>
                    </p>
                    <div id="cg_error_msg" style="color:#FF0000;text-align: left; "></div>
                    <form name="fcriar_grupo" id="fcriar_grupo" method="post" action="" autocomplete="off">
                        <p><label for="grupo">nome do grupo</label><input name="cr_grupo" id="cr_grupo" type="text" value=""></p>
                        <p class="admin"><input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name=cr_s_grupo value="criar grupo" /><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=cr_c_grupo value="limpar campo" /></p>
                    </form>
                </div>

                <div id="editar_grupo">
                    <div id="select_group">
                        <p><label for="dominio">domínio</label>
                            <select name="g2_ed_lst_dominio" id="g2_ed_lst_dominio">
                            </select>
                        </p>

                        <p><label for="grupo">selecionar grupo</label>
                            <select name="ed_lst_grupo" id="ed_lst_grupo">
                            </select>
                        </p>
                        <input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name="group_ok" value="confirmar">
                    </div>
                    <div id="loaded_grupo">
                        <p><label for="grupo">nome do grupo</label><input autocomplete="off" name="ed_n_grupo" id="ed_n_grupo" type="text" value=""></p>
                        <p><label for="grupo">desativar grupo</label>
                            <select name="desativar_grupo" id="ed_desativar_grupo">
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </p>
                        <p class="admin"><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=alt_grupo value="atualizar" /><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=del_grupo value="excluir" /><input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name=alt_c_grupo value="cancelar" /></p>
                    </div>
                </div>


            </div>

        </div>

        <div id="dominio_app" app="true">
            <h2>Controle de Domínio</h2>
            <div id="dominio_tabs">
                <ul>
                    <li><a href="#criar_dominio">Criar Domínio</a></li>
                    <li><a href="#editar_dominio">Editar Domínio</a></li>
                </ul>
                <div id="criar_dominio">
                    <div id="cd_error_msg" style="color:#FF0000;text-align: left; "></div>
                    <form name="fcriar_dominio" id="fcriar_dominio" method="post" action="" autocomplete="off">
                        <p><label for="dominio">nome do domínio</label><input name="cr_dominio" id="cr_dominio" type="text" value=""></p>
                        <p class="admin"><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=cr_s_dominio value="criar dominio" /><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=cr_c_dominio value="limpar campo" /></p>
                    </form>
                </div>

                <div id="editar_dominio">
                    <div id="select_dominio">
                        <p><label for="dominio">selecionar domínio</label>
                            <select name="ed_lst_dominio" id="ed_lst_dominio">
                            </select>                            
                        </p>
                        <input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name="dominio_ok" value="confirmar">
                    </div>
                    <div id="loaded_dominio">
                        <p><label for="dominio">nome do domínio</label><input name="ed_n_dominio" id="ed_n_dominio" autocomplete="off" type="text" value=""></p>
                        <p><label for="grupo">desativar domínio</label>
                            <select name="desativar_dominio" id="ed_desativar_dominio">
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </p>
                        <p class="admin"><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=alt_dominio value="atualizar" /><input class="ui-button ui-widget ui-state-default ui-corner-all" type="button" name=del_dominio value="excluir" /><input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name=alt_c_dominio value="cancelar" /></p>
                    </div>
                </div>

            </div>
        </div>

        <div id="evento_app" app="true">
            <h2>Eventos</h2>
            <div id="eventos_tabs">
                <ul>
                    <li><a href="#visualizar_eventos">Visualizar Eventos</a></li>
                </ul>
                <div id="visualizar_eventos">
                    Por favor aguarde, carregando eventos...
                </div>
            </div>

        </div>
    </div>
</body>
</html>


