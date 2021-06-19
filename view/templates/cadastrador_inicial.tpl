<div id="page-wrapper" >
	<div class='container'>
	<div class='col-md-12 margintop'>
		<a href="formulario/exportar/"><?=$this->lng['clique_cadastrador_inicial']?></a>
		<div class='col-md-12 text-center'>
			<img src='content/img/<?=$this->dados['arquivo2']?>' class='img'>
		</div>
		<div class='col-md-12 text-left'>
			<img src='content/img/<?=$this->dados['arquivo1']?>' class='img'>
		</div>
		<div class='col-md-12 text-center'>
			<h1><a class="btn btn-danger btn-lg" href="formulario/responder/<?=$this->dados['slug']?>/
			<?php if($this->dados['pre']){?>
					<?=$this->lng['completar_cadastrador_inicial']?>
				<?php }else{?>
					<?=$this->lng['perguntas_cadastrador_inicial']?>
				<?php }?>
				/"><?=$this->lng['preencher_cadastrador_inicial']?></a></h1>
		</div>
	</div>
	</div>
</div>