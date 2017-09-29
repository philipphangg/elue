<?php
/* ous.php, suchen und ausgeben der organisationseinheiten des ldap */

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
$searchedou=filter_input(INPUT_POST,'searchedou', FILTER_CALLBACK, array("options"=>"filterStandard"));

$con1 = new Connection();
$con1->connect();
$search1 = new Search($con1->con);

// erste ou suchen ?
if($searchedou == '0'){    
    $answer = $search1->listMainOu();
}

// zweite ou suchen ?
if($searchedou == '1'){
    $mainou = "OU=" . filter_input(INPUT_POST,'ou0', FILTER_CALLBACK, array("options"=>"filterStandard"));    
    $answer = $search1->listSubOu($mainou);
}    

// dritte ou suchen ?
if($searchedou == '2'){
    $mainou = "OU=" . filter_input(INPUT_POST,'ou1'). ", OU=" . filter_input(INPUT_POST,'ou0', FILTER_CALLBACK, array("options"=>"filterStandard"));    
    $answer = $search1->listSubOu($mainou);
}   

// verbindung beenden
$con1->disConnect();

$jsonsend = json_encode($answer);
echo $jsonsend;
?>