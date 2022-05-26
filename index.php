        <?php
        # Before use you must install Composer (https://getcomposer.org/) and Mercado Pago's SDK (https://github.com/mercadopago/sdk-php) on the same folder of this files
        # After installing Composer, run on the command line composer require "mercadopago/dx-php:2.4.6" for PHP7 
        # or composer require "mercadopago/dx-php:1.12.5" for PHP5.6.

		require 'vendor/autoload.php'; //IMPORT MERCADO PAGO SDK
	    include_once('../connection.php');
	    include_once('config.php'); //GET CREDENTIALS
		
		MercadoPago\SDK::setAccessToken(TOKEN);

		$preference = new MercadoPago\Preference();

		// Cria um item na preferência
		$item = new MercadoPago\Item();
		$item ->id= '0001';
		$item->title = "Title of product";
		$item->quantity = 1;
		$item->unit_price = $valor; #must be float
		$preference->items = array($item); 
		$preference->back_urls = array( #set the url for updates about the order
			"success" => $domain.'/n.php', # page the user can is redirected id the payment is successful
			"failure" => $domain.'/n.php', # page the user can is redirected id the payment failed
			"pending" => $domain.'/n.php' # page the user can is redirected id the payment is pending
		);
		$preference->notification_url = $domain."/n.php";# page that will receive Mercado Pago's notification about the order status
		$preference->external_reference = $reference;
		
		$payer = new MercadoPago\Payer();
		function tirarAcentos($string){
			$acentos = array('À', 'Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì',
			'Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è'
			,'é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ', '');

			$sem_acentos = array('A','A','A','A','A','A','C','E','E','E','E','I','I','I',
			'I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e'
			,'e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y', '');
			return str_replace($acentos, $sem_acentos, utf8_encode($string));
		}
		$name=tirarAcentos($buyer['name']);
		$name = explode(" ", $name);
		$payer->name = $name[0];
		$payer->surname = $name[1];
		$payer->email = $buyer['email'];
		#$payer->date_created = "2018-06-02T12:58:41.425-04:00";
		$payer->phone = array(
			"area_code" => $buyer['ddd'],
			"number" => $buyer['phone']
		);
		$payer->identification = array(
			"type" => "CPF",
			"number" => $buyer['cpf']
		);
		$preference->payment_methods = array(
		  "excluded_payment_types" => array(
			array("id" => "ticket"),
			array("id" => "bank_transfer"),
			array("id" => "digital_wallet"),
			array("id" => "digital_currency")
		  ),
		  "installments" => 12
		);
		$preference->payer=$payer;	
		$preference->save();

		$link_mp = $preference->init_point; //Link to share or redirect the user
		$id_mp = $preference->id; //Order ID to save in DB
		$mysqli->query("UPDATE order SET id_mp = '$id_mp' WHERE reference='$reference'");

		?>