

<div id="page-wrapper">
<div class='container'>
	<div class='row'>
		<ol class="breadcrumb">
			
			<li class='active'><?=$this->lng['titulo_acompanhamento_preenchimento']?></li>
			<li class='right'><a href="#" class='print btn btn-xs btn-default' title="<?=$this->lng['imprimir_acompanhamento']?>"><i class='glyphicon glyphicon-print'></i></a></li>
		</ol>
		<?php
			include_once 'filter_include.tpl';
			$count = 0;
		?>
	
	
		<div class='col-md-12'>
		<?php
		if (isset($this->resultados)) {

		$valor = 0;
		?>
		
			<table class='table'>
				<tr>
					<th><?=$this->lng['NAME_entidade']?></th>
					<th><?=$this->lng['NAME_entidade_agrupamento']?></th>
					<th><?=$this->lng['avaliador_acompanhamento_preenchimento']?></th>
					
					<?php
					foreach($this->semanas as $s) {
					?>
					<th><?=$s['nome_curto']?></th>
					<?php
					}
					?>
				</tr>
				
				
			
		<?php
		
		foreach($this->resultados as $obj) {
		$count = $count +1;
		//$valor += $obj['valor'];
		?>
		<tr>
			<td><?=$obj['entidade']?></td>
			<td><?=$obj['agrupamento']?></td>
			<td><?=$obj['avaliador']?></td>
			
			<?php
			foreach($obj['semanas'] as $s) {
			?>
			<td><?=$s?></td>
			<?php
			}
			?>
			
			
		</tr>
		
		<?php
		}
		?>

		</table>
		<?php
		}
		?>
				
					
		</div>
		
	</div>
</div>

<?php
	if($count == 0) {
	?>
		<strong><?=$this->lng['nenhum_avalia']?></strong>
	
	<?php

	}
	?>


</div>

<?php
	//$final_js = 
?>