<?php

		include_once "init.inc.php";

$eventos = $di->getDb()->query('select ee.*, date_format(ee.data, "%d/%m/%Y") as data, date_format(ee.hora, "%H:%i") as hora, ea.idEntidade_agrupamento from entidade_evento ee INNER JOIN entidade_evento_agrupamento ea ON ea.idEntidade_evento=ee.id WHERE DATE_SUB(data, INTERVAL 1 DAY)=CURRENT_DATE or DATE_SUB(data, INTERVAL 7 DAY)=CURRENT_DATE')->fetchAll();

//print_r($eventos);
//$selagrupamentos = $di->getDb()->query('select *, date_format(data, "%d/%m/%Y") as data, date_format(hora, "%H:%i") as hora from entidade_evento WHERE DATE_SUB(data, INTERVAL 1 DAY)=CURRENT_DATE')->fetchAll();;

$usuarios = $di->getDb()->prepare('SELECT  ep.nome as pessoa, ep.celular,  ea.nome as agrupamento, e.nome as entidade FROM entidade_pessoa ep INNER JOIN entidade e ON e.id=ep.idEntidade INNER JOIN entidade_agrupamento ea ON ea.id=ep.idEntidade_agrupamento  WHERE ea.id=? ORDER BY ep.nome ASC');

?>
<h2>Envio 1 dia e/ou 1 semana antes eventos</h2>

<table border=1>
	<tr>
		<th>Escola</th>
		<th>Turma</th>
		<th>Aluno</th>
		<th>Celular</th>
		<th>Mensagem</th>
		<th>Caracteres</th>
	</tr>
<?php
	
	$arr = array();
	$conf = $di->getConfig();
	$arr['group'] = $conf['api_idgrupo'];//alterar numero grupo api

	$contacts = array();
	$contacts['ddi'] = array();
	$contacts['name'] = array();
	$contacts['phone'] = array();
	$contacts[$conf['api_data_'.PROJECT_NAME]] = array();
	$contacts[$conf['api_mensagem_'.PROJECT_NAME]] = array();


foreach($eventos as $obj) {

	if($obj['sms']==1){
		$grupoenvio = $di->getDb()->query('select * from grupo_envio WHERE id=1')->fetch(PDO::FETCH_ASSOC);
	}
	else{
		$grupoenvio = $di->getDb()->query('select * from grupo_envio WHERE id=2')->fetch(PDO::FETCH_ASSOC);
	}

	//usuarios
	$usuarios->Execute(array($obj['idEntidade_agrupamento']));
	$dados = $usuarios->fetchAll();

	foreach($dados as $dado) {

		$primeiroNome = explode(" ", $dado['pessoa']);

		$dado['texto'] = $grupoenvio['texto'];
		
		$dado['texto'] = str_replace('@evento', $obj['nome'], $dado['texto']);
		$dado['texto'] = str_replace('@descricao', $obj['descricao'], $dado['texto']);
		$dado['texto'] = str_replace('@dia', $obj['data'], $dado['texto']);
		$dado['texto'] = str_replace('@hora', $obj['hora'], $dado['texto']);
		
		$contacts['ddi'][] = "+55";
		$contacts['name'][] = $primeiroNome[0];
		$contacts['phone'][] = str_replace('(', '', str_replace(')', '', str_replace('-', '', str_replace(' ', '', $dado['celular']))));
		$contacts[$conf['api_mensagem_'.PROJECT_NAME]][] = $dado['texto'];
		$date = new DateTime();
		
		if($obj['sms']==1){
			$interval = new DateInterval('P1D');//adiciona 1 dia
		}
		else{
			$interval = new DateInterval('P7D');//adiciona 1 semana
		}		
		
		$date->add($interval);
		$contacts[$conf['api_data_'.PROJECT_NAME]][] = $date->format('Y-m-d 10:00:00');
		


		?>
		<tr>
		<td><?=$dado['entidade']?></td>
		<td><?=$dado['agrupamento']?></td>
		<td><?=$dado['pessoa']?></td>
		<td><?=$dado['celular']?></td>

		<td><?=$dado['texto']?></td>
		<td><?=strlen($dado['texto'])?></td>
		</tr>
		<?php
	}
}


	$arr['contacts'] = $contacts;
	
	
	echo json_encode($arr);
	//echo '<br><br>';
	//die;

//configuracoes api
$token='$2y$10$SASZ8QKnjByQFdVKuCjQ6.NRulXbKhwSaU.KL3i4BbJNtwNeEibaq';
$url = "http://mgovbrain.com/api/v1/contacts";    
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
array("Content-type: application/json", "Mgov-Api-Token: $token", "Mgov-Api-Key: e2adbc16904dbea39b398aa4fcc4f22c"));
		
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arr));

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

//echo $status."<br><br>";
//print_r($json_response);

?>
</table>