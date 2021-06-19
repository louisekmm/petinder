<?php
			if (count($this->filters)) {
		?>
		<form class='form filter' method='post'>
		<input type='hidden' name='filter' value=1>
			<?php
				foreach($this->filters as $column=>$filter) {
			?>
				<div class="form-group col-md-<?=($filter['meta']['filter-type'] == 'date' ? '4' : '3')?> col-sm-6">
					<label for=<?=$column?>'><?=str_replace('_', ' ', $filter['meta']['title'])?></label>
				<?php
					if ($filter['meta']['filter-type'] == 'single') {
//print_r($filter['meta']['enum']);
				?>
					
					
					<select name='<?=$column?>' id='<?=$column?>' class='form-control <?=(isset($filter['class']) ? $filter['class'] : '')?>'>
						<option value=''></option>
						<?php
							if (isset($filter['meta']['enum']) && $filter['meta']['enum']) {
							
								$filter['data'] = $filter['meta']['enum'];
								if (reset($filter['data']) == '') {
									array_shift($filter['data']);
								}
							}
							//print_r($filter['data']);
							foreach($filter['data'] as $id=>$nome) {
							$sel = (isset($this->filtersSet[$column]) && $this->filtersSet[$column] === (string)$id) ? 'selected': '';
						?>
							<option value='<?=$id?>' <?=$sel?>><?=$nome?></option>
						<?php
						
							}
						?>
					</select>
				
				<?php
					}
					
					if ($filter['meta']['filter-type'] == 'text') {
					?>
					<input type="text" name='<?=$column?>' id='<?=$column?>' class='form-control <?=(isset($filter['class']) ? $filter['class'] : '')?>' value='<?=(isset($this->filtersSet[$column]) ? $this->filtersSet[$column] : '')?>'>
					<?php
					
					}
					
					if ($filter['meta']['filter-type'] == 'date') {
					?>
					<div class='col-md-6'>
						<input type="text" name='<?=$column?>[0]' id='<?=$column?>0' placeholder="De" class='form-control data' value='<?=(isset($this->filtersSet[$column][0]) ? $this->filtersSet[$column][0] : '')?>'>
					</div>
					<div class='col-md-6'>
						<input type="text" name='<?=$column?>[1]' id='<?=$column?>1' placeholder="Até" class='form-control data' value='<?=(isset($this->filtersSet[$column][1]) ? $this->filtersSet[$column][1] : '')?>'>
					</div>
					<?php
					}
				?>
				</div>
			<?php
				}
			?>
			<div class="form-group col-md-4 col-sm-6">
				<label>&nbsp;</label>
				<input type='submit' name='filter' class='btn btn-primary save' value='<?=$this->lng['filtrar_cadastramento']?>'>
			</div>
		</form>
		<?php
		
		}
		?>
		<span class='clearfix '></span>