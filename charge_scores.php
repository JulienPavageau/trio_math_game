<?php
	$chemin_data='data/sourcescompetitions/';	
	$lecode=preg_replace('/[^a-zA-Z0-9.]+/', '', $_GET["code"]);
	date_default_timezone_set('Europe/Paris');
	$nomfichier=$chemin_data.$lecode.'.csv';
	$contenu = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr-fr' lang='fr-fr' ><head><meta http-equiv='content-type' content='text/html; charset=utf-8' /><meta name='author' content='J. Pavageau' /><title>CONSULTATION DES SCORES</title><link href='favicon.png' rel='shortcut icon' type='' /><link rel='stylesheet' href='trio.css' type='text/css' /></head><body >";

if (is_file($nomfichier)) {
	setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');
	date_default_timezone_set('Europe/Paris');
	$fdistosl = fopen($nomfichier, "r");
	$compteur_best = 0;
	$identifiant_best = "";
	$date_best = "";
	$tableau_DEBUT = "<center><TABLE cellpadding=0 cellspacing=0 border=1><tbody><TR valign='middle' align='center'><TD width=400><b>Identifiant</b></TD><TD width=100><b>Score</b></TD><TD width=200><b>Date/Heure</b></TD></TR>";
	$tableau_MILIEU = "";
	$numligne = 0;
	while (!feof($fdistosl)) {
		if ($numligne!=0) if ($erreurfichier) echo "Le fichier etab.csv semble incohérent à la ligne ".$numligne.". Ca vient peut être du .csv qui a une dernière ligne vide. Sinon, les données doivent être séparées par des points virgules.";
		$erreurfichier=false;
		$ligne = fgets($fdistosl,2048) ;
		$numligne++; 
		list($idutilisateur,$score,$date)=explode(";", $ligne);
		$date= preg_replace("/(\r\n|\n|\r)/", "", $date);
		if (strpos($idutilisateur,'identifiant (etablissement)')===FALSE){
			if ( ( empty($score) || empty($date) ) || (empty($idutilisateur)) ) {
				if ($erreurfichier) { // pour éviter d'avoir un message à cause de la dernière ligne
					$tableau_MILIEU = "<TR valign='middle' align='center'><TD colspan='3'><i>Une ligne du fichier est incohérente.</i></TD></TR>".$tableau_MILIEU;
					$erreurfichier=false;
				} else {
					$erreurfichier=true;
				}
			} else {
				if ( $score > $compteur_best ) {
					$identifiant_best = $idutilisateur;
					$compteur_best = $score;
					$date_best = $date;
				}
				$tableau_MILIEU = "<TR valign='middle' align='center'><TD width=300>".$idutilisateur."</TD><TD width=100>".$score."</TD><TD width=200>".$date."</TD></TR>".$tableau_MILIEU;
			}
		} else { 
			$tableau_AVANT = "<center><H3>SCORES DE LA COMPÉTITION : ".$lecode." (code créé le ".$date.")</H3></center>";
		} 
	}
	fclose($fdistosl);
	if ($compteur_best == 0) { 
		$tableau_MILIEU .= "<TR valign='middle' align='center'><TD colspan='3'><i>Aucune participation pour l'instant.</i></TD></TR>";
	} else {
		$tableau_AVANT .= "<center>Le meilleur score est pour l'instant celui de <b>".$identifiant_best."</b><br/>avec ".$compteur_best." points obtenus le ".$date_best.".</center><br/><center>Vous trouverez cependant ci-dessous le tableau complet de toutes les sauvegardes en particulier en cas de litige liéé à l'heure du score.</center><br/><br/>";
//	"<br/>Vous trouverez ci-dessous un lien pour télécharger un fichier au format csv (à ouvrir avec un tableur)<br/> qui contient un résumé de toutes les sauvegardes présentes actuellement dans tous les dossiers :<br/><a href='data/export/export.csv'>votre fichier csv</a><br/><br/>";	
	}
	$contenu .= $tableau_AVANT.$tableau_DEBUT.$tableau_MILIEU."</tbody></table></center>";
	file_put_contents("data/logs_charge_scores.txt", strftime("Le %A %d %B à %H:%M:%S")." -> Code ".$lecode." consulté depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
} else {
	$contenu .= "<center><H3>LE CODE ".$lecode." N'EST PAS VALIDE !</H3></center>";
	file_put_contents("data/logs_charge_scores.txt", strftime("Le %A %d %B à %H:%M:%S")." -> Code ".$lecode." ECHEC depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
}

echo $contenu."</body></html>";

 ?>