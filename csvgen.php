<?php
	date_default_timezone_set('Europe/Paris');
	$chemin_data='data/';

	
$dir = $chemin_data."college";
chdir($dir);
array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
$phrase= '<pre><b><u> Par date décroissante, voici la substantifique moelle de Concours pour ceux qui sont dans mdp :</u></b><br/>';
$xls="";
foreach($files as $filename) {
	if (($filename!="mdp.csv")&&($filename!="logs_load.txt")&&($filename!="logs.txt")){
		list($pseudo,$pass,$jeu)=explode("_", $filename);
		if ($jeu=="Concours.txt"){
			$varia=(unserialize((file_get_contents($filename))));
			$xls.= $pseudo.";".$varia["ScoreJson"].";".$varia["JeuxJson"][0].";".date ("d F Y H:i:s", filemtime($filename))."\n";
		}
	}
}
file_put_contents("../export/trioconcours.csv",$xls);
$phrase.= nl2br($xls). '</pre>';

$dir = "../college";
chdir($dir);
array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
$phrase.= '<pre><br/><br/><br/><b><u> Par date décroissante, voici les sauvegardes pour ceux qui sont dans mdp :</u></b>';
foreach($files as $filename) {
	if (($filename!="mdp.csv")&&($filename!="logs_load.txt")&&($filename!="logs.txt")){
		list($pseudo,$pass,$jeu)=explode("_", $filename);
		$phrase.= "<br/><br/><br/><br/>&nbsp;&nbsp;&nbsp;<font size=\"3\" color=\"red\">".$pseudo." a sauvegardé à ".$jeu." le ".date ("d F Y H:i:s.", filemtime($filename))."</font><br/><br/><font size=\"1\" color=\"black\">";
		$phrase.= (urldecode(file_get_contents($filename)));
		$phrase.=  "</font>";
	}
}  
$phrase.=  '</pre>';    
    
$dir = "../sontpasdici";
chdir($dir);
array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
$phrase.= '<pre><br><br><br><b><u> Par date décroissante, voici les sauvegardes pour ceux qui ne sont pas dans mdp :</u></b>';
foreach($files as $filename) {
	list($pseudo,$pass,$jeu)=explode("_", $filename);
	$phrase.= "<br/><br/><br/><br/>&nbsp;&nbsp;&nbsp;<font size=\"3\" color=\"red\">".$pseudo." a sauvegardé à ".$jeu." le ".date ("d F Y H:i:s.", filemtime($filename))."</font><br/><br/><font size=\"1\" color=\"black\">";
	$phrase.= (urldecode(file_get_contents($filename)));
	$phrase.=  "</font>";
}  
$phrase.=  '</pre>';
$dir = "..";
chdir($dir);
$phrase.= '<br/><br/><br/><b><u> Logs des sauvegardes :</u></b><br/>';
$phrase.=nl2br(file_get_contents("logs.txt"));
$phrase.= '<br/><br/><br/><b><u> Logs des chargement :</u></b><br/>';
$phrase.=nl2br(file_get_contents("logs_load.txt"));


function Creer_CSV ($etab = "college")
{
	chdir($etab);
	$files="";
	array_multisort(array_map('filemtime', ($files = glob("*.*", GLOB_BRACE))), SORT_DESC, $files);
	$xls="";$pseudo="";$pass="";$jeu="";
	foreach($files as $filename) {
		if (($filename!="mdp.csv")&&($filename!="logs_load.txt")&&($filename!="logs.txt")){
			list($pseudo,$pass,$jeu)=explode("_", $filename);
			if ($jeu=="Concours.txt"){
				$varia=(unserialize((file_get_contents($filename))));
				$xls.= $pseudo.";".$varia["ScoreJson"].";".$varia["JeuxJson"][0].";".date ("d F Y H:i:s", filemtime($filename))."\n";
			}
		}
	}  
	chdir("..");
	file_put_contents("export/trioconcours".$etab.".csv",$xls);
}

Creer_CSV("epannes");
Creer_CSV("beauvoir");
Creer_CSV("frontenay");
Creer_CSV("saintsym");
Creer_CSV("rochenard");
Creer_CSV("sontpasdici");
Creer_CSV("prisse");

echo $phrase;
 ?>