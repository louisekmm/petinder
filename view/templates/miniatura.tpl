
	<div class='row'>
		<div class="col-sm-12">
			
			
			
			
			
				<?php
				$x = 1 ;
				
					foreach($this->dimension as $i=>$dimension) {
					//echo $dimension[1];
					//echo $dimension[0];
					//echo $this->width_original.'<br>';
					//echo $this->width_original*($dimension[1]/$dimension[0]);
					
					
				?>
				<form method='post' action='fotoadmin/miniatura_save/' enctype="multipart/form-data">
				<input type='hidden' name='id' value='<?=$this->id?>'>
				
				<div class='mae'>
					<input type="hidden" name="x1" value="0" class="x1" />
					<input type="hidden" name="y1" value="0" class="y1" />
					<input type="hidden" name="x2" class='x2' value="<?=intval(FotoAdminController::getWidthReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>" class="x2" />
					<input type="hidden" name="y2" class='y2' value="<?=intval(FotoAdminController::getHeightReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>" class="y2" />
					<input type="hidden" name="w" value="<?=intval(FotoAdminController::getWidthReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>" class="w" />
					<input type="hidden" name="h" value="<?=intval(FotoAdminController::getHeightReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>" class="h" />
					<input type="hidden" name="w_thumb" value="<?=$dimension[0]?>" />
					<input type="hidden" name="h_thumb" value="<?=$dimension[1]?>"  />
					<input type="hidden" name="w_original" value="<?=$this->width_original?>"  />
					<input type="hidden" name="thumb" value="<?=$x?>"  />
					<input type="hidden" name="h_original" value="<?=$this->height_original?>"  />
					
					
					<table>
						<tr>
							<td><input type='submit' class="btn btn-primary" value="Alterar"></td>
						</tr>
						<tr>
							<td valign=top><B><?=$this->lng['imagem_miniatura']?> (<?=$this->width_original?>x<?=$this->height_original?>px):</b><br><img class="thumbnail" src='<?=$this->original?>' style='width:304px;height:auto;' data-width='<?=$dimension[0]?>'  data-height='<?=$dimension[1]?>'  data-width_original='<?=$this->width_original?>'  data-height_original='<?=$this->height_original?>' data-x2='<?=intval(FotoAdminController::getWidthReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>' data-y2='<?=intval(FotoAdminController::getHeightReal($this->width_original, $this->height_original, $dimension[0], $dimension[1]))?>'></td>
							<td valign=top><b><?=$this->lng['miniatura_atual']?> (<?=$dimension[0]?>x<?=$dimension[1]?>px):</b><br><img src='<?=FotoAdminController::getContentDirAdmin('img', $dimension[2])?>' width=300></td>
						</tr>
					</table>
				</div>
				</form>
				<?php
					$x++;
				}
				?>
			
			
		
		
		</div>  
	</div>
