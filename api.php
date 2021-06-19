<?php

		include_once "init.inc.php";
		
	//AVALIACAO	


$selbd = $di->getDb()->query('SHOW DATABASES');
$dados = $selbd->fetchAll();
//echo ($valor);
//foreach($dados as $d){
	$restricoes = "information_schema,mysql,performance_schema,sys";
	$restricao = explode(',', $restricoes);
	//$cont = 0;

	//if($d['Database'] != $restricao[$cont]){
		//$bd = $d['Database'];
		$bd = 'eduqmais-pve';
		$ds = 'mysql:host=intercommgov-db.mysql.database.azure.com;dbname='.$bd;
		$username='intercom@intercommgov-db';
		$password='mgovadm1!';
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',); 
		$dsn = new PDO($ds, $username, $password, $options);


		//echo ($d['Database']);
		

		$semana = $dsn->query('select s.*, d.nome as dimensao, d.texto_envio, date_format(inicio, "%d/%m/%Y") as inicio, date_format(
			termino, "%d/%m/%Y") as termino from semana s INNER JOIN dimensao d ON d.id=s.idDimensao WHERE termino=CURRENT_DATE order by termino DESC LIMIT 1')->fetch();
		
		$usuarios = $dsn->prepare('SELECT ep.nome as pessoa, ep.celular, epa.nota, ea.nome as agrupamento, e.nome as entidade FROM entidade_pessoa_avaliacao epa INNER JOIN entidade_pessoa ep ON ep.id=epa.idEntidade_pessoa INNER JOIN entidade e ON e.id=ep.idEntidade  INNER JOIN entidade_agrupamento ea ON ea.id=ep.idEntidade_agrupamento WHERE epa.idSemana=? AND epa.nao_matriculado=0 ORDER BY ep.nome ASC');

		$usuarios->Execute(array($semana['id']));
		$dados = $usuarios->fetchAll();
		
		$descs = array();
		$descricoes = $dsn->prepare('SELECT descricao, valor FROM dimensao_descricao WHERE idDimensao=?');
		
		$descricoes->Execute(array($semana['idDimensao']));
		
		$descric = $descricoes->fetchAll();
		foreach($descric as $d) {
			$descs[$d['valor']] = $d['descricao'];
		}
		//print_r($descs);
?>

<h1>API de envio de SMS - Debug</h1>

<h2>Envios da última semana cadastrada</h2>
<p>Semana: <?=$semana['nome']?><br>
Dimensao: <?=$semana['dimensao']?> <br>
Periodo:<?=$semana['inicio']?> a <?=$semana['termino']?></p>

<table border=1>
	<tr>
		<th>Escola</th>
		<th>Turma</th>	
		<th>Aluno</th>
		<th>Celular</th>
		<th>Nota</th>
		<th>Mensagem</th>
		<th>Data</th>
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
	
	foreach($dados as $obj) {
		//$t = array();
		
    	$primeiroNome = explode(" ", $obj['pessoa']);
   
		$obj['texto'] = str_replace('@name', $primeiroNome[0], $semana['texto_envio']);
		
		$nota = (isset($descs[$obj['nota']]) ? $descs[$obj['nota']] : '');
		$obj['texto'] = str_replace('@info_aluno', $nota, $obj['texto']);
		
		
		 	
		$date = new DateTime();
		//$interval2 = new DateInterval('P1D');//adiciona 1 dia
		$week = new DateInterval('P7D');//adiciona 1 semana
		
		/*$contacts['ddi'][] = "+55";
		$contacts['name'][] = $primeiroNome[0];
		$contacts['phone'][] = str_replace('(', '', str_replace(')', '', str_replace('-', '', str_replace(' ', '', $obj['celular']))));		
		$date->add($interval);
		$contacts[$conf['api_data_referencia']][] = $date->format('Y-m-d H:i:s');
		$contacts[$conf['api_mensagem']][] = $obj['texto'];
		*/

		$contacts['ddi'][] = "+55";
		$contacts['name'][] = $primeiroNome[0];
		$contacts['phone'][] = str_replace('(', '', str_replace(')', '', str_replace('-', '', str_replace(' ', '', $obj['celular']))));
		$date->add($week);
		$contacts[$conf['api_data_'.PROJECT_NAME]][] = $date->format('Y-m-d 10:00:00');
		//$contacts[$conf['api_data_referencia']][] = $date->format('Y-m-d 10:00:00');
		//chamava info_aluno
		$contacts[$conf['api_mensagem_'.PROJECT_NAME]][] = $obj['texto'];
		//$contacts[$conf['api_mensagem']][] = $obj['texto'];
		?>
		<tr>
		<td><?=$obj['entidade']?></td>
		<td><?=$obj['agrupamento']?></td>
		<td><?=$primeiroNome[0]?></td>
		<td><?=$obj['celular']?></td>
		<td><?=$obj['nota']?></td>
		<td><?=$obj['texto']?></td>
		<td><?=$date->format('Y-m-d H:i:s')?></td>
		<td><?=strlen($obj['texto'])?></td>
		</tr>
		<?php
		//$contacts[] = $t;
		//$contacts[] = $copy;
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
<?php
	
	//}
	//$this->connection->close();
	//$cont = $cont + 1;
//}	
?>		

