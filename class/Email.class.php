<?php

class Email {
		
	
	public static function send($di, $titulo, $destinatario = array(), $mensagem) {
		include_once("class.phpmailer.php");
		include_once("class.smtp.php");
		
		global $di
		$mail = new PHPMailer();
		$mail->IsMail();
		//$mail->IsSMTP();
		
		//$mail->Username = 'contato_site@casadoschefs.com.br'; // Usuário do servidor SMTP (endereço de email)
		//$mail->Password = 'chefs001'; // Senha do servidor SMTP (senha do email usado)
		//$mail->SMTPAuth   = false;                  // enable SMTP authentication
		//$mail->SMTPDebug = 1;
		//$mail->Host     = "relay-hosting.secureserver.net"; // SMTP server
		//$mail->Port       = 587;    
		//$mail->SMTPSecure = 'ssl'; 			
		
		$mail->SetFrom("contato@cadastromgov.com","MGOV");
		$mail->CharSet = "utf-8";
		$mail->IsHTML(true);
		foreach($destinatario as $email) {
			$mail->AddAddress($email['email'], $email['nome']);
		}
		
		$mail->Subject = $titulo;
		$palavra = Meta::getLangFile('para-email', $di);
		$mail->AltBody   = $palavra;
		$mail->MsgHTML($mensagem);
		

		if($mail->Send()) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
}