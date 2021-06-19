<table>
	<tr>
		<th>Formul&aacute;rio</th>
		<?php
		if ($this->tipo <= 2) {
		?>
		<th><?=$this->lng['gerente_relatorio']?></th>
		<th>Email <?=$this->lng['gerente_relatorio']?></th>
		<?php
		}
		?>
		
		<?php
		if ($this->tipo <= 3) {
		?>
		<th><?=$this->lng['coordenador_relatorio']?></th>
		<th>Email<?=$this->lng['coordenador_relatorio']?></th>
		<?php
		}
		?>
		
		<th><?=$this->lng['cadastrador_cadastramento']?></th>
		<th>Email</th>
		<th><?=$this->lng['pessoas_cadastramento']?></th>
		<th>%<?=$this->lng['concluido_cadastramento']?></th>
		<th><?=$this->lng['pendentes_cadastramento']?></th>
		<th><?=$this->lng['identificador_relatorio']?></th>
		<th><?=$this->lng['data_relatorio']?></th>
		
		<?php
		for($x=1;$x<=$this->maximo;$x++) {
		?>
		<th>Pergunta <?=$x?></th>
		<th>Resposta</th>
		<?php
		}
		?>
	</tr>
	<?php
	
	foreach($this->perguntas as $p) {
	
	foreach($p['data'] as $c) {
	
	foreach($c['data'] as $r1) {
 	?>
	<tr>
		<td><?=$p['nome']?></td>
		<?php
		if ($this->tipo <= 2) {
		?>
		<td><?=$c['regional']?></td>
		<td><?=$c['email_regional']?></td>
		<?php
		}
		?>
		
		<?php
		if ($this->tipo <= 3) {
		?>
		<td><?=$c['coordenador']?></td>
		<td><?=$c['email_coordenador']?></td>
		<?php
		}
		?>
		<td><?=$c['nome']?></td>
		<td><?=$c['email']?></td>
		<td><?=$c['numero']?></td>
		<td><?=100-number_format(($c['numero']-$c['respondido'])/$c['numero'], 2)*100?>%</td>
		<td><?=$c['numero']-$c['respondido']?></td>
		
		<?php
		$h = 0;
		
		foreach($p['perguntas'] as $i=>$r) {
		$h++;
		?>
		
		<?php
		if ($h == 1) {
		?>
		<td><?=$r1[2]?></td>
		<td><?=$r1[1]?></td>
		<?php
		}
		?>
		<td><?=$r?></td>
		<td><?=(isset($r1[0][$i]) ? $r1[0][$i] : '')?></td>
		<?php
		}
		?>
	</tr>
	<?php
	}
	}
	}
	?>
</table>