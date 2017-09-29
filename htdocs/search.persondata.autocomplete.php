<?php
/* search.persondata.autocomplete.php, suchen von attributen eines einzelnen benutzereintrages und aufbereiten 
 * der daten fuer die gui
 */

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
$search_dn=filter_input(INPUT_POST,'dn', FILTER_CALLBACK, array("options"=>"filterStandard"));

// verbinden
$con1 = new Connection();
$con1-> connect();

// suche inizieren
$search1 = new Search($con1->con);
$answer = $search1->searchPersonData($search_dn);

// ou0 - ou2 aus dem dn extrahieren für select-anzeige
// dn aus answer OU= loeschen und dann aufteilen ohne letzte 4 teile
$ous = explode(',', str_replace('OU=', '',$answer['dn']), -4);
// element 0 mit cn= loeschen
array_shift($ous);
// solange noch element vorhanden, answer['ou'] befuellen
$e= 0;
for($i= count($ous); 0 < $i; $i--){   
    $answer['ou'][$e] = $ous[$i-1];
    $e++;
}


$jsonsend = json_encode($answer);
echo $jsonsend;

// verbindung beenden
$con1-> disConnect();
?>
