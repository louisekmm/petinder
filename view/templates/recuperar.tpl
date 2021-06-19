 <div class="container">
        <div class="row">
			<h1 class='page-header text-center'><?=$this->lng['trocar_recuperar']?></h1>
			
            <div class="col-md-4 col-md-offset-4">
				
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$this->lng['trocar1_recuperar']?></h3>
                    </div>
                    <div class="panel-body">
                        <form method='post' action='login/dorecuperar/' data-redirect='<?=URL?>/'>
							<input type='hidden' name='token' value='<?=$this->token?>'>
                            <fieldset>
                                
                                <div class="form-group">
									<input class="form-control required" placeholder="<?=$this->lng['nova_recuperar']?>" name="senha" type="password" value="">
                                </div>
                                <!--<div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="1">Permanecer Logado
                                    </label>
                                </div>-->
                                
                                <button type='submit' class="btn btn-lg btn-success btn-block btn-recuperar" data-loading-text="<?=$this->lng['aguarde_esqueci']?>"><?=$this->lng['alterar_recuperar']?></button>
                            </fieldset>
                        </form>
                    </div>
                </div>
				<div class='warnings text-center'>
				
				</div>
            </div>
		
        </div>
    </div>