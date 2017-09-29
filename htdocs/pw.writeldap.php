<?php
/* pw.writeldap.php, schreiben von passwoertern ins ldap */

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

// daten aus gui
$new_pw_ui = filter_input(INPUT_POST,'pw');
$new_pw_dn = filter_input(INPUT_POST,'dn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_pw_uid = filter_input(INPUT_POST,'uid', FILTER_CALLBACK, array("options"=>"filterStandard"));

// mit ldap verbinden
$con1 = new Connection();
$con1-> connect();


$new_pw_cache = '';
$new_pw = $new_pw_ui;
$new_pw = "\"" . $new_pw . "\"";
$len = strlen($new_pw);
for ($i = 0; $i < $len; $i++){
    $new_pw_cache .= "{$new_pw{$i}}\000";
}

$userdata["unicodepwd"] = $new_pw_cache;

if(ldap_mod_replace($con1->con, $new_pw_dn, $userdata)){
    $_SESSION["guimessage"] = "Passwort erfolgreich gespeichert";
    $answer['success'] = true;
}else{
    $_SESSION["guimessage"] = "Fehler beim speichern des Passwortes";
    $answer['success'] = false;
}


// verbindung beenden
$con1-> disConnect();

$jsonsend = json_encode($answer);
echo $jsonsend;
?>
