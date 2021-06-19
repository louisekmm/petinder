<div id="page-wrapper">
	<div class='row'>
		<div class="col-sm-12">
	
			<form method='post' action='admin/doimport/' enctype="multipart/form-data">
			<ol class="breadcrumb">
				
				<li><?=$this->lng['importar_import']?> <?=$this->lng['usuario_import_user']?></li>
				
				
			</ol>
			<?php
			include_once 'warnings.tpl';
			?>
			
			<a href="<?=URL?>/class/modelo_user.csv"><?=$this->lng['modelo_import']?> CSV</a><br><Br>
				<input style="display:none" type="text" name="fakeusernameremembered"/>
				<input style="display:none" type="password" name="fakepasswordremembered"/>
				<div class="form-group col-md-6"><label for="inp565e50199f92c6.63289037idMidia"><?=$this->lng['arquivo_import']?></label><input type="file" name="arquivo" id="inp565e50199f92c6.63289037idMidia" class="form-control required"></div>

			<div class="form-group clear col-md-12">
				
				<button type="submit" class="btn btn-primary save"><?=$this->lng['salvar_add']?></button>
			</div>
			
			</form>
		</div>  
	</div>
</div>