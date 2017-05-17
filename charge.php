<?php
	$chemin_data='data/';	
	$lelogin = preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["login"]);
	$lepass = preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["pass"]);
	$lavariante=preg_replace('/[^a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ.]+/', '', $_POST["variante"]);
	$letab=preg_replace('/[^a-zA-Z0-9.]+/', '', $_POST["etab"]);
	date_default_timezone_set('Europe/Paris');
	$sauvegardetrouve=false;
   
if ((strcasecmp($letab,"")==0)||(strcasecmp($letab,"college")==0)||empty($letab)){
	$nomfichier=$chemin_data.'mdp.csv';
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
				if ($md5_fait){
					$motdepassemd5=$motdepasse;
				} else {
					$motdepassemd5=md5($motdepasse);
				}
				if ((strcasecmp($lelogin,$idutilisateur)==0)&&(strcmp($lepass,$motdepassemd5)==0)){
					$elevetrouve=true;
					if (is_file($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
						$json=json_encode(unserialize((file_get_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
						$sauvegardetrouve=true;
						echo $json;
					}							
					else { print "20"; } 
					$inscrit=" (inscrit)";
				}
			}
		} else { if (strpos($motdepasse,'md5')===FALSE){ $md5_fait=false; } } // si le deuxième champ n'est pas md5 alors c'est que les mdp ne sont pas cryptés
	}
	fclose($fdistosl);
}
 
if (!$sauvegardetrouve)
{
	if ((strcasecmp($letab,"")==0)||(strcasecmp($letab,"college")==0)||(strcasecmp($letab,"sontpasdici")==0)||empty($letab)){
		if (is_file($chemin_data."sontpasdici/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
			$json=json_encode(unserialize((file_get_contents($chemin_data."sontpasdici/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
			$sauvegardetrouve=true;
			$inscrit=" (pas inscrit)";
			echo $json;
		}
	} else if (is_file($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt")) {
		$json=json_encode(unserialize((file_get_contents($chemin_data.$letab."/".$lelogin."_".$lepass."_".$lavariante.".txt"))));
		$sauvegardetrouve=true;
		$inscrit=" inscrit dans l'établissement ".$letab;
		echo $json;
	} //else print "0"; //echo $json;
}
 
setlocale(LC_TIME, 'fr','fr_FR','fr_FR@euro','fr_FR.utf8','fr-FR','fra');

if (!$sauvegardetrouve) {
	if (!$elevetrouve) print "0"; // si l'élève est dans mdp mais qu'il n'y a pas de sauvegarde il y aura donc eu un print "20"
	file_put_contents($chemin_data."logs_load.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin." ".$inscrit." depuis ".$letab." a échoué pour charger ".$lavariante." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND); 
}	
else file_put_contents($chemin_data."logs_load.txt", strftime("Le %A %d %B à %H:%M:%S")." -> ".$lelogin." ".$inscrit." depuis ".$letab." a chargé ".$lavariante." depuis l'IP ".getenv('REMOTE_ADDR')." avec ".$_SERVER[ 'HTTP_USER_AGENT']."\r\n",FILE_APPEND);

?>