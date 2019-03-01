<?php
	$chemin_data='data/';	
	$json = json_decode(preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $_POST["jsondatapost"]), true);
	if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
             exit("Json illisible");
	}
	$lelogin = preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["login"]);
	$lepass = preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["pass"]);
	$lavariante=preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["variante"]);
	$lemail=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["mail"]);
	$letab=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["etab"]);
	
if ((!empty($json))&&(!empty($lelogin))&&(!empty($lepass))&&(!empty($lavariante))&&(!empty($lemail))&&(!empty($letab)))
{
	setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');
	date_default_timezone_set('Europe/Paris');

//**************** A CHANGER POUR ACTIVER LA NOTIFICATION PAR MAIL ******************
	$lemail="non"; //il faut effacer cette ligne puis paramétrer correctement les suivantes (Attention il faut que le smtp soit renseigné dans php.ini) 
	$to ="julien.pavageau@MON_NOM_DE_DOMAINE.fr";
	$subject = "Trio ".$lavariante." par GitHub";
	$headers = 'From: webmaster@MON_NOM_DE_DOMAINE.net' . "\r\n" .'Reply-To: webmaster@MON_NOM_DE_DOMAINE.net' . "\r\n";
//********************************************************************************
	
	if (!is_dir($chemin_data.$letab)) mkdir($chemin_data.$letab,0755);
	$nomfichier=$chemin_data.$letab.'/mdp.csv';
	$avecsv = is_file($nomfichier);
	$elevetrouve=false;
	if ($avecsv) {
		$fdistosl = fopen($nomfichier, "r");
		$md5_fait=true; // par défaut on suppose les mots de passe sont déjà hashés dans mdp.csv mais c'est possible de s'en passer
		// pour hasher le mdp  en md5 dans un csv on peut utiliser calc avec une formule du genre "=SERVICEWEB(CONCATENER("http://www.netb.be/excelfunctions/md5.php?data=";B2))"
		$numligne = 0;
		while ((!feof($fdistosl))&&(!$elevetrouve)) {
			// Je laisse pour s'il y a un soucis une année avec un csv mal construit
			if ($numligne!=0) if ($erreurfichier) echo "Le fichier avec les mdp semble incohérent à la ligne ".$numligne.". Ca vient peut être du .csv qui a une dernière ligne vide. Sinon, les données doivent être séparées par des points virgules ou alors il faut modifier la ligne de changemot.php où il y est écrit SEPARATEUR";
			$erreurfichier=false;
			$ligne = fgets($fdistosl,2048) ;
			$numligne++;
			list($idutilisateur,$motdepasse)=explode(";", $ligne);
			$motdepasse= preg_replace("/(\r\n|\n|\r)/", "", $motdepasse);
			if (strpos($idutilisateur,'login')===FALSE){
				if (empty($idutilisateur) || empty($motdepasse) ) {
					$erreurfichier=true;
				}else{
					if (strcasecmp($lelogin,$idutilisateur)==0){
						if ($md5_fait){
							$motdepassemd5=$motdepasse;
						} else {
							$motdepassemd5=md5($motdepasse);
						}
						if (strcmp($lepass,$motdepassemd5)==0){
							$elevetrouve=true;
							file_put_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
							header("Content-type: application/json");
							$inscrit=" (inscrit dans ".$letab.")";
							print "1";
						}
					}
				}
			} else { if (strpos($motdepasse,'md5')===FALSE){ $md5_fait=false; } } // si le deuxième champ n'est pas md5 alors c'est que les mdp ne sont pas cryptés
		}
		fclose($fdistosl);
		if (!$elevetrouve) {
			$inscrit=" (pas inscrit dans ".$letab." donc enregistré comme visiteur)";
			file_put_contents($chemin_data."visiteur/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
			header("Content-type: application/json");
			print "3";
		}
	} else {
		if (strcasecmp($letab,"sourcescompetitions")==0){
			if (is_file($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt")){
				$lemail="non";
				$inscrit=" = COMPETITION déjà existante";
				print "3";
			} else {
				file_put_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
				header("Content-type: application/json");
				$inscrit=" = NOUVELLE COMPETITION";
				file_put_contents($chemin_data.$letab."/".$lelogin.".csv","identifiant (etablissement);score;".strftime("%d/%m/%y à %H:%M:%S")."\n");
				print "1";
			}
		} else {
			file_put_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
			header("Content-type: application/json");
			if (strcasecmp($letab,"visiteur")==0){
				$inscrit=" (enregistré comme visiteur)";
				print "3";
			} else {
				$inscrit=" (enregistré dans ".$letab.")";
				print "2";
			}
		}
	}

	if(strcmp($lemail,"oui")==0){ 
		$body='';
		if ((!$elevetrouve)&&($avecsv)) $body="Mot de passe ou login non reconnu  \n";
		$body.="Login : ".$lelogin." pour la variante ".$lavariante." pour l'établissement ".$letab." \n ".print_r($json,true)." \n  \n  \n ".serialize($json);
		if (mail($to, $subject, $body,$headers)) {
			//  echo("email parti");
		} else {
			//  echo("pas parti");
		}
	}

	if (strcasecmp($letab,"sourcescompetitions")==0){
		file_put_contents($chemin_data."logs.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin.$inscrit." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
	} else {
		if (strcasecmp($lavariante,"Competition")==0){
			$nomfichiercsv=preg_replace('/[^a-zA-Z0-9.]+/', '',$json["PlateauJson"][0][0]);
			file_put_contents($chemin_data."sourcescompetitions/".$nomfichiercsv.".csv",$lelogin.$inscrit.";".strip_tags ($json["ScoreJson"]).";".strftime("%d/%m/%y à %H:%M:%S")."\r\n",FILE_APPEND);
		} else {
			file_put_contents($chemin_data."logs.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin.$inscrit." a sauvé ".$lavariante." (score=".strip_tags($json["ScoreJson"]).") depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);
		}
	}

}

?>
