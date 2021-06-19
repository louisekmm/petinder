<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">

			<li class='active'><?=$this->lng['NAMEPLURAL_entidade_agrupamento']?></li>
			
		</ol>
		<?php
			include_once 'warnings.tpl';
			
			//include_once 'filter_include.tpl';
		?>
		
		
		
		
		<?php
		if (count($this->results)) {
		?>

		
		<form method='post' action='entidade/avalia/'>
		
		<label><?=$this->lng['selecione_agrupamentos']?></label>
		<select name='semana' class="form-control ">
			<?php
			foreach($this->semanas as $obj) {
			?>
			<option value='<?=$obj['id']?>'><?=$obj['nome']?> - <?=$obj['dimensao']?></option>
			<?php
			}
			?>
		</select>
<br>
		<div class="table-responsive">
		<table class='table table-striped table-hover table-condensed table-order'>
			<thead>
				<tr>
					
					
	
					<th><?=$this->lng['professor_agrupamentos']?></th>
					<th><?=$this->lng['NAME_entidade_agrupamento']?></th>
					
					<th width=180></th>
				</tr>
			</thead>
		
			
			<tbody >
		<?php
			foreach($this->results as $result) {
			
		?>
			<tr>
			
			
					<td><?=$result['nome']?></td>
					<td><?=$result['agrupamento']?></td>
					
					<td>
						
						<button name='id' class='btn btn-primary btn-xs' data-toggle="tooltip" title="<?=$this->lng['avaliar_agrupamentos']?>" value='<?=$result['id']?>'><i class="glyphicon glyphicon-pencil"></i></button>
						
					</td>
					
				
					
		
				
				
			</tr>
		<?php
			}
		?>
		</table>
		</div>
		</form>
	</div>
	
	
	
	
	
	<?php
	} else {
	?>
		<strong>Nenhum resultado encontrado</strong>
	
	<?php

	}
	?>
	
</div>
