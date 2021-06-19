Formulário<?=($this->tipo <=2 ? ';Gerente Regional;E-mail Gerente Regional' : '')?><?=($this->tipo <=3 ? ';Coordenador;E-mail Coordenador' : '')?>;Cadastrador;E-mail;Pessoas;Concluído;Pendentes;Identificador;Data/Hora<?php for($x=1;$x<=$this->maximo;$x++) { echo ';Pergunta '.$x.';Resposta'; } echo "\r\n"; ?><?php
	
	foreach($this->perguntas as $p) {
	
	foreach($p['data'] as $c) {
	
	foreach($c['data'] as $r1) {
 	?><?=$p['nome']?><?=($this->tipo <=2 ? ';'. $c['regional'].';'. $c['email_regional'] : '')?><?=($this->tipo <=3 ? ';'. $c['coordenador'].';'. $c['email_coordenador'] : '')?>;<?=$c['nome']?>;<?=$c['email']?>;<?=$c['numero']?>;<?=100-number_format(($c['numero']-$c['respondido'])/$c['numero'], 2)*100?>%;<?=$c['numero']-$c['respondido']?><?php
		$h = 0;
		
		foreach($p['perguntas'] as $i=>$r) {
		$h++;
		
		if ($h == 1) {
		?>;<?=$r1[2]?>;<?=$r1[1]?><?php
		}
		?>;<?=$r?>;<?=(isset($r1[0][$i]) ? $r1[0][$i] : '')?><?php
		}
		echo "\r\n";
	}
	

	}
	}
	