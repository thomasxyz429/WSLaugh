<?php
if (isset($_GET['nbrEtoile']) && isset($_GET['idBlague'])) {

	$dbNote=file_get_contents("./dataBase.json");
	$mapNote=json_decode($dbNote,true);
	$tailleBDNote=sizeof($mapNote["notjokes"]);
	$nbrVote;
	$noteActuelle;
	$positionBlagueJSON;

	for ($y=0; $y<$tailleBDNote ; $y++) { 

		if ($mapNote["notjokes"][$y]["id"]==$_GET['idBlague']) {
			$noteActuelle=$mapNote["notjokes"][$y]["note"];
			$nbrVote=$mapNote["notjokes"][$y]["nbrVote"];
			$positionBlagueJSON=$y;
		}
	}
	$noteFutur= $noteActuelle*$nbrVote+$_GET['nbrEtoile'];
	$noteFutur=$noteFutur/($nbrVote+1);
	$noteFutur=round($noteFutur);


	$mapNote["notjokes"][$positionBlagueJSON]["note"]=$noteFutur;
	$mapNote["notjokes"][$positionBlagueJSON]["nbrVote"]=$nbrVote+1;
	$mapNoteFinis=json_encode($mapNote,true);
	file_put_contents("dataBase.json", $mapNoteFinis);
	header('Location: acceuil.html');
}

?>