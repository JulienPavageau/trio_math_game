<?php
	$chemin_data='data/';	
	$json = json_decode(preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $_POST["jsondatapost"]), true);
	$lelogin = preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["login"]);
	$lepass = preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["pass"]);
	$lavariante=preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["variante"]);
	$lemail=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["mail"]);
	$letab=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["etab"]);
	
if ((!empty($json)&&(!empty($lelogin)&&(!empty($lepass)&&(!empty($lavariante)&&(!empty($lemail)&&(!empty($letab)){

	setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');
	date_default_timezone_set('Europe/Paris');

//**************** A CHANGER POUR ACTIVER LA NOTIFICATION PAR MAIL ******************
	$lemail="non"; //il faut effacer cette ligne puis paramétrer correctement les suivantes (Attention il faut que le smtp soit renseigné dans php.ini) 
	$to ="julien.pavageau@MON_NOM_DE_DOMAINE.fr";
	$subject = "Trio ".$lavariante." par GitHub";
	$headers = 'From: webmaster@MON_NOM_DE_DOMAINE.net' . "\r\n" .'Reply-To: webmaster@MON_NOM_DE_DOMAINE.net' . "\r\n";
//********************************************************************************
	
if ((strcasecmp($letab,"")==0)||(strcasecmp($letab,"college")==0)||empty($letab)){
	$nomfichier=$chemin_data.'mdp.csv';
	$fdistosl = fopen($nomfichier, "r");
	$elevetrouve=false;
	$md5_fait=true; // par défaut on suppose les mots de passe sont déjà hashés dans mdp.csv mais c'est possible de s'en passer
	// pour hasher le mdp  en md5 dans un csv on peut utiliser calc avec une formule du genre "=SERVICEWEB(CONCATENER("http://www.netb.be/excelfunctions/md5.php?data=";B2))"
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
				if ($md5_fait){
					$motdepassemd5=$motdepasse;
				} else {
					$motdepassemd5=md5($motdepasse);
				}
				if ((strcasecmp($lelogin,$idutilisateur)==0)&&(strcmp($lepass,$motdepassemd5)==0)){
					$elevetrouve=true;
					file_put_contents($chemin_data."college/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
					header("Content-type: application/json");
					$inscrit=" (inscrit)";
					print "1";
				}
			}
		} else { if (strpos($motdepasse,'md5')===FALSE){ $md5_fait=false; } } // si le deuxième champ n'est pas md5 alors c'est que les mdp ne sont pas cryptés
	}
	fclose($fdistosl);
}

if(strcmp($lemail,"oui")==0){ 
	$body='';
	if (!$elevetrouve) $body="Mot de passe ou login non reconnu  \n";
	$body.="Login : ".$lelogin." pour la variante ".$lavariante." pour l'établissement ".$letab." \n ".print_r($json,true)." \n  \n  \n ".serialize($json);
	if (mail($to, $subject, $body,$headers)) {
		//  echo("email parti");
	} else {
		//  echo("pas parti");
	}
}

if (!$elevetrouve) {
	if ((strcasecmp($letab,"")==0)||(strcasecmp($letab,"college")==0)||(strcasecmp($letab,"sontpasdici")==0)||empty($letab)){
		$inscrit=" (pas inscrit)";
		file_put_contents($chemin_data."sontpasdici/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
	}else{
		$inscrit=" provient de ".$letab;
		if (!is_dir($chemin_data.$letab)) mkdir($chemin_data.$letab,0755);
		file_put_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt",(serialize($json)));
	}
	header("Content-type: application/json");
	print "3";
}

file_put_contents($chemin_data."logs.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin.$inscrit." a sauvé ".$lavariante." score :".$json["ScoreJson"].";".$json["JeuxJson"][0]." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);

}

?>