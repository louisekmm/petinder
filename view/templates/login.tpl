 <div id="wrapper">
        <?php 
                global $di;
                if($this->exibir == "sim"){
                        
                            
        ?>
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
		<ul class="nav navbar-top-links navbar-right">              
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-globe"></i> Language  <i class="caret"> </i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">

                       <li><a href="admin/change/en">English</a></li>
                        <li><a href="admin/change/pt-br">Português</a></li>
                        <li><a href="admin/change/de">Deutsch</a></li>
                        <li><a href="admin/change/es">Español</a></li>
                        <li><a href="admin/change/fr">Français</a></li>
                    </ul>
                    <!-- /.dropdown-language -->
                </li>
                
            </ul>
            <?php } ?>

    <div class="container">
        <div class="row">
 
            <h1 class='page-header text-center'><img src="img/logo.png" style='max-width:400px;'></h1>
			
            <div class="col-md-6 col-md-offset-3">
				
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$this->lng['entrar_login']?></h3>
                    </div>
                    <div class="panel-body">
                        <form method='post' action='login/do/' data-redirect='<?=URL?>/'>
                            <fieldset>
								
                                <div class="form-group">
									<input class="form-control required " placeholder="Email" name="login" type="text" autofocus>
                                </div>
                                <div class="form-group">
									<input class="form-control required" placeholder="<?=$this->lng['senha_cadastro']?>" name="senha" type="password" value="">
                                </div>
                                <!--<div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="1">Permanecer Logado
                                    </label>
                                </div>-->
                                
                                <button type='submit' class="btn btn-lg btn-success btn-block btn-login" data-loading-text="<?=$this->lng['aguarde_esqueci']?>"><?=$this->lng['entrar_login']?></button>
                            </fieldset>
                        </form>
						
                    </div>
					
                </div>
				<a href="login/esqueci/" class='esqueci clear margintop marginbottom'><?=$this->lng['esqueci_esqueci']?></a>
				<a href="cadastro/" class='esqueci clear margintop marginbottom'><?=$this->lng['cadastre_login']?></a>
				<p class='margintop'><small><strong><?=$this->lng['ajuda_login']?></strong> <?=$this->lng['envie_login']?> <a href="mailto:contato@mgovbrasil.com.br">contato@mgovbrasil.com.br</a></small></p>
				<div class='warnings text-center paddingtop'>
				
				</div>
            </div>
			
        </div>
    </div>