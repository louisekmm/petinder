<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">

			<li class='active'><?=$this->lng['minhas_minhas']?> </li>
			
		</ol>
		<?php
			include_once 'warnings.tpl';
			
			//include_once 'filter_include.tpl';
		?>
		
		
		
		
		<?php
		if (count($this->results)) {
		?>

		
	
			<p><?=$this->lng['total_list']?> <strong><?=$this->totalResults?></strong></p>
	
		
		<div class="table-responsive">
		<table class='table table-striped table-hover table-condensed table-order'>
			<thead>
				<tr>
					
					
	
					<th><?=$this->lng['nome_cadastro']?></th>
					<th><?=$this->lng['codigo_cadastro']?></th>
					<th width=180></th>
				</tr>
			</thead>
		
			
			<tbody >
		<?php
			foreach($this->results as $result) {
			
		?>
			<tr>
			
			
					<td><?=$result['nome']?></td>
					<td><?=$result['codigo']?></td>
					<td>
						<?php
						if ($result['avaliacao']) {
						?>
						<a href='entidade/agrupamentos/<?=$result['id']?>/<?=$this->args?>' class='btn btn-primary btn-xs' data-toggle="tooltip" title="<?=$this->lng['avaliacao_avalia']?>"><i class="glyphicon glyphicon-pencil"></i></a>
						<?php
						}
						?>
						<?php
						
						if ($result['eventos'] && $this->tipo_usuario != 3) {
						?>
						<a href='entidade/eventos/<?=$result['id']?>/<?=$this->args?>' class='btn btn-primary btn-xs' data-toggle="tooltip" title="<?=$this->lng['eventos_eventos']?>"><i class="glyphicon glyphicon-calendar"></i></a>
						<?php
						}
						?>
					</td>
					
				
					
		
				
				
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
				echo '<li><a href="'.$this->meta['controller'].'/minhas/'.($this->page-1).'/">' ;?>
				<?=$this->lng['anterior_cadastro']?>
				<?php echo '</a></li>';
			}
		?>
	<?php
	for($i=1; $i <= $this->totalPages; $i++) {
		$active = ($i == $this->page) ? 'class="active"' : '';
		echo '<li '.$active.'><a href="'.$this->meta['controller'].'/minhas/'.$i.'/">'.$i.'</a></li>';
	}
	?>	
		<?php
			if ($this->page == $this->totalPages) {
				echo '<li class="disabled"><span>';?>
				<?=$this->lng['proxima_list']?>
				<?php echo '</span></li>';
			} else {
				echo '<li><a href="'.$this->meta['controller'].'/minhas/'.($this->page+1).'/">';?>
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
