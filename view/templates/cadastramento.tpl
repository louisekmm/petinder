<div id="page-wrapper">
<div class='container'>
	<?php
		include_once 'warnings.tpl';
		$count = 0;
	?>
		<div class='col-md-12 margintop'>
		<form method='get'>
			<select name='formulario' class='input form-control' style='display:inline-block;width:300px;margin-right:30px;'>
				<option value=''>Todos</option>
				<?php
				
				foreach($this->formularios as $obj) {
				$sel = ($obj['slug'] == $this->formulario ? 'selected' : '');
				
				?>
				<option value='<?=$obj['slug']?>' <?=$sel?>><?=$obj['nome']?></option>
				<?php
				}
				?>
			</select>
			<input type='submit' value="<?=$this->lng['filtrar_cadastramento']?>" class='btn btn-primary btn-sm'>
		</form>
		</div>

		<div class='col-md-12 margintop'>
			<table class='table'>
				<tr>
					<th><?=$this->lng['formulario_cadastrador_perguntas']?></th>
					<th><?=$this->lng['cadastrador_cadastramento']?></th>
					<th><?=$this->lng['pessoas_cadastramento']?></th>
					<th>%<?=$this->lng['concluido_cadastramento']?></th>
					<th><?=$this->lng['pendentes_cadastramento']?></th>
					
					
				</tr>
				<?php
				foreach($this->perguntas as $p) {
				$count = $count + 1;
				foreach($p['data'] as $c) {
				
				?>
				<tr>
					<td><?=$p['nome']?></td>
					<td><?=$c['nome']?></td>
					<td><?=$c['numero']?></td>
					<td><?=100-number_format(($c['numero']-$c['respondido'])/$c['numero'], 2)*100?>%</td>
					<td><?=$c['numero']-$c['respondido']?></td>
					
				</tr>
				<?php
				
				}
				}
				?>
			</table>
		</div>

	<?php
	if($count != 0) {
	?>
		<div class='col-md-12'>
			<p><a href="formulario/exportar/<?=$this->slug?>/"><?=$this->lng['exportar_cadastrador_finalizou']?> XLS</a></p>
			<?php
			if ($this->userTipo <= 3) {
			?>
			<p><a href="formulario/exportar/<?=$this->slug?>/?csv=1"><?=$this->lng['exportar_cadastrador_finalizou']?> CSV</a></p>
			<?php
			}
			?>

		</div>
<?php

	}
	?>
	</div>

	<?php
	if($count == 0) {
	?>
		<strong><?=$this->lng['nenhum_avalia']?></strong>
	
	<?php

	}
	?>

</div>

