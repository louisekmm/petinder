

<div id="page-wrapper">
<div class='container'>
	<div class='row'>
		<ol class="breadcrumb">
			
			<li class='active'><?=$this->lng['titulo_acompanhamento']?></li>
			<li class='right'><a href="#" class='print btn btn-xs btn-default' title="<?=$this->lng['imprimir_acompanhamento']?>"><i class='glyphicon glyphicon-print'></i></a></li>
		</ol>
		<?php
			include_once 'filter_include.tpl';
		?>
	
	
		<div class='col-md-12'>
		<?php
		if (isset($this->resultados)) {
		$valor = 0;
		?>
		
			<table class='table'>
				<tr>
					<th><?=$this->lng['NAME_entidade']?></th>
					<th><?=$this->lng['numero_acompanhamento']?> <?=$this->lng['NAMEPLURAL_entidade_agrupamento']?></th>
					<th><?=$this->lng['avaliadores_acompanhamento']?></th>
					<th><?=$this->lng['diretor_acompanhamento']?></th>
				</tr>
				
				
			
		<?php
		
		foreach($this->resultados as $obj) {
		//$valor += $obj['valor'];
		?>
		<tr>
			<td><a href="#" class='load-avaliadores' data-id='<?=$obj['id']?>'><?=$obj['nome']?></a><div class='results-avaliadores'></span></td>
			<td><?=$obj['agrupamentos']?></td>
			
			<td><?=$obj['avaliadores']?></td>
			<td><?=$obj['diretor']?></td>
			
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
</div>

<?php
	//$final_js = 
?>