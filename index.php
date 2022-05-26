        <?php
        require 'vendor/autoload.php';
		$domain='';
		define('URL', 'https://ws.pagseguro.uol.com.br/v2/checkout');
function tirarAcentos($string){
			$acentos = array('À', 'Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì',
			'Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è'
			,'é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ', '');

			$sem_acentos = array('A','A','A','A','A','A','C','E','E','E','E','I','I','I',
			'I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e'
			,'e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y', '');
			return str_replace($acentos, $sem_acentos, utf8_encode($string));

		}

$price =0;#must be int	
$xml="
<checkout>
  <currency>BRL</currency>
  <sender>
    <name>".tirarAcentos($buyer['name'])."</name>
    <email>".$buyer['email']."</email>
    <phone>
      <areaCode>".$buyer['ddd']."</areaCode>
      <number>".$buyer['phone']."</number>
    </phone>
    <documents>
      <document>
        <type>CPF</type>
        <value>".$buyer['cpf']."</value>
      </document>
    </documents>
  </sender>
  <items>
    <item>
      <id>0001</id>
      <description> Product description </description>
      <amount>".(string)$price.".00</amount>
      <quantity>1</quantity>
      <weight>1</weight>
      <shippingCost>0.00</shippingCost>
    </item>
  </items>
  <redirectURL>".$domain."/n.php</redirectURL>
  <extraAmount>0.00</extraAmount>
  <reference>".$buyer['reference']."</reference>
  <shipping>
    <address>
      <street>...</street>
      <number>...</number>
      <complement></complement>
      <district>...</district>
      <city>...</city>
      <state>...</state>
      <country>...</country>
      <postalCode>...</postalCode>
    </address>
    <type>1</type>
    <cost>0.00</cost>
    <addressRequired>true</addressRequired>
  </shipping>
  <maxAge>999999</maxAge>
  <maxUses>2</maxUses>
  <receiver>
    <email>...</email>
  </receiver>
  <enableRecover>false</enableRecover>
</checkout>
";
$url = URL."?email=...&token=".TOKEN;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $xml,
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
#echo $xml;
$link_ps='https://pagseguro.uol.com.br/v2/checkout/payment.html?code='.$response['code'];
if($link_ps!='https://pagseguro.uol.com.br/v2/checkout/payment.html?code='){
	$mysqli->query("UPDATE order SET link_ps = '$link_ps' WHERE reference='$reference'");
}