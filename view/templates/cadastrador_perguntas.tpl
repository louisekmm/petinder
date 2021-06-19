<div id="page-wrapper" >
<div class='container margintop'>
	<div class='col-md-12 '>
		<?php
		if (isset($this->dados)) {
		
		?>
		<h1><?=$this->dados['nome']?></h1>
		<p><small><?=$this->lng['formulario_cadastrador_perguntas']?> <?=(isset($this->indice) && $this->indice ? $this->indice : $this->dados['respondido']+1)?> <?=$this->lng['de_cadastrador_perguntas']?> <?=$this->dados['numero']?></small></p>
		<?php
		}
		
		
		?>
		
		<hr>
		
		<?php
		
		//if ($this->dados['pre'] || 1==1) {
		if (1==1 && !isset($this->preview)) {
		?>
		<a class="btn btn-warning" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
		  <?=$this->lng['formularios_cadastrador_perguntas']?>
		</a>
		
		<div class="collapse" id="collapseExample" style='margin-top:20px;margin-bottom:20px;'>
		  
			<?php
			include_once 'cadastrador_completar_widget.tpl';
			?>
		
		</div>
		<?php
		} else if (isset($this->indice) && $this->indice) {
		?>
		
				<?php
			include_once 'cadastrador_completar_widget.tpl';
			?>
		
		<div class='text-center'>
			<h2><?=$this->lng['edicao_add']?></h2>
			<ul class='pagination'>
				<?php
				for($x=1;$x<=$this->total;$x++) {
				?>
				<li class='<?=($x == $this->indice ? 'active' : '')?>'><a href="formulario/responder/<?=$this->dados['slug']?>/perguntas/<?=$x?>/"><?=$x?></a></li>
				<?php
				}
				?>
			</ul>
		</div>
		<?php
		}
		?>
			
		<span class='clear'></span>
		<form class='form' method='post' action='formulario/responder/<?=$this->dados['slug']?>/resposta/<?=(isset($this->indice) && $this->indice ? $this->indice.'/' : '')?>' style='margin-top:20px;display:block;'>
				
			<input type='hidden' name='indice' value='<?=$this->dados['respondido']?>'>
			<?php
			foreach($this->perguntas as $obj) {
			?>
			<div class="form-group">
				<label for="id<?=$obj['id']?>"><?=$obj['nome']?></label>
				<?php
				if ($obj['idTipo_Pergunta'] == 1) {
				?>
				<input type="text" name='id<?=$obj['id']?>' value='<?=(isset($obj['resposta']) ? htmlspecialchars($obj['resposta']) : '')?>' class="form-control <?=($obj['email'] ? 'email' : '')?> <?=($obj['obrigatorio'] ? 'required' : '')?> <?=($obj['numerico'] ? 'number' : '')?> <?=($obj['telefone'] ? 'telefone' : '')?>" id="id<?=$obj['id']?>" placeholder="">
				<?php
				} else {
				?>
				<?php
				for($x=1;$x<=10;$x++) {
					if ($obj['opcao'.$x]) {
					?>
					<label class='inline'><input type='radio' <?=(isset($obj['resposta']) && $obj['resposta'] == $obj['opcao'.$x] ? 'checked' : '')?> name='id<?=$obj['id']?>' value='<?=$obj['opcao'.$x]?>' class='<?=($obj['obrigatorio'] ? 'required' : '')?>'> <?=$obj['opcao'.$x]?></label>
					<?php
					}
				}
				
				if ($obj['outro']) {
				
				$valOutro = (isset($obj['resposta']) && $obj['resposta'] && strpos($obj['resposta'], 'Outro:') !== false ? str_replace('Outro: ', '', $obj['resposta']) : '');
				
				?>
				<label class='inline'><input type='radio' name='id<?=$obj['id']?>' value='-1' <?=($valOutro ? 'checked' : '')?> class='<?=($obj['obrigatorio'] ? 'required' : '')?>'> <?=$this->lng['outro_cadastrador_perguntas']?> <input type='text' value='<?=htmlspecialchars($valOutro)?>' name='outro<?=$obj['id']?>' class='form-control requiredOutro' style='display:inline-block;width:auto;'></label>
				<?php
				}
				?>
				<?php
				}
				?>
			 
			<?php
			}
			?>
			
			 </div>
			 <?php
			 if (!isset($this->preview)) {
			 ?>
			 <div class="form-group margintop">
				<input type='submit' class='btn btn-danger btn-lg save' value='<?=$this->lng['enviar_cadastrador_perguntas']?>'>
			 </div>
			<?php
			}
			?>
		</form>
	</div>
</div>
</div>