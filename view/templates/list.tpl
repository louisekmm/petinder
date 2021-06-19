<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">
			<?php
			if (isset($this->pailink)) {
			?>
			<li>
				<a href="<?=strtolower($this->pailink)?>/list/1/">
					<?=$this->pai['nome']?>
				</a>
			</li>
			<?php
			}
			?>
			<li class='active'><?=$this->meta['title']?> </li>
			<?php
			if ($this->meta['permissions']['add']) {
			?>
			<li ><a href="<?=$this->meta['controller']?>/add/0/<?=$this->args?>" class='btn btn-primary btn-xs ' data-toggle="tooltip" data-placement="top" title="<?=$this->lng['adicionar_cadastro']?>"><i class='glyphicon glyphicon-plus'></i></a></li>
			
			<?php
			if ($this->meta['import']) {
			?>
			<li ><a href="<?=$this->meta['controller']?>/import/<?=$this->args?>" class='btn btn-primary btn-xs ' data-toggle="tooltip" data-placement="top" title="<?=$this->lng['importacao_list']?>"><i class='glyphicon glyphicon-film'></i></a></li>
			
			
			
			<?php
			}
			}
			?>
			
			
		</ol>
		<?php
			include_once 'warnings.tpl';
			
			include_once 'filter_include.tpl';
		?>
		
		
		
		
		<?php
		if (count($this->results)) {
		?>
		
		<?php
			if ($this->meta['order'] == 'ordem') {
		?>
			<a href='<?=(URL.'/'.$this->meta['controller'].'/order/'.$this->args)?>' class='btn btn-warning disabled btn-order'><?=$this->lng['salvar_list']?></a>
		<?php
			}
		?>
		
	
			<p><?=$this->lng['total_list']?> <strong><?=$this->totalResults?></strong></p>
	
		
		<div class="table-responsive">
		<table class='table table-striped table-hover table-condensed table-order'>
			<thead>
				<tr>
					<?php
					//if ($this->meta['permissions']['edit'] || $this->meta['permissions']['del']) {
					?>
					<th width=180></th>
					<?php
					//}
					?>
					<?php
						foreach($this->columns as $column){
					?>
					<th><?=str_replace('_', ' ', $column['title'])?></th>
					<?php
						}
					?>
				</tr>
			</thead>
		
			
			<tbody class='<?=($this->meta['order'] == 'ordem' ? 'sort' : '')?>'>
		<?php
			foreach($this->results as $result) {
			
		?>
			<tr>
			
			
				<td align='center'>
				<?php
					if (isset($this->extraActions)) {
						foreach($this->extraActions as $obj) {
						?>
						<a href='<?=str_replace('%id', $result['id'], $obj['href'])?><?=$result['id']?>/' class='btn <?=$obj['btn-class']?> btn-xs <?=$obj['class']?>' data-toggle="tooltip" title="<?=$obj['name']?>" target="<?=(isset($obj['target']) ? $obj['target'] : '')?>">
							<i class='glyphicon glyphicon-<?=$obj['icon']?>'></i>
						</a>
						<?php
						}
					}
				?>
				<?php
				if ($this->meta['permissions']['edit'] || $this->meta['permissions']['del'] || $this->meta['permissions']['view']) {
				?>
				
					<?php
					if ($this->meta['permissions']['edit'] || $this->meta['permissions']['view']) {
					?>
					<?php
					if ($this->meta['order'] == 'ordem') {
					?>
					<a href="#" onClick="return false;" class="handle"><i class="glyphicon glyphicon-resize-vertical">&nbsp;</i></a>
					<input type='hidden' class='ordem' name='ordem[<?=$result['id']?>]' value='<?=$result['ordem']?>'>
					<?php
					}
					?>
					<a href='<?=$this->meta['controller']?>/add/<?=$result['id']?>/<?=$this->args?>' class='btn btn-primary btn-xs' data-toggle="tooltip" title="<?=($this->meta['permissions']['add'] ? $this->lng['editar_cadastrador_completar_widget'] : $this->lng['ver_list'])?>">
						<?php
						if ($this->meta['permissions']['add'] || $this->meta['permissions']['edit']) {
						?>
						<i class='glyphicon glyphicon-pencil'></i>
						<?php
						} else {
						?>
						<i class='glyphicon glyphicon-eye-open'></i>
						<?php
						}
						?>
					</a>
						
						
					<?php
					}
					?>
					
					<?php
					if ($this->meta['permissions']['del']){
					?>
						<a href='<?=$this->meta['controller']?>/del/<?=$result['id']?>/' class='btn btn-danger btn-xs remove-item' data-toggle="tooltip" title="<?=$this->lng['remover_list']?>"><i class='glyphicon glyphicon-remove'></i></a>
					<?php
					}
					?>
				
				<?php
				}
				?>
				</td>
				<?php
					foreach($this->columns as $column) {
					
					if ($column['midia']){
						$string_thumb = '';
						for($x=1;$x>0;$x++) {
							if (isset($result[$column['name'].'_dimension'.$x])) {
								//$dimension = explode(',', $result[$column['name'].'_dimension'.$x]);
								//$string_thumb .= $dimension[0].'/'.$dimension[1].'/';
								$string_thumb .= $result[$column['name'].'_dimension'.$x].'/';
							} else {
								break;
							}
						}
						
				?>
					<td><div style='text-align:right;background:url("<?=($result[$column['name']] ? FotoAdminController::getContentDirAdmin($result[$column['name'].'_type'], $result[$column['name']]) : '')?>");width:100px;height:50px;'><?=($result[$column['name']] ? "<a href='fotoadmin/miniatura/".$result[$column['name'].'_id'].'/'.$string_thumb."' target='_blank' class='btn btn-warning active btn-xs'><i class='glyphicon glyphicon-pencil'></i></a>" : '')?></td>
				<?php
					} else {
						if ($column['reference']) {
							$value = '<a href="'.URL.'/'.$column['reference'].'/list/1/'.$result['id'].'/" class="btn btn-default"><i class="glyphicon glyphicon-th-list"></i></a>';
						} else {
							//print_r($column);

							$value = $result[$column['name']];
							$inline = false;
							if (isset($column['meta']) && count($column['meta']) > 1) {
								$inline = true;
								$column['meta']['valor'] = $value;
								$column['meta']['nodesign'] = true;
							}
							if (DefaultController::isCheckBox($column['options'])) {
								$value = ($value ? 'Sim/Yes' : 'Não/No');
							}
							if (in_array('color', $column['options'])) {
								$value = '<span class="littlebox" style="background-color:'.$value.'"></span>';
							}
						}
				?>
				<td class='<?=($inline ? 'inline-edit' : '')?>'>
					
						<span class="value">
							<?=$value?>
						</span>
						<?php
						if ($inline) {
						?>
						<span class='form'>
							<form>
							<input type='hidden' name='id' value='<?=$result['id']?>'>
							<input type='hidden' name='controller' class='controller' value='<?=$this->meta['controller']?>'>
							<input type='hidden' name='columnName' class='columnName' value='<?=$column['name']?>'>
							<div class='input'>
								<?=Meta::gInput($column['meta'], '', '', '', '', '', '', $this->meta['permissions']);?>
							</div>
							<input type='button' class='btn btn-xs btn-save-inline btn-danger' value='<?=$this->lng['salvar_add']?>'>
							</form>
						</span>
						
						<span class='pencil btn btn-xs btn-success'><i class="glyphicon glyphicon-pencil"></i></span>
						<?php
						}
						?>
					
				</td>
				<?php
					}
				?>
				
				<?php
				}
				?>
				
				
			</tr>
		<?php
			}
		?>
		</table>
		</div>
	</div>
	
	
	
	<div class='text-center'>
	  <ul class="pagination">
		<?php
			if ($this->page == 1) {
				echo '<li class="disabled"><span>';?>
				<?=$this->lng['anterior_cadastro']?>
				<?php echo '</span></li>';
			} else {
				echo '<li><a href="'.$this->meta['controller'].'/list/'.($this->page-1).'/">';?>
				<?=$this->lng['anterior_cadastro']?>
				<?php echo '</a></li>';
			}
		?>
	<?php
	for($i=1; $i <= $this->totalPages; $i++) {
		$active = ($i == $this->page) ? 'class="active"' : '';
		echo '<li '.$active.'><a href="'.$this->meta['controller'].'/list/'.$i.'/">'.$i.'</a></li>';
	}
	?>	
		<?php
			if ($this->page == $this->totalPages) {
				echo '<li class="disabled"><span>';?>
				<?=$this->lng['proxima_list']?>
				<?php echo '</span></li>';
			} else {
				echo '<li><a href="'.$this->meta['controller'].'/list/'.($this->page+1).'/">';?>
				<?=$this->lng['proxima_list']?>
				<?php echo '</a></li>';
			}
		?>
	  </ul>
	</div>
	
	<?php
	} else {
	?>
		<strong><?=$this->lng['nenhum_avalia']?></strong>
	
	<?php

	}
	?>
	
</div>