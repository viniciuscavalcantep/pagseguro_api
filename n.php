<?php
	require 'vendor/autoload.php'; //IMPORT MERCADO PAGO SDK
	include_once('../connection.php');
	include_once('config.php'); //GET CREDENTIALS
	MercadoPago\SDK::setAccessToken(TOKEN);
	$payment = MercadoPago\Payment::find_by_id($_GET["payment_id"]);
	echo $payment->status;
	echo $payment->external_reference;
	#var_dump($payment);
	switch($payment->status){
		case 'approved':
			$mysqli->query("UPDATE ORDER SET status = 'APPROVED' WHERE reference='".$payment->external_reference."'");
			//SEND EMAIL CONFIRMING PAYMENT TO THE USER
			break;
		case 'rejected':
			$mysqli->query("UPDATE pedido SET status = 'REJECTED' WHERE reference='".$payment->external_reference."'");
			//SEND EMAIL WITH PHPMAILER SUGGESTING ANOTHER GATEWAY PAYMENT WAY (Ex.: PagSeguro)
			break;
		case 'in_process':
			$mysqli->query("UPDATE pedido SET status = 'IN PROCESS' WHERE reference='".$payment->external_reference."'");
			break;
	}
?>