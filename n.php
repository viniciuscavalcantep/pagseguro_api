<?php
	include_once('../connection.php');
	include_once('config.php'); //GET CREDENTIALS
	
	$code=addslashes($_POST['notificationCode']);
		$url=URL.$code.'?email=...&token='.TOKEN;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"Content-Type: application/xml; charset=ISO-8859-1"
		  ),
		));
		
		$response = json_decode(json_encode(simplexml_load_string(curl_exec($curl))), true);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  #var_dump($response);
		}
		
		$reference=$response['reference'];

		switch((int)$response['status']){
			#case 1: ...;
		}
?>