<div id="page-wrapper">
	<div class='container'>
		<?php
			include_once 'warnings.tpl';
		?>
		<div class='col-md-12 margintop'>
			<form method='get'>
				<select name='formulario' class='input form-control' style='display:inline-block;width:300px;margin-right:30px;'>
					<option value=''><?=$this->lng['todos_index_cadastrador']?></option>
					<?php
					
					foreach($this->formularios as $obj) {
					$sel = ($obj['slug'] == $this->formulario ? 'selected' : '');
					?>
					<option value='<?=$obj['slug']?>' <?=$sel?>><?=$obj['nome']?></option>
					<?php
					}
					?>
				</select>
				<input type='submit' value='<?=$this->lng['filtrar_cadastramento']?>' class='btn btn-primary btn-sm'>
			</form>
		</div>

		<div class='col-md-12 margintop'>
			<table class='table'>
				<tr>
					<th><?=$this->lng['acoes_index_cadastrador']?></th>
					<th><?=$this->lng['formulario_cadastrador_perguntas']?></th>					
					
				</tr>
				<div class="list-group">
				<?php
				
				foreach($this->formularios as $obj) {
				?>
				<tr>
				<td width='150'>
						<?php
						if ($obj['pre']) {
						?>
						
						<?php
						
						} else {
						?>
						<a href="formulario/responder/<?=urlAmigavel($obj['nome'])?>/" class='btn btn-xs btn-primary'><?=$this->lng['responder_cadastrador_completar_widget']?></a>
						<?php
						if ($obj['respondido']) {
						?>
						<a href="formulario/responder/<?=urlAmigavel($obj['nome'])?>/perguntas/1/" class='btn btn-xs btn-primary'><?=$this->lng['editar_cadastrador_completar_widget']?></a>
						<?php
						}
						
						}
						?>
					</td>
					<td class='text-left'>
						
						<a href="formulario/responder/<?=urlAmigavel($obj['nome'])?>/" class='block'>
							
							<!--<span class=""><button class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-pencil'></i></button></span>-->
							<span class="badge"><?=$obj['respondido']?>/<?=$obj['numero']?></span> <?=$obj['nome']?> 
						</a>
					</td>
					
				</tr>
				
				<?php
				
				}
				?>

			</table>
		</div>
		<div class='col-md-12'>
			<p><a href="formulario/exportar/"><?=$this->lng['exportar_index_cadastrador']?></a></p>
			
		</div>
	</div>
</div>

