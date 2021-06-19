<div id="page-wrapper" class='nomenu' style="background-color: white;">
	<div class='row'>
	
	<?php
			include_once 'warnings.tpl';
			
			
		?>
		<h1 class='page-header text-center'><img src="img/intercom.png" style='max-width:400px;'></h1>
		<div class='container margintop steps-container'>
		

					<div class="col-md-4 col-sm-4 step active-step">
						<div class="steps">
							<span class="step-number"><?=$this->lng['dados_cadastro']?></span>
							<p><?=$this->lng['dados1_cadastro']?></p>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 step">
						<div class="steps">
							<span class="step-number"><?=$this->lng['NAME_entidade']?><?=$this->lng['s_cadastro']?></span>
							<p><?=$this->lng['NAME_entidade']?><?=$this->lng['s_cadastro']?><?=$this->lng['s1_cadastro']?></p>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 step">
						<div class="steps">
							<span class="step-number"><?=$this->lng['NAME_entidade_agrupamento']?><?=$this->lng['s2_cadastro']?></span>
							<p><?=$this->lng['NAME_entidade_agrupamento']?><?=$this->lng['s2_cadastro']?><?=$this->lng['s1_cadastro']?></p>
						</div>
					</div>


                             
			
			
			<div class='form1'>
				<div class='col-md-12'>
					<h3><?=$this->lng['informacoes_cadastro']?></h3>
					<p><?=$this->lng['para_cadastro']?></p>
				</div>
				<form method="post" action="cadastro/parte1/" enctype="multipart/form-data">
					
					<input style="display:none" type="text" name="fakeusernameremembered">
					<input style="display:none" type="password" name="fakepasswordremembered">
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['cpf_cadastro']?></label>
						<input  type="text" name="cpf" value="" maxlength="250" id="" class="required form-control cpf">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['nome_cadastro']?></label>
						<input  type="text" name="nome" value="" maxlength="250" id="" class="required form-control">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['data_cadastro']?></label>
						<input  type="text" name="nascimento" value="" maxlength="250" id="" class="required form-control data">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['funcao_cadastro']?></label>
						<?php
						$v = Meta::getForeignArr($this->di, 'admin_tipo', '');
						?>
						<select name='idAdmin_Tipo' class='required form-control'>
							<option value=''></option>
							<?php
								foreach($v as $id=>$val) {
								if ($id == 1) continue;
								?>
								<option value="<?=$id?>"><?=$val?></option>
								<?php
								}
							?>
						</select>
						
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['email_cadastro']?></label>
						<input  type="text" name="email" value="" maxlength="250" id="email" class="required form-control email">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['confirmar_cadastro']?></label>
						<input  type="text" name="confirmar_email" value="" maxlength="250" id="" class="required repeteEmail form-control email">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['celular_cadastro']?></label>
						<input  type="text" name="celular" value="" maxlength="250" id="celular" class="required form-control telefone">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['confirmar1_cadastro']?></label>
						<input  type="text" name="confirmar_celular" value="" maxlength="250" id="" class="required repeteCelular form-control telefone">
					</div>

					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['operadora_cadastro']?></label>
						<?php
						$vo = Meta::getForeignArr($this->di, 'celular_operadora', '');
						?>
						<select name='idCelular_Operadora' class='required form-control'>
							<option value=''></option>
							<?php
								foreach($vo as $ido=>$valo) {
								?>
								<option value="<?=$ido?>"><?=$valo?></option>
								<?php
								}
							?>
						</select>
						
					</div>
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['senha_cadastro']?></label>
						<input  type="password" name="senha" value="" maxlength="250" id="senha" class="required form-control">
					</div>
					
					<div class="form-group col-md-12">
						<label for=""><?=$this->lng['confirmar2_cadastro']?></label>
						<input  type="password" name="confirmar_senha" value="" maxlength="250" id="" class="required repeteSenha form-control">
						<!--<input  type="hidden" readonly="true name="idAdmin" value="0" maxlength="250" id="" class="required form-control idAdmin">-->
					</div>
				
					<div class="form-group clear col-md-12 margintop">
						<button type="submit" class="btn btn-primary cadastro-proximo1 "><?=$this->lng['proximo_cadastro']?></button>
					</div>
					
				</form>
			</div>
			
			
			<div class='form2'>
				<div class='col-md-12'>
					<h3><?=$this->lng['selecione_cadastro']?> <?=$this->lng['NAME_entidade']?><?=$this->lng['s_cadastro']?></h3>
				</div>
				<form method="post" action="cadastro/parte2/" data-add="cadastro/addescola/" enctype="multipart/form-data">
					
					<input style="display:none" type="text" name="fakeusernameremembered">
					<input style="display:none" type="password" name="fakepasswordremembered">
					
					<div class="form-group col-md-11">
						<label for=""><?=$this->lng['NAME_entidade']?></label>
						<?php
						$vo = Meta::getForeignArr($this->di, 'entidade', '');
						?>
						<select name='id' id="id" class='required form-control'>
							<option value=''></option>
							<?php
								foreach($vo as $ido=>$valo) {
								?>
								<option value="<?=$ido?>"><?=$valo?></option>
								<?php
								}
							?>
						</select>
						
					</div>


					<div class="form-group col-md-1">
						<label for="">&nbsp;</label>
						<button type="button" class="btn btn-primary adicionar-escola"><?=$this->lng['adicionar_cadastro']?></button>
					</div>
					
					<div class='col-md-12'>
						<button type="button" class="btn btn-primary limpar-tabela"><?=$this->lng['limpar_cadastro']?></button>
						<table class='table escolas table-striped'>
							<thead>
								<th><?=$this->lng['NAME_entidade']?></th>
								<th><?=$this->lng['codigo_cadastro']?></th>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div class="form-group clear col-md-12 margintop">
						<button type="submit" class="btn btn-primary cadastro-anterior2 "><?=$this->lng['anterior_cadastro']?></button>
						<button type="submit" class="btn btn-primary cadastro-proximo2 "><?=$this->lng['proximo_cadastro']?></button>
					</div>
					
				</form>
			</div>
			
			<div class='form3'>
				<div class='col-md-12'>
					<h3><?=$this->lng['clique_cadastro']?></h3>
					<p><?=$this->lng['concluir_cadastro']?></p>
				</div>
				<form>
					<div class='turmas-table'>
				
					</div>
				</form>
				<div class="form-group clear col-md-12 margintop">
						<button type="submit" class="btn btn-primary cadastro-anterior3 "><?=$this->lng['anterior_cadastro']?></button>
						<button type="submit" class="btn btn-success cadastro-finalizar"><?=$this->lng['concluir1_cadastro']?></button>
					</div>
			
			</div>
			
			<div class='form4'>
				<div class='col-md-12'>
					<h3><?=$this->lng['cadastro_cadastro']?></h3>
					<p><a href="<?=URL?>"><?=$this->lng['fazer_cadastro']?></a></p>
				</div>
			</div>
		</div>
	</div>
</div>