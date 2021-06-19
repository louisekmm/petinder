<div id="page-wrapper">
	<div class='row'>
		<div class="col-sm-12">
	
			<form method='post' action='peca/doupload/' enctype="multipart/form-data">
			<ol class="breadcrumb">
				
				<li><a href="/peca/upload/"><?=$this->lng['upload_upload']?></a></li>
				<li class='active'><?=$this->lng['adicionar_cadastro']?></li>
				
			</ol>
			<?php
			include_once 'warnings.tpl';
			?>
			<b>MAN</b><br><Br>

				<input style="display:none" type="text" name="fakeusernameremembered"/>
				<input style="display:none" type="password" name="fakepasswordremembered"/>
				<div class="form-group col-md-12"><label for="inp565e50199f92.63289037idMidia"><?=$this->lng['evento_upload']?></label>
				<select name='evento' id='inp565e50199f92.63289037idMidia' class='form-control required'>
					<option value=''></option>
					<?php
					
					foreach($this->eventos as $i=>$obj) {
					?>
					<option value='<?=$i?>'><?=$obj?></option>
					<?php
					}
					?>
				</select>
				
				</div>
				<div class="form-group col-md-6"><label for="inp565e50199f92c6.63289037idMidia"><?=$this->lng['zip_upload']?></label><input type="file" name="arquivo" id="inp565e50199f92c6.63289037idMidia" class="form-control  required"></div>
				<div class="form-group col-md-6"><label for="inp565e50199f92c6.633289037idMidia"><?=$this->lng['zip1_upload']?></label><input type="file" name="arquivo2" id="inp565e50199f92c6.633289037idMidia" class="form-control required"></div>
			
		
			<div class="form-group clear col-md-12">
				
				<button type="submit" class="btn btn-primary save"><?=$this->lng['enviar_cadastrador_perguntas']?></button>
			</div>
			
			</form>
		</div>  
	</div>
</div>