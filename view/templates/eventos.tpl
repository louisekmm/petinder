<select class='hidden' id='agrupamentos'>
	<?php
	foreach($this->turmas as $obj) {
	?>
	<option value='<?=$obj['id']?>'><?=$obj['nome']?></option>
	<?php
	}
	?>
</select>
<div id="page-wrapper" data-escola='<?=$this->escola?>' data-lista='<?=(URL)?>/entidade/lista_eventos/' data-add='<?=(URL)?>/entidade/adicionar_evento/' data-del='<?=(URL)?>/entidade/del_evento/'>
	<div class='row'>
		<ol class="breadcrumb">
			
			<li class='active'><?=$this->lng['eventos_eventos']?></li>
			
		</ol>
		
		<div class='clearfix'></div>
		<?php
			include_once 'warnings.tpl';
			
			
		?>
	
	<h3><?=$this->lng['clique_eventos']?></h3>
	<br><Br><br> 
		<div id='calendar'></div>
		
		
		
	</div>
	
	
	<div class="modal fade" tabindex="-1" role="dialog" id='modalevento' data-backdrop="static">
  <div class="modal-dialog" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
       
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-danger btn-excluir-evento"><?=$this->lng['excluir_eventos']?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lng['cancelar_eventos']?></button>
        <button type="button" class="btn btn-primary btn-salvar-evento"><?=$this->lng['salvar_add']?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>
