<?php

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://microsoft-translator-text.p.rapidapi.com/translate?to=de&api-version=3.0&profanityAction=NoAction&textType=plain",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => "[{\"Text\":\"I would really like to drive your car around the block a few times.\"}]",
	CURLOPT_HTTPHEADER => [
		"content-type: application/json",
		"x-rapidapi-host: microsoft-translator-text.p.rapidapi.com",
		"x-rapidapi-key: ae2a38ef5emsh16c861b6b4ea236p1803dejsnfe85bfcbb702"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	$blagueTraduite= $response;
	$blagueTraduiteBrut=json_decode($response,true);
	echo($blagueTraduiteBrut[0]["translations"][0]["text"]);
}