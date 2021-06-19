

<div id="page-wrapper">
	<div class='row'>
		<ol class="breadcrumb">
			
			<li class='active'><?=$this->lng['mapa_calendario']?></li>
			
		</ol>
		<form class='form filter' method='post'>
			<?php
				foreach($this->filters as $column=>$filter) {
				?>
				<div class="form-group col-md-4 col-sm-6">
					<?php
						if ($filter['meta']['filter-type'] == 'single') {
						?>
						
						<label for=<?=$column?>'><?=$filter['meta']['title']?></label>
						<select name='<?=$column?>' id='<?=$column?>' class='form-control' placeholder='<?=$column?>'>
							<option value=''></option>
							<?php
								foreach($filter['data'] as $id=>$nome) {
									$sel = (isset($this->filtersSet[$column]) && $this->filtersSet[$column] == $id) ? 'selected': '';
								?>
								<option value='<?=$id?>' <?=$sel?>><?=$nome?></option>
								<?php
									
								}
							?>
						</select>
						
						<?php
						}
					?>
				</div>
				<?php
				}
			?>
			<div class="form-group col-md-4 col-sm-6">
				<label>&nbsp;</label>
				<input type='submit' name='filter' class='btn btn-primary' value='<?=$this->lng['filtrar_cadastramento']?>'>
			</div>
		</form>
	
		<div class='clearfix'></div>
	<br><Br><br>
		<div id='calendar'></div>
		
		
		
	</div>

</div>

<?php
	//$final_js = 
?>
	
	