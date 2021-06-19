<?php
	include_once "init.inc.php";
	global $di;

if(isset($_GET['banco']) && isset($_GET['query'])){

		$logado = LoginController::isLogged($di);
		$vetor = array();
		$bd = $_GET['banco'];
		$query = $_GET['query'];
		$ds = 'mysql:host=intercommgov-db.mysql.database.azure.com;dbname='.$bd;
		$username='intercom@intercommgov-db';
		$password='mgovadm1!';

		$con = new PDO($ds, $username, $password, $options);
		
		$dados = $con->query('select email, celular, cpf, nome from admin WHERE email='.$query." OR celular = ".$query." OR cpf='".$query)->fetch();

		$email = preg_replace('/(?:^|.@).\K|.\.[^@]*$(*SKIP)(*F)|.(?=.*?\.)/', '*', $dados['email']);
		$celular = preg_replace('/^([0-9]{4})([0-9]{5})([0-9]{2})$/', '$1*****$3', $dados['celular']);
		$cpf = preg_replace('/^([0-9]{3})\.([0-9]{3})\.([0-9]{3})-([0-9]{2})$/', '$1.***.***-$4', $dados['cpf']);

		$vetor[] = "<br>" . json_encode(array(
			            'Nome' => utf8_encode($dados['nome']),
			            'Cpf' => ($cpf),
			            'Email' => ($email),
			            'Celular' => ($celular),
			          ), JSON_UNESCAPED_UNICODE) . "<br>";
		$this->connection->close();
}
else{
	$vetor[0] = "error";
}

return $vetor;
?>
