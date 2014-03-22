<?php
require_once "Mail.php";
$body = $msgBodys.$msgBodym.$msgBodye;
$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject);

$smtp = Mail::factory('smtp',
		      array ('host' => $host,
			     'auth' => true,
			     'username' => $username,
			     'password' => $password));
$mail = $smtp->send($to, $headers, $body);
?>
  
