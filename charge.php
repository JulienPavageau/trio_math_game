<?php
	$chemin_data='data/';	
	$lelogin = preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["login"]);
	$lepass = preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["pass"]);
	$lavariante=preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["variante"]);
	$letab=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["etab"]);
	date_default_timezone_set('Europe/Paris');
	$sauvegardetrouve=false;
	$nomfichier=$chemin_data.$letab.'/mdp.csv';
	$avecsv = is_file($nomfichier);
	
if ($avecsv) {
	$fdistosl = fopen($nomfichier, "r");
	$elevetrouve=false;
	$sauvegardetrouve=false;
	$md5_fait=true; // par défaut on suppose les mots de passe sont déjà hashés dans mdp.csv mais c'est possible de s'en passer
	while ((!feof($fdistosl))&&(!$elevetrouve)) {
		if ($numligne!=0) if ($erreurfichier) echo "Le fichier avec les mdp semble incohérent à la ligne ".$numligne." Ca vient peut être du .csv qui a une dernière ligne vide. Sinon, les données doivent être séparées par des points virgules ou alors il faut modifier la ligne de changemot.php où il y écrit SEPARATEUR";
		$erreurfichier=false;
		$ligne = fgets($fdistosl,2048) ;
		$numligne++; 
		list($idutilisateur,$motdepasse)=explode(";", $ligne);
		$motdepasse= preg_replace("/(\r\n|\n|\r)/", "", $motdepasse);
		if (strpos($idutilisateur,'login')===FALSE){
			if (empty($idutilisateur)|| empty($motdepasse) ) {
				$erreurfichier=true;
			}else{
				if (strcasecmp($lelogin,$idutilisateur)==0){
					if ($md5_fait){
						$motdepassemd5=$motdepasse;
					} else {
						$motdepassemd5=md5($motdepasse);
					}
					if (strcmp($lepass,$motdepassemd5)==0){
						$inscrit=" (inscrit dans ".$letab.")";
						$elevetrouve=true;
						if (is_file($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
							$json=json_encode(unserialize((file_get_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
							$sauvegardetrouve=true;
							echo $json;
						}							
					}

				}
			}
		} else { if (strpos($motdepasse,'md5')===FALSE){ $md5_fait=false; } } // si le deuxième champ n'est pas md5 alors c'est que les mdp ne sont pas cryptés
	}
	fclose($fdistosl);
}
 
if (!$sauvegardetrouve)
{
	if (is_file($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
		$json=json_encode(unserialize((file_get_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
		$sauvegardetrouve=true;
		$inscrit=" (trouvé dans ".$letab.")";
		echo $json;
	} else if  (is_file($chemin_data."visiteur/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
		if ($elevetrouve) {
			$inscrit=" (inscrit dans ".$letab." mais trouvé dans visiteur)";
		} else {
			$inscrit=" (trouvé dans visiteur mais pas dans ".$letab.")";			
		}
		$json=json_encode(unserialize((file_get_contents($chemin_data."visiteur/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
		$sauvegardetrouve=true;
		echo $json;
	}
}
 
setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');

if (!$sauvegardetrouve) {
	if (!$elevetrouve) {
		if ($avecsv) {
			$inscrit=" (pas inscrit dans ".$letab.")";
			print "1";
		} else {
			$inscrit=" (pas de csv dans ".$letab.")";
			print "2";
		}
	} else {
		print "0"; 
	}
	file_put_contents($chemin_data."logs_load.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin." ".$inscrit." a échoué pour charger ".$lavariante." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND); 
}	
else file_put_contents($chemin_data."logs_load.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin." ".$inscrit." a chargé ".$lavariante." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);

?>