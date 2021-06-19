<?php
	if (!$this->modal) {

?>
<div id="page-wrapper">
<?php
}
?>
	<div class='row'>
		<div class="col-sm-12">
	
			<form  method='post' action='<?=$this->meta['controller']?>/add_action/<?=$this->args?>' enctype="multipart/form-data">
			<ol class="breadcrumb">
				<li><?=$this->lng['confirmacao_enviar']?></li>
			</ol>
			<?php
			include_once 'warnings.tpl';
			?>
			
			<p style="font-weight: bold; font-size: 1.3em;"><?=$this->lng['voce_enviar']?></p><br>

				<input style="display:none" type="text" name="fakeusernameremembered"/>
				<input style="display:none" type="password" name="fakepasswordremembered"/>
				<fieldset>
				<?php
					$break_tab = false;
					foreach($this->meta['fieldsToShow'] as $item) {
						if (in_array('break-tab', $item['info']['comment'])) {
							$break_tab = true;
						}
						Meta::gInput($item['info'], $this->args_arr, $this->args, '', false, 0, $this->tabs, $this->meta['permissions']);
					}
					
					
					if (count($this->tabs) && !$break_tab) {
					?>
					<span class="clearfix"></span>
					 
					 </div>

					</div>
					<?php
					
					}
				?>
			</fieldset>
			<div class="form-group clear col-md-12 margintop">
				<button type="submit" class="btn btn-primary save <?=$this->modal ? 'save-ajax' : ''?>"><?=$this->lng['sim_enviar']?></button>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button type="submit" onClick="JavaScript: window.history.back();" class="btn btn-primary save <?=$this->modal ? 'save-ajax' : ''?>"><?=$this->lng['nao_enviar']?></button>
			</div>
			</form>
		</div>  
	</div>
<?php
	if (!$this->modal) {
?>
</div>
<?php
	}
?>