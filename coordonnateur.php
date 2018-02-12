<?php

	$chemin_data='data/';	
	$letab=preg_replace('/[^a-zA-Z0-9.]+/', '', $_GET["etab"]);
	$lelogin = preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_GET["login"]);
	$lepass = preg_replace('/[^a-zA-Z0-9.]+/', '', $_GET["pass"]);
	date_default_timezone_set('Europe/Paris');
	$nomfichier=$chemin_data.'etab.csv';
	$coordo_verifie = false;
	$contenu = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr-fr' lang='fr-fr' ><head><meta http-equiv='content-type' content='text/html; charset=utf-8' /><meta name='author' content='J. Pavageau' /><title>INTERFACE COORDONNATEUR</title><link href='favicon.png' rel='shortcut icon' type='' /><link rel='stylesheet' href='trio.css' type='text/css' /></head><body ><CENTER>";

if (is_file($nomfichier)) {
	setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');
	date_default_timezone_set('Europe/Paris');
	$fdistosl = fopen($nomfichier, "r");
	$md5_fait=true; // par défaut on suppose les mots de passe sont déjà hashés dans etab.csv mais c'est possible de s'en passer
	while ((!feof($fdistosl)) && (!$coordo_verifie)) {
		if ($numligne!=0) if ($erreurfichier) echo "Le fichier etab.csv semble incohérent à la ligne ".$numligne.". Ca vient peut être du .csv qui a une dernière ligne vide. Sinon, les données doivent être séparées par des points virgules.";
		$erreurfichier=false;
		$ligne = fgets($fdistosl,2048) ;
		$numligne++; 
		list($etab,$descriptif,$idutilisateur,$motdepasse)=explode(";", $ligne);
		$motdepasse= preg_replace("/(\r\n|\n|\r)/", "", $motdepasse);
		if (strpos($idutilisateur,'login')===FALSE){
			if ( (empty($etab) || empty($descriptif)) || (empty($idutilisateur) || empty($motdepasse)) ) {
				$erreurfichier=true;
			} else {
				if ((strcmp($letab,$etab)==0) && (strcmp($lelogin,$idutilisateur)==0)) {
					if ($md5_fait){
						$motdepassemd5=$motdepasse;
					} else {
						$motdepassemd5=md5($motdepasse);
					}
					if (strcmp($lepass,$motdepassemd5)==0) {
						$num_coordo = $numligne;
						$coordo_verifie = true;
					}
				}
			}
		} else { if (strpos($motdepasse,'md5')===FALSE){ $md5_fait=false; } } // si le deuxième champ n'est pas md5 alors c'est que les mdp ne sont pas cryptés
	}
	fclose($fdistosl);
	if ($coordo_verifie) {
		$compteur["Concours.txt"] = 0;
		$compteur["Classique.txt"] = 0;
		$compteur["UnPourToutes.txt"] = 0;
		$compteur["TousPourUne.txt"] = 0;
		$compteur["Classe.txt"] = 0;
		$tableau["DEBUT"] = "<TABLE cellpadding=0 cellspacing=0 border=1><tbody><TR valign='middle' align='center'><TD width=50><b>n°</b></TD><TD width=300><b>Identifiant</b></TD><TD width=100><b>Score</b></TD><TD width=200><b>Date/Heure</b></TD></TR>";
		$tableau["Concours.txt"] = "<H3>VARIANTE CONCOURS</H3>".$tableau["DEBUT"];
		$tableau["Classique.txt"] = "<H3>VARIANTE CLASSIQUE</H3>".$tableau["DEBUT"];
		$tableau["UnPourToutes.txt"] = "<H3>VARIANTE UN POUR TOUTES</H3>".$tableau["DEBUT"];
		$tableau["TousPourUne.txt"] = "<H3>VARIANTE TOUS POUR UNE</H3>".$tableau["DEBUT"];
		$tableau["Classe.txt"] = "<H3>VARIANTE POUR LA CLASSE</H3>".$tableau["DEBUT"];
		if ($num_coordo == 2) { //cas où la demande est faite par l'"administrateur" (celui qui est au début du fichier etab.csv)
			$xls = "etab;variante;identifiant;score;date_heure\n";
			$fdistosl = fopen($nomfichier, "r");
			$dir = "data/export"; // c est juste pour se placer dans un sous dossier de data pour pouvoir répéter le chdir plus bas 
			chdir($dir);
			while (!feof($fdistosl)) {
				if ($numligne!=0) if ($erreurfichier) echo "Le fichier etab.csv semble incohérent à la ligne ".$numligne.". Ca vient peut être du .csv qui a une dernière ligne vide. Sinon, les données doivent être séparées par des points virgules.";
				$erreurfichier=false;
				$ligne = fgets($fdistosl,2048) ;
				$numligne++; 
				list($etab,$descriptif,$idutilisateur,$motdepasse)=explode(";", $ligne);
				if (strpos($idutilisateur,'login')===FALSE){
					if ( (empty($etab) || empty($descriptif)) || (empty($idutilisateur) || empty($motdepasse)) ) {
						$erreurfichier=true;
					} else {
						$tableau["debut_etab"] = "<TR valign='middle' align='center'><TD colspan='4'>Sauvegarde pour : ".$etab."</TD></TR>";
						$tableau["Concours.txt"] .= $tableau["debut_etab"];
						$tableau["Classique.txt"] .= $tableau["debut_etab"];
						$tableau["UnPourToutes.txt"] .= $tableau["debut_etab"];
						$tableau["TousPourUne.txt"] .= $tableau["debut_etab"];
						$tableau["Classe.txt"] .= $tableau["debut_etab"];
						$xls_etab = "";
						$dir = "../".$etab;
						chdir($dir);
						array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
						foreach($files as $filename) {
							if (($filename!="mdp.csv") && ($filename!="export.csv")){
								list($pseudo,$pass,$jeu)=explode("_", $filename);
								$varia=(unserialize((file_get_contents($filename))));
								$compteur[$jeu]++;
								$tableau[$jeu] .= "<TR valign='middle' align='center'><TD width=50>".$compteur[$jeu]."</TD><TD width=300>".$pseudo."</TD><TD width=100>".$varia["ScoreJson"]."</TD><TD width=200>".date ("d F Y H:i:s", filemtime($filename))."</TD></TR>";
								$xls_etab.= $etab.";".substr($jeu, 0, strpos($jeu, ".")).";".$pseudo.";".$varia["ScoreJson"].";".date ("d F Y H:i:s", filemtime($filename))."\n";
							}
						}
						$xls .= $xls_etab;
						$xls_etab = "etab;variante;identifiant;score;date_heure\n".$xls_etab;
						file_put_contents("../export/".$etab.".csv",$xls_etab);
					}
				}
			}
			file_put_contents("../export/export.csv",$xls);
			fclose($fdistosl);
		} else {
			$xls = "variante;identifiant;score;date_heure\n";
			$dir = $chemin_data.$letab;
			chdir($dir);
			array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
			foreach($files as $filename) {
				if (($filename!="mdp.csv") && ($filename!="export.csv")){
					list($pseudo,$pass,$jeu)=explode("_", $filename);
					$varia=(unserialize((file_get_contents($filename))));
					$compteur[$jeu]++;
					$tableau[$jeu] .= "<TR valign='middle' align='center'><TD width=50>".$compteur[$jeu]."</TD><TD width=300>".$pseudo."</TD><TD width=100>".$varia["ScoreJson"]."</TD><TD width=200>".date ("d F Y H:i:s", filemtime($filename))."</TD></TR>";
					$xls.= substr($jeu, 0, strpos($jeu, ".")).";".$pseudo.";".$varia["ScoreJson"].";".date ("d F Y H:i:s", filemtime($filename))."\n";
				}
			}
			file_put_contents("export.csv",$xls);
		}
		$tableau["VIDE"] = "<TR valign='middle' align='center'><TD colspan='4'><i>Aucune sauvegarde pour cette variante.</i></TD></TR>";
		if ($compteur["Concours.txt"] == 0) { $tableau["Concours.txt"] .= $tableau["VIDE"]; }
		if ($compteur["Classique.txt"] == 0) { $tableau["Classique.txt"] .= $tableau["VIDE"]; }
		if ($compteur["UnPourToutes.txt"] == 0) { $tableau["UnPourToutes.txt"] .= $tableau["VIDE"]; }
		if ($compteur["TousPourUne.txt"] == 0) { $tableau["TousPourUne.txt"] .= $tableau["VIDE"]; }
		if ($compteur["Classe.txt"] == 0) { $tableau["Classe.txt"] .= $tableau["VIDE"]; }
		$tableau["FIN"] = "</tbody></table><br/>";
		if ($num_coordo == 2) { 
			$contenu .= "<br/>Vous trouverez ci-dessous un lien pour télécharger un fichier au format csv (à ouvrir avec un tableur)<br/> qui contient un résumé de toutes les sauvegardes présentes actuellement dans tous les dossiers :<br/><a href='data/export/export.csv'>votre fichier csv</a><br/><br/>";	
		} else {
			$contenu .= "<br/>Vous trouverez ci-dessous un lien pour télécharger un fichier au format csv (à ouvrir avec un tableur)<br/> qui contient un résumé de toutes les sauvegardes présentes actuellement dans votre dossier :<br/><a href='data/".$etab."/export.csv'>votre fichier csv</a><br/><br/>";	
		}
		$contenu .= "Vous trouverez également ci-dessous des tableaux contenant ces informations classées par variante :<br/>".$tableau["Concours.txt"].$tableau["FIN"].$tableau["Classique.txt"].$tableau["FIN"].$tableau["UnPourToutes.txt"].$tableau["FIN"].$tableau["TousPourUne.txt"].$tableau["FIN"].$tableau["Classe.txt"].$tableau["FIN"];
		$dir = "..";
		chdir($dir);
		if ($num_coordo == 2) { 
			$contenu .= "<H3>LOGS SAUVEGARDES</H3><div style='border: 1px black solid; overflow:auto;height:300px;'><P align='left'>"; 
			$contenu .= nl2br(file_get_contents("logs.txt"));
			$contenu .= "</p></div><H3>LOGS CHARGEMENTS</H3><div style='border: 1px black solid; overflow:auto;height:300px;'><P align='left'>";
			$contenu .= nl2br(file_get_contents("logs_load.txt"));
			$contenu .= "</p></div><H3>LOGS COORDONNATEURS</H3><div style='border: 1px black solid; overflow:auto;height:300px;'><P align='left'>";
			$contenu .= nl2br(file_get_contents("logs_coordo.txt"))."</p></div>";
		}
		file_put_contents("logs_coordo.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$letab."/".$lelogin."/".$lepass." a réussi depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
	} else {
		$contenu .= "Les identifiants ne correspondent pas à l'établissement sélectionné.";
		file_put_contents($chemin_data."logs_coordo.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$letab."/".$lelogin."/".$lepass." a ECHOUE depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
	}
} else {
	$contenu .= "Le fichier csv des établissements n'est pas présent.";
}

echo $contenu."</CENTER></body></html>";

 ?>