<?php
	//declaration des variables
$blagueAnglais="pas de blague trouvé";
$blagueTraduite="pas traduite";
	// vérification si les variables sont reçus
if(isset($_POST['langue']) && isset($_POST['categorie']) && (isset($_POST['case1']) || isset($_POST['case2'])) && isset($_POST['motCible']))
{
	//recupereration des données du formulaire
		$langue=$_POST['langue']; //string
		$categorie=$_POST['categorie'];//string
		$motContenuDansLaBlage=$_POST['motCible'];//string
		$url='https://v2.jokeapi.dev/joke/';
		$url=$url.$categorie.'?blacklistFlags=nsfw,religious,political,racist,sexist,explicit';
		$type;

		if (isset($_POST['case1'])&&isset($_POST['case2'])) {
			$uneParti=$_POST['case1'];
			$deuxParti=$_POST['case2'];
			
		}
		else if (isset($_POST['case1'])) {
			$uneParti=$_POST['case1'];
			$deuxParti="pas activer";
			
		}else{
			$uneParti="pas activer";
			$deuxParti=$_POST['case2'];

		}

		if ($uneParti == "pas activer" && $deuxParti == "activer")
		{
			$url=$url.'&type=twopart';
			$type="twopart";
		}
		elseif ($uneParti == "activer" && $deuxParti == "pas activer" )
		{
			$url=$url.'&type=single';
			$type="single";

		}
		if ($motContenuDansLaBlage != "")
		{
			$url=$url.'&contains='.$motContenuDansLaBlage;
		}

	//recuper les données Json
		$jsonBrut= file_get_contents($url);
		$json = json_decode($jsonBrut, true);

	//recuper la Blague
	if ($json["type"] == "twopart") //on regarde si c est une blague est en deux partie
	{
		$blagueAnglais=$json["setup"]."</br>".$json["delivery"];
	}
	else //blague en 1 partie
	{
		$blagueAnglais=$json["joke"];
	}

	//blague traduite
	if ($langue!="Anglais") {
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
			CURLOPT_POSTFIELDS => $texteAEnvoyer,
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
			$blagueTraduiteBrut=json_decode($response,true);
			$blagueTraduite=$blagueTraduiteBrut[0]["translations"][0]["text"];
		}

	}else
	{
		$blagueTraduite= "déjà en anglais...";
	}

	//ajouter a la base de données
	$dbBlague=file_get_contents("./dataBaseBlague.json");
	$mapBlague=json_decode($dbBlague,true);
	//verification si la blague n'est pas implementer
	$nbrBlagueSimilaire=0;
	$tailleBDBlague=sizeof($mapBlague["jokes"]);
	for ($y=0; $y<$tailleBDBlague ; $y++) { 

		if ($mapBlague["jokes"][$y]["id"]==$json["id"]) {
			$nbrBlagueSimilaire++;

		}
	}
	//si elle n'existe pas dans notre base de données
	if ($nbrBlagueSimilaire==0 && $type=="single") {
		$mapBlague["jokes"][$tailleBDBlague]["category"]=$json["category"];
		$mapBlague["jokes"][$tailleBDBlague]["type"]=$json["type"];
		$mapBlague["jokes"][$tailleBDBlague]["id"]=$json["id"];
		$mapBlague["jokes"][$tailleBDBlague]["joke"]=$json["joke"];
		$mapBlagueFinis=json_encode($mapBlague,true);
		file_put_contents("dataBaseBlague.json", $mapBlagueFinis);
	}else{
		$mapBlague["jokes"][$tailleBDBlague]["category"]=$json["category"];
		$mapBlague["jokes"][$tailleBDBlague]["type"]=$json["type"];
		$mapBlague["jokes"][$tailleBDBlague]["id"]=$json["id"];
		$mapBlague["jokes"][$tailleBDBlague]["setup"]=$json["setup"];
		$mapBlague["jokes"][$tailleBDBlague]["delivery"]=$json["delivery"];
		$mapBlagueFinis=json_encode($mapBlague,true);
		file_put_contents("dataBaseBlague.json", $mapBlagueFinis);
	};
	if ($nbrBlagueSimilaire==0){
		$dbNote=file_get_contents("./dataBase.json");
		$mapNote=json_decode($dbNote,true);
		$tailleBDNote=sizeof($mapNote["notjokes"]);
		$mapNote["notjokes"][$tailleBDNote]["id"]=$json["id"];
		$mapNote["notjokes"][$tailleBDNote]["note"]=0;
		$mapNote["notjokes"][$tailleBDNote]["nbrVote"]=0;
		$mapNoteFinis=json_encode($mapNote,true);
		file_put_contents("dataBase.json", $mapNoteFinis);
	};
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="charteGraphique.css" />
	<script>

		function NoterBlague(nbr){
			var idBlague = <?php echo ($json["id"]);?>;
			var url = "notationEtoile.php?nbrEtoile="+nbr+"&idBlague="+idBlague;
			
			
			window.location.href = url;
		} 
	</script>
	<noscript>
		JS pas activé
	</noscript>
</head>


<header>
	<div class="enteteG" id="titre">Laugh</div>
	<div class="text-alignC">
		<form method='POST' action='acceuil.html'><!-- Bouton vers la pageprincipale du site -->
			<div class="enteteC bold">
				<img src='http://iparla.iutbayonne.univ-pau.fr/~rcoussy/projet%20php/home.png' class="recadrageImg" id="position">
				<input class="enteteD" type='submit' value='Home'>
			</div>
		</div>
	</form>
</header>
<body>
	<center>

		<p  class="h4" id="test">
			La blague originale:
			<br>
			<br>
			<?php echo $blagueAnglais;?>
			<br>
			<br>
			La blague traduite:
			<br>
			<br>
			<?php echo $blagueTraduite;?>
			<br>
			<br>
			<div class="rating sizeStar">
				<p class="h6 sousligner">: note actuelle de la blague </p>
			<a  id="5" title="Give 5 stars" onclick="NoterBlague(5)">★</a><!--
			--><a href="notationEtoile.php?nbrEtoile=4" title="Give 4 stars">★</a><!--
			--><a href="notationEtoile.php?nbrEtoile=3" title="Give 3 stars">★</a><!--
			--><a href="notationEtoile.php?nbrEtoile=2" title="Give 2 stars">★</a><!--
		--><a href="notationEtoile.php?nbrEtoile=1" title="Give 1 star">★</a>
	</div>

</div>

</p>
</center>

<!--
   <div class="rating  ml-15 ">
		<p class="h5 sousligner ">: note de la blague </p>
        <a href="#5" id="7" title="Give 5 stars" onclick="valeurNoteTraduction(5)">★</a>
        <a href="#4" title="Give 4 stars" onclick="valeurNoteTraduction(4)">★</a>
        <a href="#3" title="Give 3 stars" onclick="valeurNoteTraduction(3)">★</a>
        <a href="#2" title="Give 2 stars" onclick="valeurNoteTraduction(2)">★</a>
        <a href="#1" title="Give 1 star" onclick="valeurNoteTraduction(1)">★</a>
    </div>
-->
</body>
</html>

