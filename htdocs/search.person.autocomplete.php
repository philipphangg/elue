<?php
/* search.person.autocomplete.php, suchen von benutzereintrages anhand namen */

session_start();
  
include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}
// zu suchenden string auslesen
$search_cn=filter_input(INPUT_POST,'search', FILTER_CALLBACK, array("options"=>"filterStandard"));

// verbinden
$con1 = new Connection();
$con1->connect();

// suche inizieren
$search1 = new Search($con1->con);

$jsonsend = json_encode($search1->searchPerson($search_cn));

echo $jsonsend;

// verbindung beenden
$con1->disConnect();
?>
