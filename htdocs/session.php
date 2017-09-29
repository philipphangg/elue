<?php
/* login und logout */

session_start();

include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

$login = filter_input(INPUT_POST,'login', FILTER_CALLBACK, array("options"=>"filterStandard"));
$logout = filter_input(INPUT_POST,'logout', FILTER_CALLBACK, array("options"=>"filterStandard"));

// wenn login-vorgang //////////////////////////////////////////////////////////
if($login){
    // benutzernamen und passwort auslesen
    $loginuser = filter_input(INPUT_POST,'loginuser', FILTER_CALLBACK, array("options"=>"filterStandard"));
    $loginpassword = filter_input(INPUT_POST,'loginpassword', FILTER_CALLBACK, array("options"=>"filterStandard"));
    
    // login ueberpruefen
    $con1 = new Connection();
    $con2 = new Connection();
    $con2->connect();
    $search1 = new Search($con2->con);
    $loginuid = $loginuser;
    $loginuser = $loginuser .'@'. 'foo.bar.org';
    
    // wenn login stimmt und in gruppe it
    if($con1->checkUser($loginuser, $loginpassword) && $search1->uidInGroup($loginuid)){
        $answer["logedin"] = true;
        // session_id aendern anti session fixation
        session_regenerate_id();
        // session_variable zur kontrolle ob eingeloggt
        $_SESSION["userlogedin"]= true;        
    }else{
        $answer["logedin"] = false;
    }
       
    $con1-> disConnect();
    $con2-> disConnect();
} // end if login

// wenn logout-vorgang $_SESSION variable löschen und cookie zerstören /////////
if($logout){
    $_SESSION = array();
    session_destroy();
    setcookie( "PHPSESSID", "", time()-3600, "/" );
    $answer["logedout"] = true;
}

$jsonsend = json_encode($answer);
echo $jsonsend;
?>
