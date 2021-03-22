<?php
/*
$blagues=file_get_contents("./dataBaseBlague.json");
file_put_contents("toto.txt", file_get_contents("https://v2.jokeapi.dev/joke/Any?amount=3"));
var_dump(json_decode(file_get_contents("dataBaseBlague.json",true)));
var_dump(json_decode(file_get_contents("dataBase.json",true)));
*/

$type=$_GET['type'];
$langue=$_GET['langue'];

$note1=1;
$note2=2;

if ($type == "bien") {
	$note1=5;
	$note2=4;
}


$jsonNote=file_get_contents("./dataBase.json");
$mapNote=json_decode($jsonNote,true);
$json=file_get_contents("./dataBaseBlague.json");
$mapBlague=json_decode($json,true);
$blagueASave;
$nbrblaguemax=0;
$blague;
for ($i=0; $i < sizeof($mapNote["notjokes"]) ; $i++) { 
	
	if ($mapNote["notjokes"][$i]["note"]==$note1 & $nbrblaguemax==0) {
		$blagueASave=$mapNote["notjokes"][$i]["id"];
		$nbrblaguemax++;
	}
	if ($mapNote["notjokes"][$i]["note"]==$note2 & $nbrblaguemax==0) {
		$blagueASave=$mapNote["notjokes"][$i]["id"];
		$nbrblaguemax++;
	}
}
if ($nbrblaguemax!=0) {

	for ($y=0; $y<sizeof($mapBlague["jokes"]) ; $y++) { 

		if ($mapBlague["jokes"][$y]["id"]==$blagueASave) {
			$blague=$mapBlague["jokes"][$y];
		}
	}

}
else {
	$blague= "pas de blague dans cette categorie pour le moment";
}



if ($langue!="en") {

	if ($blague["type"]=="single") {
		
	$blagueAnglais=$blague["joke"];
	$urlAPI="https://microsoft-translator-text.p.rapidapi.com/translate?to=".$langue."&api-version=3.0&profanityAction=NoAction&textType=plain";
	$texteAEnvoyer = "[{\"Text\":\"".$blagueAnglais."\"}]";
	
$curl = curl_init();
curl_setopt_array($curl, [
	CURLOPT_URL => $urlAPI,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS =>"[{\"Text\":\"I would really like to drive your car around the block a few times.\"}]",
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
	$blague["joke"]=json_decode($response,true)[0]["translations"][0]["text"];
}

echo json_encode($blague,true); 
}else
{
	$blagueTraduite= "déjà en anglais...";
}
}
?>