<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">

			<li class='active'><?=$this->lng['avaliacao_avalia']?></li>
			
		</ol>
		<?php
			include_once 'warnings.tpl';
			
			//include_once 'filter_include.tpl';
		?>
		<h5 style='line-height:1.6'>
		<?=$this->lng['professor_agrupamentos']?> <strong><?=$this->turma['professor']?></strong><br>
		<?=$this->lng['NAME_entidade']?> <strong><?=$this->turma['entidade']?></strong><br>
		
		
		<?php
		if ($this->turma['nome']) {
		?>
		 - <?=$this->lng['NAMEPLURAL_entidade_agrupamento']?> <strong><?=$this->turma['nome']?></strong>
		<?php
		}
		?>
		</h5>
		<h3 class='tcenter'><?=$this->semana['dimensao']?> | <?=$this->lng['referente_avalia']?> <?=$this->semana['preenchimento']?></h3>
		
		<h3 class='tcenter'><?=$this->lng['plataforma_avalia']?> <?=$this->semana['inicio']?> <?=$this->lng['a_avalia']?> <?=$this->semana['termino']?></h3>
		<p class='tcenter'><?=$this->lng['apos_avalia']?></p>
		
		<?php
		if (count($this->results)) {
		?>

		
		<form method='post' action='entidade/salva_avaliacao/'>
		<input type='hidden' name='semana' value='<?=$this->semana['id']?>'>
		<input type='hidden' name='turma' value='<?=$this->turma['id']?>'>
		
<br>
		<div class="table-responsive">
		<table class='table table-striped table-hover table-condensed table-order'>
			<thead>
				<tr>
					
					
	
					<th><?=$this->lng['nome_avalia']?></th>
					<th><?=$this->lng['nao_avalia']?></th>
					<th><?=$this->semana['dimensao']?></th>
					
					
				</tr>
			</thead>
		<script>
		var titulos = [];
		
		<?php
		foreach($this->notas as $obj) {
		?>
		titulos.push("<?=$obj['descricao']?>");
		<?php
		}
		?>
		
		</script>
			
			<tbody >
		<?php
			foreach($this->results as $result) {
			
		?>
			<tr>
			
			
					<td><span  title='<?=$this->lng['ra_avalia']?>: <?=$result['ra']?>' data-toggle='tooltip'><?=$result['nome']?></span></td>
					<td><input type='checkbox'  onclick="document.getElementById('nota[<?=$result['idPessoa']?>]').disabled = true;" <?=($this->semana['expirado'] ? 'disabled read-only' : '')?> name='nao_matriculado[<?=$result['idPessoa']?>]' <?=($result['nao_matriculado'] ? 'checked' : '')?> value='1'></td>
					<td><input type="hidden" <?=($this->semana['expirado'] ? 'data-readonly' : '')?> class="rating1" name='nota[<?=$result['idPessoa']?>]' id='nota[<?=$result['idPessoa']?>]' value='<?=$result['nota']?>' data-filled="glyphicon glyphicon-star gi-2x" data-empty="glyphicon glyphicon-star-empty gi-2x"></td>
					

				
			</tr>
		<?php
			}
		?>
		</table>
		</div>
		<?php
		
		if (!$this->semana['expirado']) {
		?>

		<input type='submit' name='submit' class='btn' value='<?=$this->lng['salvar_add']?>'>
		<?php
		}
		?>
		</form>
	</div>
	
	
	
	
	
	<?php
	} else {
	?>
		<strong><?=$this->lng['nenhum_avalia']?></strong>
	
	<?php

	}
	?>
	
</div>
