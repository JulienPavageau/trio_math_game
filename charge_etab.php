<?php
	$chemin_data='data/';	
	date_default_timezone_set('Europe/Paris');
	$nomfichier=$chemin_data.'etab.csv';
	$contenu_fieldset = "<LEGEND><i class='fa fa-floppy-o fa-lg'></i>&nbsp; Arrêter ou reprendre une partie :</LEGEND><CENTER><TABLE cellpadding=0 cellspacing=0 border=0><tbody><TR valign='middle'><TD height=30><SELECT NAME='ListeEtab' SIZE=1>";
	
if (is_file($nomfichier)) {
	$fdistosl = fopen($nomfichier, "r");
	$selected_fait=false;
	while (!feof($fdistosl)) {
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
				if ($selected_fait){
					$contenu_fieldset = $contenu_fieldset."<OPTION VALUE='";
				} else {
					$contenu_fieldset = $contenu_fieldset."<OPTION SELECTED VALUE='";
					$selected_fait=true;
				}
				$contenu_fieldset = $contenu_fieldset.$etab."'>".$descriptif."</option>";
			}
		}
	}
	$contenu_fieldset = $contenu_fieldset."</SELECT></TD></TR></tbody></table></CENTER><CENTER><TABLE cellpadding=0 cellspacing=0 border=0><tbody><TR valign='middle'><TD height=30 width=100>Identifiant : </TD><TD height=30 width=90><INPUT type='text' name='Identifiant' size=10 value=''/></TD></TR><TR valign='middle'><TD height=30 width=100>Mot de passe : </TD><TD height=30 width=90><INPUT type='password' name='Motdepasse' size=10 value=''/></TD></TR></tbody></table><I>Les visiteurs choisissent leurs propres identifiants et les élèves du collège doivent utiliser ceux du <a href='javascript:alert_reseau_college();'>réseau</a>. Une seule sauvegarde par variante n'est possible, la dernière écrasera donc la précédente.</i><br/><INPUT type='button' value='SAUVER' onclick='Sauver();'/> <INPUT type='button' value='CHARGER' onclick='Charger();'/></CENTER>";
	fclose($fdistosl);
	echo $contenu_fieldset;
} else {
	echo "Le fichier csv des établissements n'est pas présent.";
}
 
?>