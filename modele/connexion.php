<?php
try{
	$myPDO = new PDO('mysql:host=localhost;dbname=biblio_v2', 'root', '');
	$myPDO->exec('SET NAMES "UTF8"');
}
catch(PDOException $erreur)
{
	echo 'erreur:'.$erreur->getMessage();
}
?>