<div id="page-wrapper" >
	
<div class='container'>
	<div class='col-md-12 '>
		<?php
		if (isset($this->dados)) {
		
		?>
		<h1><?=$this->dados['nome']?></h1>
		
		<?php
		}
		
		
		?>
		
	</div>
	<?php
	include_once 'cadastrador_completar_widget.tpl';
	?>
</div>
</div>