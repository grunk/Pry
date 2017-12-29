<?php
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../vendor'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('PHPMailer/class.phpmailer.php');
require('../Pry/Pry.php');
Pry::register();

$mail             = new PHPMailer();

$body             = 'test';

//$mail->IsSMTP(); // telling the class to use SMTP
//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = false;                  // enable SMTP authentication
$mail->Host       = "172.16.12.4"; // sets the SMTP server
$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
$mail->Username   = "oroger@prynel.com"; // SMTP account username
$mail->Password   = "OlPryoR";        // SMTP account password

$mail->SetFrom('oroger@prynel.com', 'First Last');

$mail->AddReplyTo("oroger@prynel.com","First Last");

$mail->Subject    = "PHPMailer Test Subject via smtp, basic with authentication";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$address = "oroger@prynel.com";
$mail->AddAddress($address, "John Doe");

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}