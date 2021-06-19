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
				<li>
					<?php
					if ($this->meta['permissions']['add'] == 1) {
					?>
					<a href="<?=$this->meta['controller']?>/list/1/<?=$this->args?>">
					<?php
					}
					?>
						<?=$this->meta['title']?>
					<?php
					if ($this->meta['permissions']['add'] == 1) {
					?>
					</a>
					<?php
					}
					?>
				</li>
				<li class='active'>
				<?php if($this->meta['permissions']['add'] || $this->meta['permissions']['edit']){?>
					<?=$this->lng['edicao_add']?>
				<?php }else{?>
					<?=$this->lng['visualizacao_add']?>
				<?php }?></li>
				<?php
					if ($this->meta['permissions']['add'] || $this->meta['permissions']['edit']) {
				?>	
				<li class='pull-right'><button type="submit" class="btn btn-primary btn-xs save <?=$this->modal ? 'save-ajax' : ''?>"><?=$this->lng['salvar_add']?></button></li>
				<?php
				}
				?>
			</ol>
			<?php
			include_once 'warnings.tpl';
			?>
			
				<input style="display:none" type="text" name="fakeusernameremembered"/>
				<input style="display:none" type="password" name="fakepasswordremembered"/>
				<fieldset>
				<?php
					$break_tab = false;
					//print_r($this->meta['fieldsToShow']);
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
				<?php
				if ($this->meta['permissions']['add'] || $this->meta['permissions']['edit']) {
				?>
				<button type="submit" class="btn btn-primary save <?=$this->modal ? 'save-ajax' : ''?>"><?=$this->lng['salvar_add']?></button>
				<?php
				}
				?>
					
				<?php
					if (!$this->modal && $this->meta['permissions']['add'] == 1) {
				?>		
					<br></br><label class='pointer'><input type='checkbox' name='back' <?=($this->back ? 'checked' : '')?> value=1> <?=$this->lng['criar_add']?></label>
				<?php
					}
				?>
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