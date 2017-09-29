<?php
/* delete.writeldap.php, loeschen eines gesamten eintrages aus dem ldap */

session_start();
  
include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');
include('../classes/class.2ndldap.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}

/* in das ldap zu schreibende daten vorbereiten */
$delete_dn = filter_input(INPUT_POST,'dn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$delete_uid = filter_input(INPUT_POST,'uid', FILTER_CALLBACK, array("options"=>"filterStandard"));

$answer = [];

// mit ldap verbinden und neue suche 
$con1 = new Connection();
$con1-> connect();

// alten eintrag loeschen
if(ldap_delete($con1->con, $delete_dn )){
    $answer['success'] = true;
    $_SESSION["guimessage"] = "Benutzereintrag gelöscht";
}else{
    $answer['success'] = false;
    $_SESSION["guimessage"] = "Fehler bei Löschung des Benutzereintrages";
}
    
// verbindung beenden
$con1-> disConnect();

$jsonsend = json_encode($answer);
echo $jsonsend;
?>

