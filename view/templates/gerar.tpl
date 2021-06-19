<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">
			
			<li class='active'>Generate/Gerar</li>
			
		</ol>
		<?php
			include_once 'warnings.tpl';
		?>
		
		<form class='form ' method='post' action='boleto/gerarBoletos/'>
			<div class="form-group col-md-3 col-sm-6">
			<label>Mês</label>
			
			<select name='mes' class='required form-control'>
				<option value=''></option>
				<?php
				foreach($this->meses as $i=>$obj) {
				?>
				<option value='<?=$i?>'><?=$obj?></option>
				<?php
				}
				?>
			</select>
			</div>
			<div class="form-group col-md-3 col-sm-6">
			<label>Ano</label>
			<select name='ano' class='required form-control'>
				<option value=''></option>
				<?php
				$anos = array((date("Y")-1)=>date("Y")-1, date("Y")=>date("Y"), (date("Y")+1)=>date("Y")+1);
				foreach($anos as $i=>$obj) {
				$sel = ($obj == date("Y") ? 'selected' : '');
				?>
				<option <?=$sel?> value='<?=$i?>'><?=$obj?></option>
				<?php
				}
				?>
			</select>
			</div>
			<div class="form-group col-md-12 col-sm-12">
				
				<input type='submit' name='enviar' value='Generate/Gerar' class='btn btn-danger confirm save'>
			</div>
		</form>
		
	</div>
</div>