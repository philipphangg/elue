<?php
/* guimessage.php, ausgeben aller informationen und fehlermeldungen an die gui */

session_start(); 

include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}

// auslesen des post
$messagerequest = filter_input(INPUT_POST,'messagerequest', FILTER_CALLBACK, array("options"=>"filterStandard"));

if(isset($_SESSION["guimessage"])){
    $answer["guimessage"] = $_SESSION["guimessage"];
}else{
    $answer["guimessage"] = '-----';
}    

// leeren nach ausgabe
$_SESSION["guimessage"] = '';

$jsonsend = json_encode($answer);
echo $jsonsend;
?>