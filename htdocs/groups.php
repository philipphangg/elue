<?php
/* groups.php, suchen und auflisten der gruppen des ldap */

session_start();
  
include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}

$search = filter_input(INPUT_POST,'search', FILTER_CALLBACK, array("options"=>"filterStandard"));

// neue verbindung
$con1 = new Connection();
$con1->connect();

// suche nach gruppen inizieren
$search1 = new Search($con1->con);
$answer = $search1->listGroups($search);

// verbindung beenden
$con1->disConnect();

// antwort in json umwandeln und senden
$jsonsend = json_encode($answer);
echo $jsonsend;
?>